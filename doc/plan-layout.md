# Plan multi-layout pour les sites publics des coachs

## 1. Objectifs et contraintes

- **Objectif principal**
  - Permettre à chaque coach de choisir parmi plusieurs layouts pour son site public.
  - Tous les layouts doivent consommer **exactement les mêmes données** (même contrat de données), afin de garantir une **maintenance facile**.

- **Contraintes fortes**
  - Aucune duplication de logique métier : le contenu reste centralisé dans `Coach`, les contrôleurs existants et les formulaires du dashboard / wizard.
  - Zéro dépendance cachée : tous les layouts doivent fonctionner avec les mêmes variables `$coach`, `$plans`, `$transformations`, `$faqs`.
  - Fallback robuste : si un layout invalide est configuré (clé inconnue, code supprimé, etc.), le système doit **toujours** retomber sur un layout par défaut fonctionnel.
  - Pas de variation de contrat côté JS/CSS : tous les layouts s'appuient sur `resources/js/coach-site.js` et Tailwind via Vite, comme aujourd'hui.


## 2. Contexte actuel (résumé d’architecture)

### 2.1. Routing public coach

- `routes/web.php`
  - Domaine : `Route::domain('{coach_slug}.' . config('app.domain', 'localhost'))`
  - Middleware : `['web', 'resolve.coach']` (injecte l’instance de `Coach` dans le container, accessible via `app(Coach::class)`).
  - Routes :
    - `GET /` → `CoachSiteController@show` (page principale du site public)
    - `POST /contact` → `CoachSiteController@contact` (formulaire de contact)
    - `GET /mentions-legales` → `CoachSiteController@legal` (page légale dédiée)

### 2.2. Modèle `Coach`

- Fichier : `app/Models/Coach.php`
- Champs principaux (dans `$fillable`) déjà existants et utilisés par le site public :
  - Identité : `user_id`, `name`, `slug`, `subdomain`.
  - Branding : `color_primary`, `color_secondary`.
  - Contenu :
    - `hero_title`, `hero_subtitle`.
    - `about_text`.
    - `method_text`, `method_title`, `method_subtitle`.
    - `method_step1_title`, `method_step1_description`, `method_step2_*`, `method_step3_*`.
    - `pricing_title`, `pricing_subtitle`.
    - `transformations_title`, `transformations_subtitle`.
    - `final_cta_title`, `final_cta_subtitle`, `cta_text`, `intermediate_cta_title`, `intermediate_cta_subtitle`.
    - `legal_terms`.
  - Statistiques : `satisfaction_rate`, `average_rating`.
  - Réseaux sociaux : `facebook_url`, `instagram_url`, `twitter_url`, `linkedin_url`, `youtube_url`, `tiktok_url`.
  - Status : `is_active` (booléen).
- Relations utilisées :
  - `user()`
  - `transformations()` (-> `CoachTransformation`)
  - `plans()`
  - `faqs()`
  - `contactMessages()`
  - `clients()`
- Media library (Spatie) :
  - Collections : `logo`, `hero`, `profile`, avec des fallback URLs.

### 2.3. Site public actuel

- Contrôleur : `app/Http/Controllers/CoachSiteController.php`
  - `show(Request $request): View`
    - Récupère le `Coach` courant via `app(Coach::class)`.
    - Charge les relations `user`, `transformations`, `plans` (actifs), `faqs` (actives), via `loadMissing`.
    - Passe à la vue :
      - `coach`
      - `plans` (plans actifs)
      - `transformations`
      - `faqs`
    - Vue actuelle : `view('coach-site.index', [...])`.
  - `contact(Request $request)`
    - Persiste un message de contact lié au coach (`contactMessages`).
  - `legal(Request $request): View`
    - Vue : `view('coach-site.legal', ['coach' => $coach])`.

- Layout principal : `resources/views/layouts/coach-site.blade.php`
  - Structure HTML globale (`<!DOCTYPE html>`, `<head>`, `<body>`).
  - Chargement CSS/JS via `@vite(['resources/css/app.css', 'resources/js/coach-site.js'])`.
  - Définition des couleurs dynamiques CSS via variables `--color-primary`, `--color-secondary` à partir de `coach->color_primary` et `coach->color_secondary`.
  - Navigation, header, footer global du site public.
  - Slot de contenu principal via `@yield('content')`.

- Contenu principal : `resources/views/coach-site/index.blade.php`
  - `@extends('layouts.coach-site')`.
  - Sections :
    - Hero (`#accueil`), 
    - "À propos" (`#a-propos`),
    - "Ma méthode" (`#methode`),
    - CTA intermédiaire,
    - Tarifs (`#tarifs`),
    - Transformations (`#resultats`),
    - (plus bas : FAQ, contact, etc. dans la suite du fichier non recopiée ici).
  - Utilise intensivement les champs de `Coach` et les relations (`$plans`, `$transformations`, `$faqs`).

- Page légale : `resources/views/coach-site/legal.blade.php`
  - Page autonome avec son propre `<html>` et `<body>`.
  - Utilise `coach->color_primary`, `coach->color_secondary`, `coach->legal_terms` et `coach->getFirstMediaUrl('logo')`.

### 2.4. Édition de contenu côté dashboard / wizard

- Branding :
  - `Dashboard/Branding.vue` + `Dashboard\BrandingController`
    - Gère `color_primary`, `color_secondary`, `logo`, `hero`.

- Contenu :
  - `Dashboard/Content.vue` + `Dashboard\ContentController`
    - Gère tous les champs textuels du site public (hero, about, méthode, CTA, pricing, transformations, stats, réseaux sociaux, etc.).

- Setup Wizard :
  - `SetupWizardController` + `resources/js/Pages/Setup/Step*.vue`.
  - Étapes de configuration initiale (branding, contenu, sections avancées, etc.).
  - Note : certaines étapes utilisent des noms de champs `primary_color`/`secondary_color` qui ne correspondent pas aux colonnes `color_primary`/`color_secondary` (incohérence existante indépendante du multi-layout, à corriger séparément).


## 3. Design général multi-layout

### 3.1. Principe : Layout = simple choix de template

- Chaque coach peut choisir une **clé de layout** (ex. `classic`, `minimal`, `bold`).
- Cette clé est stockée **dans la table `coaches`**.
- Le contrôleur `CoachSiteController@show` utilise cette clé pour déterminer **quelle vue Blade** rendre.
- Toutes ces vues reçoivent **le même contrat de données** :
  - `coach` (instance `Coach` + relations chargées)
  - `plans` (collection de `Plan` actifs)
  - `transformations` (collection de `CoachTransformation`)
  - `faqs` (collection de `Faq` actives)

### 3.2. Liste centralisée des layouts disponibles

- Créer un fichier de config dédié : `config/coach_site.php`.
- Contenu prévu :

```php
return [
    'default_layout' => 'classic',

    'layouts' => [
        'classic' => [
            'label' => 'Classique',
            'description' => 'Layout actuel, équilibré et polyvalent.',
            'view' => 'coach-site.layouts.classic',
            'preview_image' => '/images/layouts/classic.png', // optionnel
        ],
        'minimal' => [
            'label' => 'Minimal',
            'description' => 'Layout épuré, très focalisé sur le texte et les CTA.',
            'view' => 'coach-site.layouts.minimal',
            'preview_image' => '/images/layouts/minimal.png',
        ],
        'bold' => [
            'label' => 'Impact',
            'description' => 'Layout très visuel avec de grosses sections hero.',
            'view' => 'coach-site.layouts.bold',
            'preview_image' => '/images/layouts/bold.png',
        ],
        // Layouts futurs à ajouter ici
    ],
];
```

- Avantages :
  - Une seule source de vérité pour les layouts disponibles.
  - Réutilisable côté backend **et** côté Inertia (dashboard) pour construire dynamiquement le sélecteur de layout.
  - Facile à étendre : ajouter un layout = ajouter une entrée dans cette config + créer la vue associée.

### 3.3. Contrat de données unique pour toutes les vues

- Toutes les vues listées dans `config('coach_site.layouts.*.view')` doivent accepter exactement les **quatre variables** suivantes :
  - `$coach` : instance de `App\Models\Coach` avec au minimum les attributs listés dans la section 2.2 et la relation `user` chargée (pour les mentions légales, TVA, etc.).
  - `$plans` : collection de `App\Models\Plan` **actifs** (filtres appliqués dans le contrôleur).
  - `$transformations` : collection de `App\Models\CoachTransformation`.
  - `$faqs` : collection de `App\Models\Faq` actives.

- Règle d'or :
  - **Aucune vue ne doit exiger un champ ou une variable supplémentaire** côté contrôleur.
  - Si un nouveau champ est nécessaire (ex. `testimonials_title`), il doit être :
    - Ajouté au modèle `Coach` + migrations + dashboard.
    - Passé dans `$coach` (et non comme variable flottante séparée).
    - Documenté dans la section "Contrat de données".


## 4. Modifications techniques détaillées

### 4.1. Base de données : ajout du champ `site_layout`

- Nouvelle migration : `database/migrations/xxxx_xx_xx_xxxxxx_add_site_layout_to_coaches_table.php`.
- Implémentation (principes) :

```php
Schema::table('coaches', function (Blueprint $table) {
    $table->string('site_layout')
        ->default('classic')
        ->after('subdomain');
});
```

- Caractéristiques :
  - Type : `string` (clé courte de layout).
  - Default : `'classic'` pour garantir un layout valide pour tous les coaches existants.
  - Pas besoin d'index spécifique, car la valeur est toujours lue pour un coach donné (pas pour filtrer).

- `down()` :

```php
Schema::table('coaches', function (Blueprint $table) {
    $table->dropColumn('site_layout');
});
```

- Seeders :
  - `database/seeders/CoachSeeder.php` : compléter les appels `Coach::create([...])` avec :

```php
'site_layout' => 'classic', // ou une autre valeur de test
```

### 4.2. Modèle `Coach`

- Fichier : `app/Models/Coach.php`.
- Ajouter dans `$fillable` :

```php
'site_layout',
```

- Optionnel : ajouter un accessor pour simplifier la gestion du fallback dans le code :

```php
public function getSiteLayoutOrDefaultAttribute(): string
{
    $key = $this->site_layout ?: config('coach_site.default_layout', 'classic');
    $layouts = config('coach_site.layouts', []);

    return array_key_exists($key, $layouts)
        ? $key
        : config('coach_site.default_layout', 'classic');
}
```

> Cet accessor n'est pas obligatoire, mais rend le code du contrôleur plus lisible.


### 4.3. Config `config/coach_site.php`

- Créer le fichier avec la structure décrite en 3.2.
- Points à respecter :
  - `default_layout` doit toujours correspondre à une clé existante dans `layouts`.
  - Chaque entrée `layouts[*]` doit au minimum contenir :
    - `label` (string, affiché dans le dashboard),
    - `description` (string courte),
    - `view` (chemin complet de la vue Blade),
  - `preview_image` est optionnel mais recommandé pour la future UI.


### 4.4. Contrôleur `CoachSiteController` : sélection de layout

- Fichier : `app/Http/Controllers/CoachSiteController.php`.

#### 4.4.1. Logique de sélection de vue

- Remplacer l'appel direct :

```php
return view('coach-site.index', [...]);
```

- Par une sélection basée sur la config :

```php
$config = config('coach_site');
$layouts = $config['layouts'] ?? [];
$defaultKey = $config['default_layout'] ?? 'classic';

// Récupérer la clé de layout du coach (avec accessor ou directement)
$layoutKey = method_exists($coach, 'getSiteLayoutOrDefaultAttribute')
    ? $coach->site_layout_or_default
    : ($coach->site_layout ?: $defaultKey);

if (! isset($layouts[$layoutKey])) {
    $layoutKey = $defaultKey;
}

$viewName = $layouts[$layoutKey]['view'] ?? 'coach-site.layouts.classic';

return view($viewName, [
    'coach' => $coach,
    'plans' => $activePlans,
    'transformations' => $transformations,
    'faqs' => $faqs,
]);
```

- Points de robustesse :
  - Si `site_layout` est `null` → fallback sur `default_layout`.
  - Si `site_layout` contient une clé non déclarée dans `config('coach_site.layouts')` → fallback sur `default_layout`.
  - Si `view` est manquant pour une clé (erreur de config) → fallback final sur `coach-site.layouts.classic`.

#### 4.4.2. Impact sur `contact` et `legal`

- `contact` : aucun changement fonctionnel nécessaire, la logique ne dépend pas du layout.
- `legal` : reste une page autonome pour l’instant.
  - Dans une itération ultérieure, on pourra :
    - Soit unifier la structure avec `layouts.coach-site`.
    - Soit proposer une variante multi-layout également pour la page légale.
  - Pour l’instant : **ne rien changer** pour ne pas introduire de risque inutile.


### 4.5. Structure des vues Blade multi-layout

#### 4.5.1. Organisation des fichiers

- Nouveau dossier de templates de page :

```
resources/views/coach-site/layouts/
    classic.blade.php
    minimal.blade.php
    bold.blade.php
    ...
```

- Stratégie :
  - Déplacer le contenu actuel de `coach-site/index.blade.php` dans `coach-site/layouts/classic.blade.php`.
  - Mettre à jour ce fichier pour qu'il :
    - `@extends('layouts.coach-site')` (comme aujourd'hui),
    - `@section('content')` contienne l'ensemble des sections.

- Pour `minimal.blade.php`, `bold.blade.php`, etc. :
  - Même structure (`@extends('layouts.coach-site')`, `@section('content')`), mais avec une mise en page différente.
  - Toujours basés sur le même contrat de données.

- Fichier `coach-site/index.blade.php` :
  - Option 1 (recommandée) : le laisser exister mais simplement déléguer au layout par défaut pour éviter de casser du code existant éventuel :

```blade
@extends('coach-site.layouts.classic')
```

  - Option 2 : le conserver comme simple wrapper pour compatibilité, tout en s’assurant que `CoachSiteController` ne dépend plus de ce chemin.

#### 4.5.2. Partials réutilisables (optionnel mais recommandé)

- Pour limiter la duplication de markup entre layouts, créer des partials :

```
resources/views/coach-site/partials/
    hero.blade.php
    about.blade.php
    method.blade.php
    pricing.blade.php
    transformations.blade.php
    faqs.blade.php
    contact.blade.php
```

- Chaque partial reçoit le même contrat de données global (accès à `$coach`, `$plans`, `$transformations`, `$faqs`).
- Chaque layout peut :
  - Réutiliser progressivement ces partials (`@include('coach-site.partials.hero')`),
  - Ou en définir de nouveaux s'il souhaite un découpage différent.

> Important : les partials ne changent **pas** le contrat de données global, ils n’en sont qu’une "projection".


### 4.6. Dashboard : sélecteur de layout (Branding)

#### 4.6.1. Backend – `BrandingController`

- Fichier : `app/Http/Controllers/Dashboard/BrandingController.php`.

1. **Edit** : enrichir les props Inertia

```php
public function edit(Request $request): Response
{
    $coach = $request->user()->coach;

    return Inertia::render('Dashboard/Branding', [
        'coach' => $coach->load('media'),
        'availableLayouts' => config('coach_site.layouts'),
        'defaultLayout' => config('coach_site.default_layout'),
    ]);
}
```

2. **Update** : valider et persister `site_layout`

- Ajouter `use Illuminate\Validation\Rule;` en haut du fichier.
- Ajouter la règle de validation :

```php
$validated = $request->validate([
    'color_primary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
    'color_secondary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
    'site_layout' => [
        'required',
        Rule::in(array_keys(config('coach_site.layouts'))),
    ],
]);
```

- Puis :

```php
$coach->update($validated);
```

> Attention : ne pas oublier d'ajouter `site_layout` dans `$fillable` du modèle `Coach`, sinon l’update ne sera pas pris en compte.

#### 4.6.2. Frontend – `Dashboard/Branding.vue`

- Fichier : `resources/js/Pages/Dashboard/Branding.vue`.

1. **Props**

Ajouter dans `defineProps` :

```js
const props = defineProps({
    coach: Object,
    availableLayouts: Object,
    defaultLayout: String,
});
```

2. **Formulaire**

Étendre l’état du formulaire :

```js
const form = useForm({
    color_primary: props.coach.color_primary || '#3B82F6',
    color_secondary: props.coach.color_secondary || '#10B981',
    logo: null,
    hero: null,
    site_layout: props.coach.site_layout || props.defaultLayout,
});
```

3. **UI du sélecteur de layout**

Ajouter une nouvelle section dans le template, par exemple après les couleurs :

- Grille de cartes pour chaque entrée de `availableLayouts` :
  - Afficher `label`, `description`, et éventuellement `preview_image`.
  - Sur clic, mettre à jour `form.site_layout`.
  - Visuellement, mettre en avant la carte sélectionnée (bordure, icône, etc.).

> La logique Vue est simple : itérer sur `Object.entries(props.availableLayouts)`.

4. **Soumission**

- Le `submit()` actuel poste déjà le formulaire `form` vers `dashboard.branding.update`.
- Comme `site_layout` est dans `form`, il sera automatiquement envoyé (formData).


### 4.7. Setup Wizard (optionnel)

Objectif : ne pas dupliquer la logique de sélection de layout.

- Étape 1 ou 5 peuvent éventuellement proposer une pré-sélection de layout pour le coach.
- Stratégie recommandée pour éviter les bugs :
  - **Étape 1** : focus sur slug + couleurs + logo.
  - **Étape 5** : présentation des layouts disponibles + CTA "Choisir ce layout".
  - Implémentation : 
    - Reprendre la même liste `config('coach_site.layouts')` dans `SetupWizardController`.
    - Exposer cette liste aux vues `Setup/Step5.vue` via Inertia.
    - Quand l’utilisateur choisit un layout, appeler une route qui effectue un `update` du coach uniquement sur `site_layout` (en respectant la même validation que dans `BrandingController`).
  - Dans tous les cas, le coach pourra toujours modifier son choix ensuite dans `Dashboard/Branding`.

> Pour la première version, il est possible de **ne pas** exposer le choix de layout dans le wizard et de limiter la sélection au dashboard. Cela réduit le risque et la duplication. Ce point peut être décidé fonctionnellement.


## 5. Contrat de données détaillé pour les layouts

### 5.1. Variable `$coach`

- Type : instance de `App\Models\Coach` avec les champs :
  - Identité : `name`, `slug`, `subdomain`.
  - Branding : `color_primary`, `color_secondary`.
  - Contenu hero : `hero_title`, `hero_subtitle`, `cta_text`.
  - Section "À propos" : `about_text`.
  - Section "Méthode" : `method_text`, `method_title`, `method_subtitle`, `method_step1_*`, `method_step2_*`, `method_step3_*`.
  - Sections CTA/pricing/transformations : `pricing_title`, `pricing_subtitle`, `transformations_title`, `transformations_subtitle`, `intermediate_cta_title`, `intermediate_cta_subtitle`, `final_cta_title`, `final_cta_subtitle`.
  - Statistiques : `satisfaction_rate`, `average_rating`.
  - Légal : `legal_terms`.
  - Réseaux sociaux : `facebook_url`, `instagram_url`, `twitter_url`, `linkedin_url`, `youtube_url`, `tiktok_url`.
  - Statut : `is_active`.
  - Layout : `site_layout`.
- Relations garanties :
  - `user` (chargée au moins pour accéder à `vat_number` dans les mentions légales).
- Media accessibles via Spatie :
  - `getFirstMediaUrl('logo')`, `getFirstMediaUrl('hero')`, `getFirstMediaUrl('profile')`.

### 5.2. Variable `$plans`

- Type : `Illuminate\Support\Collection` ou `Illuminate\Database\Eloquent\Collection` de `App\Models\Plan`.
- Filtre dans le contrôleur : `where('is_active', true)->orderBy('price')`.
- Champs typiques : `name`, `price`, `description`, `cta_url`, etc.
- Règle pour les layouts :
  - Toujours gérer le cas `count() === 0` (afficher un message "Les formules seront bientôt disponibles", etc.).

### 5.3. Variable `$transformations`

- Type : collection de `App\Models\CoachTransformation`.
- Ordre : par `order` (défini dans le modèle / contrôleur).
- Media : `before` et `after` via MediaLibrary.
- Règle pour les layouts :
  - Gérer le cas où l'une des images manque (`hasMedia('before')`/`hasMedia('after') == false`).

### 5.4. Variable `$faqs`

- Type : collection de `App\Models\Faq`.
- Filtre : `is_active = true`, ordonnée par `order`, `created_at`.
- Règle pour les layouts :
  - Gérer le cas où `faqs` est vide (afficher rien ou un placeholder discret, mais ne pas casser la page).


## 6. Gestion des cas limites et robustesse

1. **Coach sans layout défini** (colonne ajoutée après coup) :
   - `site_layout` est par défaut `'classic'` via la migration.
   - Si pour une raison quelconque la colonne est `NULL`, le contrôleur ou l'accessor utilise `default_layout`.

2. **Coach avec un layout obsolète** (clé supprimée de la config) :
   - Le contrôleur vérifie toujours `isset($layouts[$layoutKey])`.
   - Si la clé n'est pas trouvée → fallback `default_layout`.

3. **Erreur de configuration (view manquante)** :
   - Même si `view` est absent, on retombe sur un chemin de vue par défaut (`coach-site.layouts.classic`).

4. **Plans / transformations / FAQs vides** :
   - Chaque layout doit gérer explicitement ces cas (textes "bientôt disponibles", sections cachées, etc.).
   - La logique actuelle dans `coach-site/index.blade.php` peut servir de référence.

5. **Coach inactif (`is_active = false`)** :
   - Le comportement actuel n’empêche pas l’affichage du site.
   - Le multi-layout ne doit pas changer cette logique.
   - Une future évolution pourrait rediriger vers une page "Site en maintenance" mais cela sort du périmètre de ce plan.

6. **Compatibilité avec `legal.blade.php`** :
   - La page légale reste indépendante pour la première version.
   - Elle continue d'utiliser `color_primary` / `color_secondary` et `legal_terms`.
   - Aucune dépendance aux layouts multiples, donc pas de risque ici.


## 7. Plan d’implémentation étape par étape

1. **Préparation**
   - Créer `config/coach_site.php` avec au moins un layout `classic` pointant vers `coach-site.layouts.classic`.

2. **Migration**
   - Ajouter la colonne `site_layout` à la table `coaches` avec default `'classic'`.
   - Mettre à jour `CoachSeeder` pour renseigner `site_layout`.

3. **Modèle**
   - Ajouter `site_layout` à `$fillable` dans `Coach`.
   - Optionnel : ajouter l'accessor `site_layout_or_default`.

4. **Vues**
   - Créer `resources/views/coach-site/layouts/classic.blade.php` à partir du contenu actuel de `coach-site/index.blade.php`.
   - Adapter `coach-site/index.blade.php` pour déléguer à `classic` (ou le laisser non utilisé si plus référencé nulle part).

5. **Contrôleur**
   - Modifier `CoachSiteController@show` pour utiliser `config('coach_site')` et rendre la vue correspondant à `site_layout`.

6. **Dashboard Branding**
   - Modifier `BrandingController@edit` pour passer `availableLayouts` et `defaultLayout` à Inertia.
   - Modifier `BrandingController@update` pour valider et enregistrer `site_layout`.
   - Modifier `Dashboard/Branding.vue` pour :
     - accepter les nouvelles props,
     - ajouter `site_layout` dans le `useForm`,
     - afficher une UI de sélection de layout.

7. **Tests**
   - Vérifier manuellement :
     - Coach avec `site_layout = 'classic'` → chargement du layout classic.
     - Changement de layout dans le dashboard → affichage correct sur le domaine public.
     - Fallback quand `site_layout` est forcé à une valeur invalide.
   - Ajouter des tests automatisés (feature tests) pour `CoachSiteController@show` avec différentes valeurs de `site_layout`.

8. **Évolutions futures**
   - Ajout d’un nouvel item dans `config/coach_site.layouts` + création de la vue correspondante.
   - Éventuelle intégration du choix de layout dans le Setup Wizard (en réutilisant `config('coach_site.layouts')`).
   - Harmonisation éventuelle de la page `legal.blade.php` avec les layouts.


## 8. Résumé

- Le multi-layout repose sur un **champ `site_layout`** dans `coaches` et une **config centralisée** des layouts disponibles.
- `CoachSiteController` sélectionne la vue Blade en fonction de `site_layout`, avec un fallback robuste sur un layout par défaut.
- Tous les layouts consomment **le même contrat de données** (`coach`, `plans`, `transformations`, `faqs`), ce qui garantit une maintenance simple et une compatibilité maximale.
- Le sélecteur de layout est exposé dans le dashboard (page Branding) via Inertia, sans modifier le flux métier existant.
- Le plan prévoit les cas limites, la compatibilité ascendante et une stratégie d’extension future (nouveaux layouts, wizard, page légale).
