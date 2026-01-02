# 🚀 Déploiement du Générateur de Mentions Légales

## ✅ Fichiers créés/modifiés

### 1. Migrations de base de données
- ✅ `database/migrations/2026_01_02_190000_add_legal_entity_fields_to_users_table.php`
- ✅ `database/migrations/2026_01_02_190100_add_legal_settings_to_coaches_table.php`

### 2. Modèles
- ✅ `app/Models/User.php` - Nouveaux champs fillable
- ✅ `app/Models/Coach.php` - Nouveaux champs fillable et casts

### 3. Service Layer
- ✅ `app/DataTransferObjects/LegalData.php` - DTO pour les données légales
- ✅ `app/Services/LegalContentGenerator.php` - Service de génération
- ✅ `config/legal_templates.php` - Templates de textes légaux

### 4. Controllers
- ✅ `app/Http/Controllers/Dashboard/LegalController.php` - Entièrement refactorisé
- ✅ `app/Http/Controllers/CoachSiteController.php` - Méthode `legal()` mise à jour

### 5. Frontend
- ✅ `resources/js/Pages/Dashboard/LegalGenerator.vue` - Nouvelle interface moderne
- ✅ `resources/views/coach-site/legal.blade.php` - Affichage HTML sémantique

### 6. Routes
- ✅ `routes/web.php` - Route API ajoutée pour l'aperçu

---

## 📋 Instructions de déploiement

### Étape 1 : Exécuter les migrations

```bash
php artisan migrate
```

Cela ajoutera les nouveaux champs dans les tables `users` et `coaches`.

### Étape 2 : Compiler les assets

```bash
npm run build
# ou pour le dev
npm run dev
```

### Étape 3 : Vérifier la configuration

Assurez-vous que le fichier `config/legal_templates.php` est chargé :

```bash
php artisan config:cache
```

### Étape 4 : Tester l'interface

1. Connectez-vous en tant que coach
2. Accédez à **Dashboard > Mentions Légales**
3. Remplissez le formulaire
4. Cliquez sur "Générer l'aperçu"
5. Sauvegardez

### Étape 5 : Vérifier la page publique

Visitez : `https://[slug].unicoach.app/mentions-legales`

Le contenu généré devrait s'afficher avec un HTML sémantique propre.

---

## 🔧 Fonctionnalités implémentées

### ✨ Interface Dashboard

**3 sections de configuration :**

1. **Identité de l'entité**
   - Type (Personne Physique / Société)
   - Nom légal
   - N° BCE
   - Représentant légal (si société)
   - Adresse, TVA, téléphone

2. **Types de services**
   - ☑ Coaching en présentiel
   - ☑ Coaching en ligne
   - ☑ Produits numériques
   - ☑ Abonnements récurrents
   - ☑ Photos avant/après

3. **Règles métier**
   - Régime TVA (Assujetti / Franchise)
   - Délai d'annulation (heures)
   - Tribunal compétent
   - Assurance (optionnel)

**Fonctionnalités :**
- ✅ Aperçu en temps réel
- ✅ Génération automatique à la sauvegarde
- ✅ Réactivité : l'aperçu se met à jour automatiquement
- ✅ Validation des champs
- ✅ Design moderne avec Tailwind CSS

### 📄 Génération intelligente

**CGV (Conditions Générales de Vente) :**
- En-tête adapté (PP vs Société)
- Articles conditionnels selon les services
- Prix avec/sans TVA
- Droit de rétractation adapté
- Politique d'annulation
- Responsabilité (présentiel vs online)
- Propriété intellectuelle
- Droit à l'image (si activé)

**Politique de Confidentialité (RGPD) :**
- Collecte de données
- Données de santé (si coaching)
- Finalités
- Conservation
- Droits des utilisateurs

### 🌐 Page publique

- URL : `/mentions-legales`
- HTML sémantique (SEO-friendly)
- Génération à la volée ou cache
- Style conforme au branding du coach

---

## 🎨 Personnalisation

### Modifier les textes légaux

Éditez `config/legal_templates.php` pour ajuster les textes sans toucher au code.

```php
'cgv' => [
    'article_objet' => "Votre nouveau texte...",
    // ...
]
```

### Ajouter une langue

1. Dupliquer `config/legal_templates.php` → `config/legal_templates_nl.php`
2. Traduire les textes
3. Modifier le service `LegalContentGenerator` pour détecter la langue

---

## 🔍 Résolution de problèmes

### L'aperçu ne se génère pas

Vérifiez dans la console JavaScript :
```
Network → api/legal/generate-preview
```

Si erreur 500, vérifiez les logs Laravel.

### Les champs ne se sauvegardent pas

Vérifiez que les champs sont bien dans `$fillable` :
- `app/Models/User.php`
- `app/Models/Coach.php`

### La page publique affiche "bientôt disponible"

Assurez-vous que :
1. Le coach a sauvegardé ses informations
2. Le mode génération est AUTO (par défaut)
3. Les champs obligatoires sont remplis

---

## 📊 Structure de données

### Table `users`
```
- entity_type (ENUM: PP, SOC)
- legal_name
- company_number
- legal_representative (nullable)
- phone_contact (nullable)
```

### Table `coaches`
```
- is_coaching_presentiel (boolean)
- is_coaching_online (boolean)
- has_digital_products (boolean)
- has_subscriptions (boolean)
- use_client_photos (boolean)
- vat_regime (ENUM: ASSUJETTI, FRANCHISE)
- cancellation_delay (integer)
- tribunal_city
- insurance_company (nullable)
- insurance_policy_number (nullable)
- legal_generation_mode (ENUM: AUTO, MANUAL)
- legal_terms (longText) - Cache du HTML généré
```

---

## 🚦 Migration depuis l'ancien système

Les coachs qui ont déjà des mentions légales manuelles :

1. Leurs données dans `coaches.legal_terms` seront préservées
2. Le mode sera AUTO par défaut
3. S'ils modifient les settings, le HTML sera régénéré
4. Pour garder leur version personnalisée : implémenter un toggle "Mode manuel" (futur)

---

## 🎯 Prochaines étapes (optionnel)

### Phase 2 - Améliorations

1. **Toggle AUTO/MANUAL**
   - Permettre au coach de basculer en mode manuel
   - Afficher un warning avant régénération

2. **Historique des versions**
   - Sauvegarder chaque version générée
   - Permettre de revenir en arrière

3. **Export PDF**
   - Bouton "Télécharger en PDF"
   - Utiliser Laravel DomPDF ou Browsershot

4. **Validation juridique**
   - Faire valider les templates par un avocat
   - Ajouter une date de dernière révision des templates

5. **Multi-langue**
   - FR, NL, EN
   - Détection automatique selon le profil du coach

---

## ⚖️ Validation juridique

**Statut** : ✅ **EXCELLENT - Haute qualité juridique validée** (2 janvier 2026)

**Verdict du juriste :**
> "Excellent. Ton générateur produit un texte d'une très haute qualité juridique. Il intègre les mises à jour législatives les plus récentes en Belgique."

**6 corrections critiques appliquées** :

1. **Clause de compétence** : Distinction B2C (tribunal du consommateur) vs B2B (tribunal du coach)
2. **Recouvrement de dettes** : Ajout du délai légal de 14 jours après le rappel gratuit (Livre XIX CDE - Sept 2023)
3. **Force majeure** : Élargie au-delà du seul certificat médical (décès, panne, etc.)
4. **Droit de rétractation numérique** : Précision sur la nécessité d'une case à cocher au checkout (Art. VI.53)
5. **Responsabilité corporelle** : Mention explicite de l'exception en cas de faute lourde du coach
6. **Abonnements récurrents** : Article 3 bis ajouté sur la tacite reconduction et les modalités de résiliation

**Points forts validés :**
- ✅ Clause de réciprocité (protection contre nullité pour clause abusive)
- ✅ Gestion RGPD des données de santé (Article 9)
- ✅ Protection contre les "contrats prison"
- ✅ Conformité Règlement Bruxelles I bis

**Fichiers modifiés** : `config/legal_templates.php`, `app/Services/LegalContentGenerator.php`  
**Rapports** : `LEGAL_VALIDATION_REPORT.md`, `LEGAL_FINAL_VERDICT.md`

⚠️ **Action requise** : Si vous proposez des produits numériques, vous devez implémenter une case à cocher spécifique au moment de l'achat pour que la renonciation au droit de rétractation soit valable (Art. VI.53 CDE).

---

## 📝 Checklist de validation

Avant de passer en production :

- [ ] Migrations exécutées sans erreur
- [ ] Interface Dashboard accessible et fonctionnelle
- [ ] Aperçu en temps réel fonctionne
- [ ] Sauvegarde des données réussie
- [ ] Page publique affiche le contenu généré
- [ ] HTML sémantique validé (pas d'erreurs)
- [ ] Tests sur mobile/tablette
- [x] Textes validés par un juriste ✅ **FAIT**
- [x] Corrections juridiques appliquées ✅ **FAIT**
- [ ] Documentation utilisateur créée
- [ ] Backup de la BDD avant déploiement
- [ ] ⚠️ Si produits numériques : case à cocher checkout implémentée

---

## 🆘 Support

En cas de problème :

1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier la console JavaScript
3. Tester avec un nouveau coach (compte test)
4. Contacter l'équipe de développement

---

**Date de déploiement** : 2 janvier 2026  
**Version** : 1.0.0  
**Statut** : ✅ Prêt pour déploiement
