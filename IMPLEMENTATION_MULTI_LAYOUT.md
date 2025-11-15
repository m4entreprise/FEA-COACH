# Implémentation du système multi-layout - Résumé

## ✅ Modifications effectuées

### 1. Configuration
- **Fichier créé** : `config/coach_site.php`
  - Définit 3 layouts : `classic`, `minimal`, `bold`
  - Configure le layout par défaut : `classic`
  - Centralise la liste des layouts disponibles

### 2. Base de données
- **Migration créée** : `database/migrations/2024_11_15_221700_add_site_layout_to_coaches_table.php`
  - Ajoute colonne `site_layout` (string, default='classic') dans la table `coaches`
  - Positionnée après la colonne `subdomain`

### 3. Modèle Coach
- **Fichier modifié** : `app/Models/Coach.php`
  - Ajout de `site_layout` dans `$fillable`
  - Ajout de l'accessor `getSiteLayoutOrDefaultAttribute()` pour gérer les fallbacks

### 4. Structure des vues
- **Dossier créé** : `resources/views/coach-site/layouts/`
  - `classic.blade.php` : Layout actuel (copié depuis index.blade.php)
  - `minimal.blade.php` : Layout minimaliste (placeholder pour développement futur)
  - `bold.blade.php` : Layout impact (placeholder pour développement futur)
- **Fichier modifié** : `resources/views/coach-site/index.blade.php`
  - Simplifié pour déléguer au layout classic (compatibilité)

### 5. Contrôleurs

#### CoachSiteController
- **Fichier modifié** : `app/Http/Controllers/CoachSiteController.php`
  - Logique de sélection dynamique du layout dans la méthode `show()`
  - Fallback robuste sur `classic` si layout invalide
  - Lecture de la configuration `coach_site.php`

#### BrandingController
- **Fichier modifié** : `app/Http/Controllers/Dashboard/BrandingController.php`
  - Import de `Illuminate\Validation\Rule`
  - Méthode `edit()` : passage de `availableLayouts` et `defaultLayout` à Inertia
  - Méthode `update()` : validation du champ `site_layout`

### 6. Interface Dashboard
- **Fichier modifié** : `resources/js/Pages/Dashboard/Branding.vue`
  - Ajout des props : `availableLayouts`, `defaultLayout`
  - Ajout de `site_layout` dans le formulaire
  - Nouvelle section UI avec grille de sélection des layouts
  - Indicateur visuel pour le layout sélectionné

### 7. Seeders
- **Fichier modifié** : `database/seeders/CoachSeeder.php`
  - Pierre Martin : `site_layout` => `'classic'`
  - Sophie Dubois : `site_layout` => `'minimal'`
  - Thomas Leroy : `site_layout` => `'bold'`

## 📋 Prochaines étapes

### Étapes obligatoires avant utilisation

1. **Exécuter la migration**
   ```bash
   php artisan migrate
   ```

2. **Mettre à jour les coaches existants (optionnel)**
   Si vous avez des coaches en base qui n'ont pas le champ `site_layout`, la valeur par défaut `'classic'` sera utilisée automatiquement.

3. **Compiler les assets frontend**
   ```bash
   npm run build
   # ou pour le développement
   npm run dev
   ```

### Développement futur

1. **Implémenter les layouts `minimal` et `bold`**
   - Actuellement, seuls des placeholders existent
   - Créer les designs complets dans :
     - `resources/views/coach-site/layouts/minimal.blade.php`
     - `resources/views/coach-site/layouts/bold.blade.php`
   - Respecter le même contrat de données : `$coach`, `$plans`, `$transformations`, `$faqs`

2. **Ajouter des images de prévisualisation**
   - Créer les images dans `public/images/layouts/`
     - `classic.png`
     - `minimal.png`
     - `bold.png`
   - Améliorer l'UI du sélecteur dans `Dashboard/Branding.vue`

3. **Tests automatisés**
   - Tester le fallback quand `site_layout` est invalide
   - Tester la sélection de chaque layout
   - Tester la validation dans `BrandingController`

4. **Intégration dans le Setup Wizard (optionnel)**
   - Ajouter une étape de sélection du layout dans le wizard de configuration initiale

## 🔧 Fonctionnement technique

### Flux de sélection du layout

1. Le coach sélectionne un layout dans `Dashboard > Branding`
2. La valeur `site_layout` est enregistrée dans la table `coaches`
3. Lors de l'affichage du site public :
   - `CoachSiteController@show` lit `coach->site_layout`
   - Utilise l'accessor `site_layout_or_default` pour gérer les fallbacks
   - Vérifie que le layout existe dans `config/coach_site.php`
   - Charge la vue correspondante (ex: `coach-site.layouts.classic`)
4. Toutes les vues reçoivent le même contrat de données

### Contrat de données unifié

Toutes les vues de layout reçoivent :
- `$coach` : Instance du modèle Coach avec relations chargées
- `$plans` : Collection des plans actifs
- `$transformations` : Collection des transformations
- `$faqs` : Collection des FAQs actives

### Sécurité et robustesse

- ✅ Validation stricte dans `BrandingController` (seules les clés définies dans config sont acceptées)
- ✅ Fallback automatique sur `classic` si layout invalide ou supprimé
- ✅ Accessor dans le modèle pour centraliser la logique de fallback
- ✅ Default value dans la migration pour les coaches existants

## 📁 Fichiers modifiés/créés

### Nouveaux fichiers
- `config/coach_site.php`
- `database/migrations/2024_11_15_221700_add_site_layout_to_coaches_table.php`
- `resources/views/coach-site/layouts/classic.blade.php`
- `resources/views/coach-site/layouts/minimal.blade.php`
- `resources/views/coach-site/layouts/bold.blade.php`

### Fichiers modifiés
- `app/Models/Coach.php`
- `app/Http/Controllers/CoachSiteController.php`
- `app/Http/Controllers/Dashboard/BrandingController.php`
- `resources/js/Pages/Dashboard/Branding.vue`
- `resources/views/coach-site/index.blade.php`
- `database/seeders/CoachSeeder.php`

## ✨ Avantages de l'implémentation

1. **Maintenance facile** : Un seul contrat de données pour tous les layouts
2. **Extensible** : Ajouter un nouveau layout = 1 entrée dans config + 1 vue Blade
3. **Robuste** : Multiples niveaux de fallback pour éviter les erreurs
4. **Centralisé** : Configuration unique dans `config/coach_site.php`
5. **Pas de duplication** : La logique métier reste dans les contrôleurs et modèles
6. **Rétrocompatible** : Les coaches existants utilisent automatiquement le layout `classic`
