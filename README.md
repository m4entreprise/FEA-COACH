# FEA-COACH

Plateforme SaaS multi-tenant pour coachs sportifs permettant à chaque coach d'avoir son propre site personnalisable via sous-domaine.

## 🎯 Concept

FEA-COACH est une solution permettant aux coachs sportifs de créer rapidement leur site web personnalisé accessible via un sous-domaine unique (ex: `coach-name.kineseducation.academy`). Chaque coach peut gérer son contenu, ses couleurs, ses images et ses tarifs via un dashboard simple et intuitif.

## 🏗️ Architecture

### Stack technique

- **Backend**: Laravel 11.31 (PHP 8.2+)
- **Frontend public**: Blade + TailwindCSS + Alpine.js 3.x
- **Dashboard**: Inertia.js + Vue 3 + Vite (avec mode sombre)
- **Base de données**: MySQL/MariaDB (single database multi-tenant)
- **Médias**: Spatie Media Library v11 + stockage local/S3
- **Auth**: Laravel Breeze + Sanctum
- **Styling**: TailwindCSS 3.x avec CSS variables dynamiques

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

5. Exécuter les migrations et seeders
```bash
php artisan migrate:fresh --seed
```

Ceci créera 3 coachs de test :
- `pierre-martin` (actif)
- `sophie-dubois` (actif)
- `thomas-leroy` (inactif)

6. Créer le lien symbolique pour le stockage
```bash
php artisan storage:link
```

7. Compiler les assets
```bash
# Mode développement (avec hot reload)
npm run dev

# Mode production (minifié)
npm run build
```

8. Démarrer le serveur de développement
```bash
php artisan serve
```

Le serveur sera accessible sur `http://localhost:8000`

## 🗂️ Structure de la base de données

### Tables principales

- **coaches**: Profils des coachs (slug, couleurs, contenus)
- **users**: Utilisateurs (avec role et coach_id)
- **coach_transformations**: Galerie avant/après
- **plans**: Forfaits et tarifs
- **media**: Gestion des fichiers (Spatie)
- **activity_log**: Logs d'activité (Spatie)

## 🎨 Fonctionnalités

### Sites publics (Blade + Alpine.js)

- ✅ Design responsive (mobile-first)
- ✅ Théming dynamique avec CSS variables
- ✅ Navigation smooth scroll avec menu mobile
- ✅ Sections complètes :
  - Hero avec image de fond personnalisable
  - À propos avec statistiques
  - Méthode en 3 étapes
  - Grille de tarifs/forfaits
  - Galerie transformations avant/après
  - FAQ accordéon interactif
  - Section contact/CTA
- ✅ Animations fluides avec Alpine.js
- ✅ SEO-friendly

### Dashboard Coach (Vue 3 + Inertia)

- ✅ Interface moderne et intuitive
- ✅ Mode sombre supporté
- ✅ Gestion du branding :
  - Upload logo (preview instantané)
  - Upload image hero (preview instantané)
  - Sélecteur de couleurs (primaire/secondaire)
- ✅ Gestion du contenu :
  - Titre et sous-titre hero
  - Texte "À propos"
  - Description de la méthode
  - Texte des boutons CTA
- ✅ Gestion de la galerie :
  - Ajout transformations avec modal
  - Upload images avant/après
  - Suppression avec confirmation
  - Réorganisation (à venir)
- ✅ Validation temps réel
- ✅ Feedback visuel (succès/erreur)
- ✅ Navigation fluide (Inertia SPA)

### Architecture multi-tenant

- **Pattern**: Single database avec filtrage par `coach_id`
- **Résolution**: Middleware `ResolveCoachFromHost` pour détecter le coach depuis le sous-domaine
- **Isolation**: Toutes les données sont filtrées par `coach_id`

## 🧪 Tests et développement

### Comptes de test

Après le seeding, vous aurez accès aux comptes suivants :

| Coach | Email | Password | Sous-domaine | URL locale |
|-------|-------|----------|--------------|------------|
| Pierre Martin | pierre.martin@example.com | password | pierre-martin | http://pierre-martin.localhost:8000 |
| Sophie Dubois | sophie.dubois@example.com | password | sophie-dubois | http://sophie-dubois.localhost:8000 |
| Thomas Leroy (inactif) | thomas.leroy@example.com | password | thomas-leroy | http://thomas-leroy.localhost:8000 |
| Admin | admin@example.com | password | - | - |

**Note Windows :** Sur Windows, vous devrez peut-être ajouter les sous-domaines à votre fichier `hosts` :
```
127.0.0.1 pierre-martin.localhost
127.0.0.1 sophie-dubois.localhost
```
Fichier : `C:\Windows\System32\drivers\etc\hosts` (nécessite droits admin)

### Guide de test complet

Voir [`GUIDE-TESTING.md`](./GUIDE-TESTING.md) pour les scénarios de test détaillés.

## 📚 Documentation

Voir le dossier `/doc` pour plus de détails :

- [`concept.md`](./doc/concept.md) - Vision technique et architecture complète
- [`avancement.md`](./doc/avancement.md) - Suivi détaillé du développement
- [`database-schema.md`](./doc/database-schema.md) - Schéma de base de données
- [`test-accounts.md`](./doc/test-accounts.md) - Comptes de test et configuration

### Résumés des phases

- [`PHASE-6-SUMMARY.md`](./PHASE-6-SUMMARY.md) - Base de données & seeders
- [`PHASE-8-SUMMARY.md`](./PHASE-8-SUMMARY.md) - Routage & contrôleurs
- [`PHASE-9-10-SUMMARY.md`](./PHASE-9-10-SUMMARY.md) - Interfaces utilisateur (Blade + Vue)

## 📊 Statut du projet

**Version actuelle :** 0.8 (Phases 0-10 complétées)  
**Progression :** 80%

### ✅ Fonctionnalités complétées

- ✅ Setup Laravel 11 + packages
- ✅ Modèles & migrations
- ✅ Multi-tenancy (single database)
- ✅ Seeders avec données de test
- ✅ Contrôleurs & routes
- ✅ Sites publics (Blade + Alpine.js)
- ✅ Dashboard (Vue 3 + Inertia)
- ✅ Upload de médias (logo, hero, transformations)
- ✅ Théming dynamique

### 🔄 En cours / À venir

- ⏳ Tests automatisés (Feature, Unit)
- ⏳ Configuration production (Redis, Supervisor)
- ⏳ Optimisation performances
- ⏳ Déploiement
- ⏳ Gestion des plans/abonnements (Stripe)
- ⏳ Analytics intégrés

## 🚀 Déploiement

### Prérequis production

- VPS (Ubuntu 22.04 LTS)
- Nginx avec configuration wildcard
- PHP-FPM 8.2+
- MySQL 8.0+
- Redis (cache & queues)
- Supervisor (gestion queues)
- Stockage S3 ou compatible
- DNS wildcard configuré (`*.domain.com`)
- Certificat SSL wildcard (Let's Encrypt)

### Laravel Forge (recommandé)

Configuration recommandée pour un déploiement simplifié avec Laravel Forge :
1. Configurer le serveur avec Nginx + MySQL + Redis
2. Activer les queues avec Supervisor
3. Configurer le certificat SSL wildcard
4. Déployer via Git push

### Configuration Nginx wildcard

```nginx
server {
    listen 80;
    server_name ~^(?<subdomain>.+)\.domain\.com$;
    
    root /path/to/app/public;
    index index.php;
    
    # Configuration Laravel standard
    # ...
}
```

## 🔒 Sécurité

- Authentification via Laravel Breeze
- CSRF protection
- XSS protection
- SQL injection protection (Eloquent ORM)
- Validation stricte des inputs
- Stockage sécurisé des médias

## 📄 License

Ce projet est sous licence MIT.
