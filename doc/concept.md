### 1. Vue d’ensemble de la stack

* **Backend** : Laravel 11, PHP 8.2/8.3
* **Frontend public** : Blade + TailwindCSS (+ Alpine.js léger)
* **Dashboard coach** : Laravel + Inertia.js + Vue 3
* **Pattern** : multi-tenant “single database” (tous les coachs dans la même BDD, filtrés par `coach_id`)
* **Infra** : VPS (Forge) + Nginx + Redis + stockage type S3 + backups automatiques

Public = pages des coachs (SEO, rapide, simple)
Dashboard = mini CMS pour gérer textes, images, couleurs, logo.

---

### 2. Infra & hébergement (Forge / VPS)

**Serveur :**

* OS : Ubuntu LTS
* Web server : Nginx
* PHP-FPM : 8.2 ou 8.3
* Base de données : MySQL 8 ou MariaDB
* Cache & file d’attente : Redis
* Stockage fichiers : S3 ou compatible (Bunny, Wasabi…) via `FILESYSTEM_DISK=s3`
* Queue : `redis` + Supervisor (configuré dans Forge)
* Cron : `php artisan schedule:run` chaque minute

**DNS & SSL :**

* `A` ou `CNAME` wildcard : `*.website.com` → IP du VPS
* Certificat SSL (Let’s Encrypt) sur le domaine racine + wildcard si possible
* (Facultatif) Cloudflare pour DNS + cache + protection

**Mauvaise idée à éviter :**
Créer un VPS par coach ou un vhost Nginx par coach → ingérable.
👉 Un seul VPS, un seul vhost avec wildcard + routage Laravel par sous-domaine.

---

### 3. Backend Laravel – packages recommandés

**Base :**

* `laravel/laravel` 11.x
* `laravel/sanctum` (si besoin d’API plus tard)
* `laravel/breeze` (auth de base, version Inertia + Vue)

**Multi-tenancy :**

* Option 1 : package dédié `stancl/tenancy` (mode single-database)
* Option 2 : maison, si tu veux rester léger :

  * Middleware `ResolveCoachFromHost`
  * Tout le contenu a une colonne `coach_id`

Pour ton cas (simple, single DB), l’option maison est déjà suffisante.

**Médias & fichiers :**

* `spatie/laravel-medialibrary` (gestion images, conversions, responsive)

  * Logo, photo de profil, hero image, avant/après

**Autres utilitaires utiles :**

* `spatie/laravel-activitylog` (log des modifs dans le dashboard)
* `spatie/laravel-backup` (sauvegardes automatiques BDD + storage)
* `barryvdh/laravel-debugbar` (dev uniquement)

**Mauvaise idée à éviter :**
Multiplier les packages “gros” type builder / page builder → alourdit la stack, augmente les risques de conflits.
👉 Tu contrôles la structure, eux ne touchent qu’à quelques champs + images.

---

### 4. Frontend public – Blade + Tailwind

**Objectif** : sites rapides, propres, identiques en structure, personnalisables en contenu.

**Stack :**

* TailwindCSS (installé via Vite)
* Blade components pour chaque section :

  * `<x-hero />`
  * `<x-about />`
  * `<x-method />`
  * `<x-plans />`
  * `<x-transformations />`
  * `<x-faq />`
* Alpine.js pour un peu d’interactivité (FAQ, modales, carrousel simple)

**Théming (couleurs) :**

* Variables CSS / classes Tailwind calculées à partir de `color_primary` / `color_secondary` du coach.
* Exemple :

  ```php
  <!-- layout.blade.php -->
  <body class="bg-slate-950" style="--primary: {{ $coach->color_primary }}; --secondary: {{ $coach->color_secondary }};">
  ```

  et dans Tailwind, classes utilitaires type `[background-color:var(--primary)]`.

**Mauvaise idée à éviter :**
Autoriser les coachs à changer la structure des sections ou ajouter du HTML custom.
👉 Tu gardes la structure fixe, ils changent seulement texte + images + couleurs.

---

### 5. Dashboard coach – Inertia + Vue 3

**Pourquoi Inertia côté dashboard :**

* UX plus moderne (sans rechargement)
* Cohérent avec Laravel (Breeze Inertia)
* Idéal pour formulaires, uploads, prévisualisations

**Stack :**

* Inertia.js (côté Laravel + côté Vue)
* Vue 3 + script setup
* Vite pour le bundling
* Quelques composants spécifiques :

  * `TextEditor` (simple textarea + compteur de caractères)
  * `ImageUploader` (upload + preview)
  * `ColorPicker` (picker JS simple)
  * `TransformationsManager` (liste d’images avant/après)

**Sections du dashboard :**

1. `/dashboard/branding`

   * Logo (upload)
   * Couleur principale / secondaire (color picker)
2. `/dashboard/content`

   * Hero title / sous-titre
   * À propos
   * Méthode
   * Texte du bouton CTA
3. `/dashboard/gallery`

   * Liste des avant/après (max N)
   * Upload / suppression
4. (Optionnel) `/dashboard/plans`

   * Nom, description, prix, lien de paiement

Le tout derrière middleware `auth` + vérification du `coach_id`.

---

### 6. Modélisation BDD (simplifiée)

**Table `coaches`**

* `id`
* `user_id` (si 1 user principal par coach)
* `name`
* `slug` (pour le sous-domaine : `slug.kineseducation.academy`)
* `subdomain` (optionnel si différent du slug)
* `color_primary`
* `color_secondary`
* `hero_title`
* `hero_subtitle`
* `about_text`
* `method_text`
* `cta_text`
* `is_active`
* timestamps

Les images (logo, hero, avant/après) sont gérées via Media Library (pivot en BDD, pas besoin de colonnes supplémentaires).

**Table `users`**

* Standard Laravel (Breeze)
* Colonne `role` (`admin`, `coach`)
* Colonne `coach_id` pour lier un utilisateur à un coach (si un coach = un user)

**Table `coach_transformations`**

* `id`
* `coach_id`
* `title` (optionnel)
* `description` (optionnel)
* médias attachés via Media Library (avant/après)
* `order`
* timestamps

**Optionnel : `plans`**

* `id`
* `coach_id`
* `name`
* `description`
* `price`
* `cta_url`
* `is_active`

---

### 7. Routage & middleware (multi-tenant)

**Routage public :**

```php
// routes/web.php
Route::domain('{coach_slug}.kineseducation.academy')
    ->middleware(['web', 'resolve.coach'])
    ->group(function () {
        Route::get('/', [CoachSiteController::class, 'show'])->name('coach.site');
    });
```

**Middleware `resolve.coach` :**

* Récupère `{coach_slug}` depuis le host.
* Cherche `Coach::where('slug', $coach_slug)->firstOrFail()`.
* Stocke le coach dans le container / dans la requête (ex : `app()->instance(Coach::class, $coach)`).
* Optionnel : rejette si `!$coach->is_active`.

**Routage dashboard (centralisé) :**

```php
// routes/web.php
Route::middleware(['auth', 'verified'])
    ->prefix('dashboard')
    ->group(function () {
        Route::get('/branding', [BrandingController::class, 'edit'])->name('dashboard.branding');
        Route::put('/branding', [BrandingController::class, 'update']);

        Route::get('/content', [ContentController::class, 'edit'])->name('dashboard.content');
        Route::put('/content', [ContentController::class, 'update']);

        Route::get('/gallery', [GalleryController::class, 'index'])->name('dashboard.gallery');
        Route::post('/gallery', [GalleryController::class, 'store']);
        Route::delete('/gallery/{id}', [GalleryController::class, 'destroy']);
    });
```

Chaque contrôleur :

* Récupère le coach via `auth()->user()->coach`
* Applique les validations et met à jour les champs.

**Mauvaise idée à éviter :**
Faire une app dashboard par sous-domaine coach (ex. `coach1.kine.../dashboard`).
👉 Garde un dashboard central (ex. `app.kineseducation.academy` ou `kineseducation.academy/dashboard`) multi-tenant.

---

### 8. DevOps & qualité

* **Déploiement** : Forge (Git → deploy script)

  * `php artisan migrate --force`
  * `php artisan config:cache`
  * `php artisan route:cache`
  * `php artisan view:cache`
* **Queues** :

  * Traitement des uploads lourds (optimisation d’image)
  * Envoi d’emails
* **Monitoring** :

  * Laravel Telescope en environnement de staging
  * Logs centralisés (papertrail, logtail, etc.) en prod
* **Backups** :

  * `spatie/laravel-backup` → S3 / FTP externe
* **Tests** :

  * Tests de feature pour vérifier :

    * Résolution du tenant par sous-domaine
    * Isolation des données coach dans le dashboard

---

### 9. Résumé des mauvaises approches à éviter (et alternatives)

1. **Un site ou dépôt par coach**
   → Maintenance monstrueuse, aucune scalabilité.
   ✅ Alternative : un seul code, multi-tenant par `coach_id` + sous-domaine.

2. **Les laisser éditer du HTML brut / builder type “page builder”**
   → Design cassé, support énorme, performances en baisse.
   ✅ Alternative : champs encadrés (textes, images, couleurs), structure fixe.

3. **Multi-database par coach**
   → Complexité inutile pour ton volume, surtout au début.
   ✅ Alternative : une seule DB avec `coach_id` partout.