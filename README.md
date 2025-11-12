# FEA-COACH

Plateforme SaaS multi-tenant pour coachs sportifs permettant à chaque coach d'avoir son propre site personnalisable via sous-domaine.

## 🎯 Concept

FEA-COACH est une solution permettant aux coachs sportifs de créer rapidement leur site web personnalisé accessible via un sous-domaine unique (ex: `coach-name.kineseducation.academy`). Chaque coach peut gérer son contenu, ses couleurs, ses images et ses tarifs via un dashboard simple et intuitif.

## 🏗️ Architecture

### Stack technique

- **Backend**: Laravel 11.31 (PHP 8.2)
- **Frontend public**: Blade + TailwindCSS + Alpine.js
- **Dashboard**: Inertia.js + Vue 3 (avec mode sombre)
- **Base de données**: MySQL/MariaDB (single database multi-tenant)
- **Médias**: Spatie Media Library + stockage S3
- **Auth**: Laravel Breeze + Sanctum

### Packages principaux

- `laravel/breeze` 2.3 - Authentification avec Inertia + Vue
- `spatie/laravel-medialibrary` 11.17 - Gestion des médias
- `spatie/laravel-activitylog` 4.10 - Logs d'activité
- `spatie/laravel-backup` 9.3 - Sauvegardes automatiques
- `inertiajs/inertia-laravel` - SPA-like avec Vue 3

## 📦 Installation

### Prérequis

- PHP 8.2 ou supérieur
- Composer
- Node.js & NPM
- Extension PHP EXIF activée

### Configuration

1. Cloner le repository
```bash
git clone <repo-url>
cd FEA-COACH
```

2. Installer les dépendances
```bash
composer install
npm install
```

3. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer la base de données dans `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fea_coach
DB_USERNAME=root
DB_PASSWORD=
```

5. Exécuter les migrations
```bash
php artisan migrate
```

6. Compiler les assets
```bash
npm run dev
```

## 🗂️ Structure de la base de données

### Tables principales

- **coaches**: Profils des coachs (slug, couleurs, contenus)
- **users**: Utilisateurs (avec role et coach_id)
- **coach_transformations**: Galerie avant/après
- **plans**: Forfaits et tarifs
- **media**: Gestion des fichiers (Spatie)
- **activity_log**: Logs d'activité (Spatie)

## 🎨 Fonctionnalités

### Pour les coachs

- ✅ Site personnalisé avec sous-domaine unique
- ✅ Personnalisation des couleurs (primaire/secondaire)
- ✅ Upload de logo et image hero
- ✅ Gestion du contenu (hero, à propos, méthode)
- ✅ Galerie de transformations (avant/après)
- ✅ Gestion des forfaits et tarifs
- ✅ Dashboard moderne avec Inertia + Vue 3

### Architecture multi-tenant

- **Pattern**: Single database avec filtrage par `coach_id`
- **Résolution**: Middleware `ResolveCoachFromHost` pour détecter le coach depuis le sous-domaine
- **Isolation**: Toutes les données sont filtrées par `coach_id`

## 📚 Documentation

Voir le dossier `/doc` pour plus de détails:

- [`concept.md`](./doc/concept.md) - Document technique complet
- [`avancement.md`](./doc/avancement.md) - Suivi du développement

## 🚀 Déploiement

### Prérequis production

- VPS (Ubuntu LTS)
- Nginx avec configuration wildcard
- PHP-FPM 8.2+
- MySQL 8+
- Redis (cache & queues)
- Stockage S3 ou compatible
- DNS wildcard configuré (*.domain.com)

### Laravel Forge

Configuration recommandée pour un déploiement simplifié avec Laravel Forge.

## 🔒 Sécurité

- Authentification via Laravel Breeze
- CSRF protection
- XSS protection
- SQL injection protection (Eloquent ORM)
- Validation stricte des inputs
- Stockage sécurisé des médias

## 📄 License

Ce projet est sous licence MIT.
