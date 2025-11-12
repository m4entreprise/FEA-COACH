# Module de Gestion de FAQ

## Vue d'ensemble

Un système complet de gestion de FAQ (Questions Fréquemment Posées) a été ajouté pour permettre aux coachs de créer, modifier et gérer les questions et réponses affichées sur leur site public.

## Fichiers créés

### Migration
- **`database/migrations/2025_11_12_181900_create_faqs_table.php`**
  - Table `faqs` avec les champs :
    - `id` : Identifiant unique
    - `coach_id` : Clé étrangère vers le coach
    - `question` : Texte de la question (max 500 caractères)
    - `answer` : Texte de la réponse (max 2000 caractères)
    - `order` : Ordre d'affichage (entier, défaut: 0)
    - `is_active` : Statut actif/inactif (booléen, défaut: true)
    - `timestamps` : created_at, updated_at

### Modèle
- **`app/Models/Faq.php`**
  - Fillable : `coach_id`, `question`, `answer`, `order`, `is_active`
  - Casts : `order` (integer), `is_active` (boolean)
  - Relation : `belongsTo(Coach::class)`

### Contrôleur
- **`app/Http/Controllers/Dashboard/FaqController.php`**
  - CRUD complet (Create, Read, Update, Delete)
  - Validation des données d'entrée
  - Vérification de sécurité (le coach ne peut gérer que ses propres FAQs)
  - Tri par ordre puis par date de création
  - Messages de succès après chaque action

### Vue
- **`resources/js/Pages/Dashboard/Faq.vue`**
  - Interface complète avec modal pour création/édition
  - Liste des questions avec statut et ordre
  - Badge de statut (Actif/Inactif)
  - Affichage de la question et de la réponse
  - Boutons d'action : Modifier et Supprimer
  - Confirmation avant suppression
  - État vide (empty state) quand aucune question
  - Responsive design

## Routes

### Routes dashboard (protégées par auth + verified)
```php
GET    /dashboard/faq              → index   : Liste des FAQs
POST   /dashboard/faq              → store   : Créer une FAQ
PATCH  /dashboard/faq/{faq}        → update  : Modifier une FAQ
DELETE /dashboard/faq/{faq}        → destroy : Supprimer une FAQ
```

### Noms de routes
- `dashboard.faq` : Page principale
- `dashboard.faq.store` : Création
- `dashboard.faq.update` : Modification
- `dashboard.faq.destroy` : Suppression

## Navigation

Le lien "FAQ" a été ajouté dans :
- **Menu desktop** : Dans `AuthenticatedLayout.vue`, barre de navigation principale
- **Menu mobile** : Dans la navigation responsive

## Fonctionnalités

### Création d'une question
1. Bouton "Nouvelle Question"
2. Modal avec formulaire :
   - Question (obligatoire, max 500 caractères)
   - Réponse (obligatoire, max 2000 caractères)
   - Ordre d'affichage (optionnel, défaut: 0)
   - Statut actif/inactif (checkbox)
3. Validation côté serveur
4. Message de succès

### Modification d'une question
1. Clic sur "Modifier" sur une question
2. Modal pré-remplie avec les données existantes
3. Modification possible de tous les champs
4. Validation et mise à jour
5. Message de succès

### Suppression d'une question
1. Clic sur "Supprimer"
2. Confirmation avec le texte de la question
3. Suppression définitive
4. Message de succès

### Ordre d'affichage
- Les questions sont triées par le champ `order` (croissant)
- Puis par date de création
- Permet de contrôler précisément l'ordre d'apparition sur le site public
- 0 = en premier, valeurs plus grandes = plus tard

### Statut actif/inactif
- `is_active = true` : Question visible sur le site public
- `is_active = false` : Question cachée (brouillon ou désactivée temporairement)
- Badge visuel dans l'interface

## Relation avec le modèle Coach

La relation `faqs()` a été ajoutée au modèle `Coach` :
```php
public function faqs(): HasMany
{
    return $this->hasMany(Faq::class);
}
```

## Sécurité

- **Authentification** : Routes protégées par middleware `auth` et `verified`
- **Authorization** : Vérification que le coach ne peut modifier/supprimer que ses propres FAQs
- **Validation** : Validation stricte des données (longueurs maximales, types)
- **Protection CSRF** : Automatique via Laravel
- **Cascade delete** : Suppression automatique des FAQs si le coach est supprimé

## Interface utilisateur

### Design
- Cartes blanches avec ombre légère
- Badges de statut colorés (vert pour actif, gris pour inactif)
- Boutons d'action avec codes couleur (bleu = modifier, rouge = supprimer)
- Modal modale pour création/édition
- Empty state élégant quand aucune question
- Responsive sur tous les écrans
- Support du dark mode

### UX
- Messages flash de succès
- Confirmation avant suppression
- Formulaire clair avec labels et placeholders
- Gestion des erreurs de validation
- Préservation du scroll lors des actions
- Fermeture du modal après succès

## Utilisation

### Accès
URL : `http://localhost:8000/dashboard/faq`

### Workflow typique
1. Connectez-vous avec un compte coach
2. Cliquez sur "FAQ" dans le menu
3. Créez vos questions fréquentes avec "Nouvelle Question"
4. Organisez l'ordre d'affichage avec le champ "ordre"
5. Activez/désactivez selon vos besoins
6. Les FAQs actives s'affichent automatiquement sur le site public

## Intégration future

Pour afficher les FAQs sur le site public du coach :

1. **Charger les FAQs** dans `CoachSiteController` :
```php
$faqs = $coach->faqs()
    ->where('is_active', true)
    ->orderBy('order')
    ->orderBy('created_at')
    ->get();
```

2. **Passer les données** à la vue :
```php
return Inertia::render('CoachSite', [
    'coach' => $coach,
    'faqs' => $faqs,
    // ...
]);
```

3. **Afficher dans le template** avec un composant accordéon ou liste déroulante

## Notes techniques

- **Validation max** : Question 500 chars, Réponse 2000 chars
- **Type de champ** : `text` pour answer (permet stockage long texte)
- **Whitespace** : La réponse préserve les retours à ligne (`whitespace-pre-line`)
- **Ordre** : Integer positif ou zéro, pas de valeurs négatives
- **Soft delete** : Non implémenté (suppression définitive)

## Améliorations possibles

- [ ] Drag & drop pour réorganiser l'ordre des questions
- [ ] Soft delete avec corbeille
- [ ] Catégories de questions
- [ ] Recherche dans les FAQs
- [ ] Export/Import des FAQs
- [ ] Templates de questions prédéfinies
- [ ] Statistiques : questions les plus consultées sur le site public
- [ ] Traductions multilingues
