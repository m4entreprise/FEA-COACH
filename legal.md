# Plan d'Implémentation : Générateur Dynamique de Mentions Légales

## 📋 Vue d'ensemble

Transformer le module "Mentions Légales" du dashboard coach d'un **éditeur de texte libre** en un **générateur intelligent** qui produit automatiquement des CGV et une politique de confidentialité conformes à la législation belge, basées sur les choix du coach.

---

## 🎯 Objectifs

1. **Collecte structurée** : Remplacer le textarea par un formulaire guidé
2. **Génération dynamique** : Créer le HTML sémantique à la volée selon les inputs
3. **Conformité légale** : Garantir l'exactitude des textes selon le type d'activité
4. **SEO-friendly** : Page publique indexable avec contenu HTML structuré
5. **Maintenance simplifiée** : Mise à jour automatique si le coach modifie ses settings

---

## 🗄️ Architecture Base de Données

### 1. Extension du modèle `users`

**Migration** : `add_legal_entity_fields_to_users_table.php`

```
Nouveaux champs requis :
- entity_type : ENUM('PP', 'SOC') - Type d'entité
- legal_name : STRING - Nom complet (PP) ou Dénomination sociale (SOC)
- company_number : STRING - Numéro BCE (format: 0xxx.xxx.xxx)
- legal_representative : STRING (nullable) - Requis uniquement si SOC
- phone_contact : STRING (nullable) - Téléphone professionnel

Champs existants à réutiliser :
✓ vat_number (déjà présent)
✓ legal_address (déjà présent)
✓ email (email_contact)
✓ first_name + last_name (pour PP)
```

### 2. Extension du modèle `coaches`

**Migration** : `add_legal_settings_to_coaches_table.php`

```
Typologie des services :
- is_coaching_presentiel : BOOLEAN (default: false)
- is_coaching_online : BOOLEAN (default: false)  
- has_digital_products : BOOLEAN (default: false)
- has_subscriptions : BOOLEAN (default: false)
- use_client_photos : BOOLEAN (default: false)

Règles métier :
- vat_regime : ENUM('ASSUJETTI', 'FRANCHISE') (default: 'ASSUJETTI')
- cancellation_delay : INTEGER (default: 24) - Heures
- tribunal_city : STRING (default: 'Bruxelles')
- insurance_company : STRING (nullable)
- insurance_policy_number : STRING (nullable)

Champs à conserver :
✓ legal_terms (longText) - Pour stockage optionnel du HTML généré
```

### 3. Stratégie de stockage

**Option recommandée : HYBRIDE**

- **Calcul à la volée** par défaut (temps réel)
- **Cache optionnel** : Sauvegarder le HTML généré dans `coaches.legal_terms` uniquement si le coach active l'option "Verrouiller la version actuelle"
- **Regeneration automatique** : Bouton "Mettre à jour" si les settings ont changé

**Avantages** :
- ✅ Toujours synchronisé avec les données du coach
- ✅ Flexibilité pour personnaliser manuellement si besoin
- ✅ Performance acceptable (page peu visitée)

---

## 🏗️ Architecture Logicielle

### 1. Service Layer : `LegalContentGenerator`

**Localisation** : `app/Services/LegalContentGenerator.php`

```
Classe responsable de générer le contenu HTML :

Méthodes publiques :
- generate(Coach $coach) : string
  └─ Génère l'intégralité du HTML (CGV + Privacy)

Méthodes privées :
- generateCGV(array $data) : string
- generatePrivacyPolicy(array $data) : string
- renderBlock(string $blockName, array $data) : string
- shouldDisplayBlock(string $condition, array $flags) : bool
- interpolateVariables(string $template, array $data) : string
```

**Design Pattern** : **Template Method + Strategy**
- Templates de texte stockés dans `config/legal_templates.php`
- Logique conditionnelle centralisée
- Testable unitairement

### 2. Configuration : `config/legal_templates.php`

```php
Structure proposée :
[
    'cgv' => [
        'header_pp' => "Texte pour Personne Physique...",
        'header_soc' => "Texte pour Société...",
        'article_objet' => "Article 1 - Objet...",
        'article_sante' => "Article 2 - État de santé...",
        // ... tous les blocs
    ],
    'privacy' => [
        'header' => "Introduction RGPD...",
        'donnees_health' => "1. Données collectées...",
        // ... tous les blocs
    ],
    'conditions' => [
        'show_sante' => ['is_coaching_presentiel', 'is_coaching_online'],
        'show_digital' => ['has_digital_products'],
        // ... mapping des conditions
    ]
]
```

**Avantages** :
- Modification des textes sans toucher au code
- Gestion des traductions future (FR/NL/EN)
- Versioning des templates légaux

### 3. DTO (Data Transfer Object) : `LegalData`

**Localisation** : `app/DataTransferObjects/LegalData.php`

```php
Classe immuable qui prépare toutes les variables :

Propriétés :
- CoachEntity (type_entite, nom_commercial, nom_legal, etc.)
- ServiceFlags (is_presentiel, is_online, etc.)
- BusinessRules (delai_annulation, ville_tribunal, etc.)

Méthode statique :
- fromCoach(Coach $coach) : self
  └─ Construit l'objet depuis le modèle Coach + User
```

**Avantages** :
- Type-safe
- Auto-complétion IDE
- Validation centralisée

---

## 🎨 Interface Utilisateur (Vue.js)

### 1. Transformation de `Legal.vue` → `LegalGenerator.vue`

**Structure proposée** :

```
┌─────────────────────────────────────────────┐
│  📋 Informations Légales                    │
│  [Identité de l'entité]                     │
│  - Type : ○ Personne Physique ○ Société     │
│  - Nom commercial : [________]              │
│  - Nom légal : [________]                   │
│  - N° BCE : [0xxx.xxx.xxx]                  │
│  - N° TVA : [BE 0xxx.xxx.xxx]               │
│  - Adresse siège : [________]               │
│  - Email contact : [________]               │
│  - Téléphone : [________]                   │
│  [Si Société] Représentant légal : [____]   │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│  🏋️ Types de Services                       │
│  ☑ Coaching en présentiel                   │
│  ☑ Coaching en ligne (visio/app)            │
│  ☐ Produits numériques (PDF, vidéos)        │
│  ☐ Abonnements récurrents                   │
│  ☐ Utilisation de photos avant/après        │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│  ⚖️ Règles Métier                            │
│  - Régime TVA : ○ Assujetti ○ Franchise     │
│  - Délai annulation : [24] heures           │
│  - Tribunal compétent : [Bruxelles ▾]       │
│  - Assureur : [________] (optionnel)        │
│  - N° police : [________] (optionnel)       │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│  👁️ Aperçu en temps réel                    │
│  [HTML généré dynamiquement]                │
└─────────────────────────────────────────────┘

[Sauvegarder]  [Prévisualiser]  [Copier HTML]
```

**Composants Vue** :
- `LegalEntityForm.vue` : Formulaire d'identité
- `ServiceTypesSelector.vue` : Checkboxes des services
- `BusinessRulesForm.vue` : Règles métier
- `LegalPreview.vue` : Aperçu live du HTML généré

**Approche** : Composition API + `computed` pour régénérer l'aperçu à chaque changement

### 2. API Endpoint pour la génération

**Route** : `POST /api/legal/generate-preview`

```php
LegalController::generatePreview(Request $request)
{
    $validated = $request->validate([...]);
    
    // Créer un DTO temporaire sans sauvegarder
    $legalData = LegalData::fromArray($validated);
    
    // Générer le HTML
    $html = app(LegalContentGenerator::class)->generate($legalData);
    
    return response()->json(['html' => $html]);
}
```

**Avantages** :
- Aperçu en temps réel sans recharger la page
- Validation avant sauvegarde
- Découplage génération / persistance

---

## 🌐 Page Publique : Amélioration de `/mentions-legales`

### 1. Controller : `CoachSiteController::legal()`

**Logique actuelle** : Affiche `$coach->legal_terms` (texte brut)

**Nouvelle logique** :

```php
public function legal(Request $request): View
{
    $coach = app(Coach::class);
    $coach->load('user');
    
    // Option 1 : Utiliser le cache si disponible
    $html = $coach->legal_terms;
    
    // Option 2 : Générer à la volée si vide
    if (empty($html)) {
        $html = app(LegalContentGenerator::class)->generate($coach);
    }
    
    return view('coach-site.legal', [
        'coach' => $coach,
        'legalHtml' => $html, // Nouveau : HTML pré-généré
    ]);
}
```

### 2. Blade Template : `legal.blade.php`

**Modification** :

```blade
<!-- Avant (texte brut) -->
<div class="prose whitespace-pre-line">
    {{ $coach->legal_terms }}
</div>

<!-- Après (HTML sémantique) -->
<div class="legal-container prose prose-lg max-w-none">
    {!! $legalHtml !!}
</div>
```

**Sécurité** : HTML généré côté serveur = trusté (pas de XSS)

### 3. Structure HTML générée

```html
<div class="legal-container">
    <section id="cgv">
        <h1>Conditions Générales de Vente</h1>
        
        <article class="legal-block">
            <h2>Article 1 - Objet</h2>
            <p>...</p>
        </article>
        
        <!-- ... tous les articles CGV -->
    </section>
    
    <hr class="legal-separator my-12 border-gray-300" />
    
    <section id="privacy">
        <h1>Politique de Confidentialité</h1>
        
        <article class="legal-block">
            <h2>1. Données collectées</h2>
            <p>...</p>
        </article>
        
        <!-- ... tous les articles RGPD -->
    </section>
    
    <footer class="legal-footer mt-8 text-sm text-gray-500">
        <p>Dernière mise à jour : {{ Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </footer>
</div>
```

**SEO** :
- Balises sémantiques (`<article>`, `<section>`, `<h1>`, `<h2>`)
- Indexable par Google
- Structured data possible (Schema.org LegalDocument)

---

## 🔄 Workflow Utilisateur

### Scénario 1 : Premier setup

1. Coach accède à **Dashboard > Mentions Légales**
2. Formulaire vierge avec champs pré-remplis depuis `User` (email, adresse)
3. Coach sélectionne type d'entité + services + règles métier
4. **Aperçu live** se met à jour en temps réel
5. Coach clique "Sauvegarder"
   - Données sauvegardées dans `users` + `coaches`
   - HTML généré et sauvegardé dans `coaches.legal_terms` (optionnel)
6. Page publique `slug.unicoach.app/mentions-legales` affiche le contenu généré

### Scénario 2 : Modification ultérieure

1. Coach modifie son adresse dans **Profil**
2. Bannière d'alerte dans **Dashboard > Mentions Légales** :
   > ⚠️ Vos mentions légales ne sont pas à jour. [Mettre à jour]
3. Coach clique "Mettre à jour" → régénération automatique
4. Option : "Verrouiller cette version" pour empêcher les mises à jour auto

### Scénario 3 : Personnalisation avancée

1. Coach génère la version de base
2. Option : "Passer en mode édition libre"
3. Convertit le formulaire en éditeur WYSIWYG (TinyMCE/Tiptap)
4. Modifications manuelles = désactive la génération auto

---

## 🧪 Tests & Validation

### 1. Tests unitaires

**`LegalContentGeneratorTest.php`**

```php
- testGenerateForPersonnePhysique()
- testGenerateForSociete()
- testConditionalBlocksPresence()
  └─ Vérifie que "Article Santé" apparaît SI is_presentiel = true
- testTVARegimeFranchise()
- testInterpolationVariables()
  └─ Vérifie que {{nom_legal}} est bien remplacé
```

### 2. Tests fonctionnels

**`LegalPageTest.php`**

```php
- testPublicLegalPageDisplaysGeneratedContent()
- testDashboardFormSavesCorrectly()
- testPreviewEndpointReturnsHTML()
```

### 3. Validation légale

**⚠️ IMPORTANT** :
- Templates validés par un juriste spécialisé en droit belge
- Disclaimer visible : "Ces mentions sont fournies à titre indicatif"
- Recommandation de validation par un professionnel

---

## 📦 Livrables

### Phase 1 : Base de données (Semaine 1)
- [ ] Migration `add_legal_entity_fields_to_users_table`
- [ ] Migration `add_legal_settings_to_coaches_table`
- [ ] Mise à jour des modèles `User` + `Coach` (fillable, casts)
- [ ] Seeders de test

### Phase 2 : Service Layer (Semaine 2)
- [ ] `LegalData` DTO
- [ ] `LegalContentGenerator` Service
- [ ] `config/legal_templates.php`
- [ ] Tests unitaires

### Phase 3 : Interface Dashboard (Semaine 3)
- [ ] Composants Vue (`LegalEntityForm`, `ServiceTypesSelector`, etc.)
- [ ] `LegalGenerator.vue` (page principale)
- [ ] API endpoint `/api/legal/generate-preview`
- [ ] Mise à jour `LegalController`

### Phase 4 : Page Publique (Semaine 4)
- [ ] Mise à jour `CoachSiteController::legal()`
- [ ] Amélioration `legal.blade.php` (HTML sémantique)
- [ ] Styles Tailwind pour `.legal-container`
- [ ] Tests fonctionnels

### Phase 5 : Améliorations (Semaine 5)
- [ ] Système de détection de changements (bannière d'alerte)
- [ ] Mode "Verrouillage" pour personnalisation manuelle
- [ ] Export PDF des mentions légales
- [ ] Versioning (historique des modifications)

---

## 🚀 Points d'Attention

### 1. Compatibilité avec l'existant

**Migration en douceur** :
- Ne pas casser les mentions légales déjà saisies manuellement
- Ajouter un champ `coaches.legal_generation_mode` : ENUM('AUTO', 'MANUAL')
- Si `MANUAL`, afficher un warning : "Passer en mode automatique ?"

### 2. Performance

**Optimisations** :
- Cache de la génération (Laravel Cache : 1h)
- Invalidation du cache quand settings changent
- Queue job pour régénération asynchrone (optionnel)

### 3. Internationalisation

**Préparer le futur** :
- Templates multilingues dans `config/legal_templates.php`
- Détection de la langue du coach (`coaches.language`)
- Traductions FR/NL/EN

### 4. Conformité RGPD

**Transparence** :
- Ajouter dans le footer : "Généré automatiquement par Unicoach"
- Lien vers disclaimer : "Comment nous générons vos mentions légales"
- Possibilité de télécharger un PDF pour archivage

---

## 🔮 Évolutions Futures

### V2 : Wizard Guidé
- Remplacer le formulaire unique par un wizard 3 étapes
- Tooltips explicatifs (ex: "Le régime franchise s'applique si...")
- Validation contextuelle (ex: si SOC, représentant légal requis)

### V3 : Modules Complémentaires
- Génération des **CGU** (Conditions Générales d'Utilisation)
- **Politique de Cookies**
- **Charte de Déontologie** pour coachs certifiés

### V4 : Intelligence Artificielle
- Analyse du site du coach pour pré-remplir automatiquement
- Suggestions de textes basées sur le profil (ex: CrossFit = risques spécifiques)
- OCR pour extraire infos depuis documents officiels (extrait BCE, assurance)

---

## 📚 Références Techniques

### Documentation
- RGPD : https://www.autoriteprotectiondonnees.be/
- Code de Droit Économique (Livre XIX) : https://economie.fgov.be/
- Banque Carrefour des Entreprises : https://economie.fgov.be/fr/themes/entreprises/banque-carrefour-des

### Stack Technique
- Laravel 11.x : Service Container, DTOs, Config
- Vue 3 + Composition API : Réactivité formulaire
- Tailwind CSS : Styles `.legal-container`
- Spatie Laravel Data (optionnel) : Alternative aux DTOs manuels

---

## ✅ Checklist de Validation Finale

Avant déploiement en production :

- [ ] Tous les textes validés par un juriste
- [ ] Tests unitaires passent à 100%
- [ ] Interface responsive (mobile/tablet)
- [ ] Accessibilité WCAG 2.1 AA
- [ ] Page publique indexée par Google
- [ ] Disclaimer légal visible
- [ ] Documentation utilisateur (vidéo tuto)
- [ ] Migration des données existantes testée
- [ ] Rollback plan défini

---

**Date de rédaction** : 2 janvier 2026  
**Auteur** : Cascade AI  
**Version** : 1.0  
**Statut** : 📋 Proposition - En attente de validation
