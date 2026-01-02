# UNICOACH

Plateforme SaaS multi-tenant pour coachs sportifs permettant à chaque coach d'avoir son propre site personnalisable via sous-domaine.

## 🧭 Présentation commerciale (partenariat Fitness Education Academy)

UNICOACH est une plateforme web clé-en-main conçue pour être déployée en partenariat avec **Fitness Education Academy (FEA)**, école de formation de coachs sportifs.

### Pour Fitness Education Academy

- **Valoriser les diplômés** : offrir un outil concret pour lancer leur activité en ligne dès la fin de la formation.
- **Renforcer la marque FEA** : sites des coachs co-brandés via des sous-domaines dédiés qui prolongent l’expérience FEA.
- **Standardiser la qualité en ligne** : chaque coach dispose d’un site moderne, responsive, aligné avec les bonnes pratiques pédagogiques et marketing.

### Pour les coachs diplômés FEA

- **Site pro en quelques minutes** : création de site via sous-domaine dédié, sans compétences techniques.
- **Branding personnalisable** : couleurs, logo, visuels et textes ajustables depuis un dashboard simple.
- **Mise en avant des résultats** : galerie « avant/après », FAQ, appels à l’action optimisés et formulaire de contact intégré.
- **Tarif préférentiel FEA** : implémentation actuelle d’un abonnement à **20€ HTVA / mois** pour les diplômés FEA (vs **30€ HTVA / mois** standard), géré via Lemon Squeezy (paiement et facturation).

Les sections ci-dessous détaillent l’architecture et l’installation pour l’équipe technique.

## 🎯 Concept

UNICOACH est une solution (anciennement FEA-COACH) permettant aux coachs sportifs de créer rapidement leur site web personnalisé accessible via un sous-domaine unique (ex: `coach-name.kineseducation.academy`). Chaque coach peut gérer son contenu, ses couleurs, ses images et ses tarifs via un dashboard simple et intuitif.

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

En local, vous pouvez aussi démarrer rapidement avec SQLite en laissant la configuration par défaut de `.env.example` (`DB_CONNECTION=sqlite`).

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

- **coaches**: Profils des coachs (slug, couleurs, contenus, statistiques, sections personnalisées)
- **users**: Utilisateurs (avec role, coach_id, champs d'onboarding, statut d'abonnement, essais, etc.)
- **coach_transformations**: Galerie avant/après
- **plans**: Plans tarifaires des coachs
- **faqs**: Questions fréquentes par coach
- **promo_code_requests**: Demandes de codes promo
- **promo_code_batches**: Lots de codes promo pré-générés
- **contact_messages**: Messages envoyés via le formulaire de contact
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
- ✅ Gestion du contenu (page `/dashboard/content`) :
  - Hero (titre, sous-titre)
  - À propos (texte + statistiques personnalisables)
  - Méthode (titre, sous-titre, description + 3 étapes)
  - Sections Tarifs, Transformations, FAQ et Appel à l'action final (titres/sous-titres)
  - Texte des boutons d'appel à l'action
- ✅ Gestion de la photo de profil (upload/suppression avec preview)
- ✅ Gestion de la galerie :
  - Ajout transformations avec modal
  - Upload images avant/après
  - Suppression avec confirmation
  - Réorganisation (à venir)
- ✅ Gestion des plans tarifaires (création, édition, suppression, activation) via `/dashboard/plans`
- ✅ Gestion des FAQs (création, édition, suppression, activation) intégrée au contenu
- ✅ Dashboard enrichi avec statistiques (complétion du profil, nombre de plans, transformations, statut du site)
- ✅ Validation temps réel
- ✅ Feedback visuel (succès/erreur)
- ✅ Navigation fluide (Inertia SPA)

### Onboarding & activation des comptes

- ✅ Onboarding en 3 étapes après connexion
  - Step 1 : type de compte (diplômé FEA / non diplômé)
  - Step 2 : informations légales (nom, prénom, TVA, adresse)
  - Step 3 : activation par code promo ou paiement (Lemon Squeezy)
- ✅ Activation automatique du compte et du profil coach lors de l'approbation d'une demande de code promo
- ✅ Redirection intelligente vers le dashboard une fois l'onboarding complété

### Facturation (Lemon Squeezy)

La facturation MVP est basée sur Lemon Squeezy.

Variables d'environnement nécessaires (voir `.env.example`) :

```env
LEMON_SQUEEZY_API_KEY=
LEMON_SQUEEZY_STORE_ID=
LEMON_SQUEEZY_VARIANT_NON_FEA=
LEMON_SQUEEZY_VARIANT_FEA=
LEMON_SQUEEZY_WEBHOOK_SECRET=
LEMON_SQUEEZY_BASE_URL=https://api.lemonsqueezy.com/v1
```

Webhook (public, protégé par signature) :

- **Endpoint**: `POST /webhooks/lemonsqueezy`
- **Header de signature**: `X-Signature` (HMAC SHA-256 hex digest du payload brut)

Checklist sandbox / test end-to-end (transaction fictive) :

- **Créer un Store** et des **Variants** en mode test dans Lemon Squeezy
- **Renseigner** les variables ci-dessus dans `.env`
- **Configurer** un webhook Lemon Squeezy avec :
  - URL: `https://<votre-domaine-ou-tunnel>/webhooks/lemonsqueezy`
  - Signing secret: la même valeur que `LEMON_SQUEEZY_WEBHOOK_SECRET`
- **Exécuter** le flow onboarding jusqu'au paiement (Step 3)
- **Vérifier** que l'événement `subscription_created` met à jour l'utilisateur et déclenche la création du profil coach si nécessaire

### Panel Admin (multi-tenant)

- ✅ Panel d'administration dédié (`/admin`) protégé par middleware `admin`
- ✅ Gestion des coachs : création, édition, suppression
- ✅ Gestion des sous-domaines personnalisés
- ✅ Activation/désactivation des coachs
- ✅ Génération automatique du sous-domaine à partir du nom

### Architecture multi-tenant

- **Pattern**: Single database avec filtrage par `coach_id`
- **Résolution**: Middleware `ResolveCoachFromHost` pour détecter le coach depuis le sous-domaine
- **Isolation**: Toutes les données sont filtrées par `coach_id`
- **Domaine**: Configuration basée sur `APP_DOMAIN` (ex: `localhost:8000` en local, `votre-domaine.com` en production) avec DNS wildcard (`*.votre-domaine.com`)

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

## 📚 Documentation

Voir le dossier `/doc` pour plus de détails :

- [`concept.md`](./doc/concept.md) - Vision technique et architecture complète
- [`FEA-proposition-commerciale.md`](./doc/FEA-proposition-commerciale.md) - Proposition commerciale et positionnement du produit

## 📊 Statut du projet

**Statut actuel :** cœur fonctionnel en place (multi-tenant, sites publics, dashboard, contenu, onboarding, panel admin). La facturation et les tests automatisés restent à finaliser.

### ✅ Fonctionnalités complétées

- ✅ Setup Laravel 11 + packages
- ✅ Modèles & migrations
- ✅ Multi-tenancy (single database, résolution par sous-domaine)
- ✅ Seeders avec données de test et comptes de démo
- ✅ Sites publics (Blade + Alpine.js) avec sections complètes (Hero, À propos, Méthode, Tarifs, Transformations, FAQ, CTA, CTA final)
- ✅ Dashboard (Vue 3 + Inertia) avec mode sombre
- ✅ Gestion avancée du contenu (stats, méthode, tarifs, transformations, FAQ, CTA final, photo de profil)
- ✅ Upload de médias (logo, hero, transformations) via Spatie Media Library
- ✅ Gestion des plans tarifaires (création/édition/suppression/activation)
- ✅ Système de FAQ dynamique (CRUD et affichage public)
- ✅ Onboarding en 3 étapes + activation par code promo
- ✅ Panel Admin pour la gestion des coachs et des sous-domaines

### 🔄 En cours / À venir

- ⏳ Finalisation et tests end-to-end Lemon Squeezy (sandbox + webhooks)
- ⏳ Tests automatisés (Feature, Unit)
- ⏳ Optimisation des performances et de la mise en cache
- ⏳ Configuration production (Redis, Supervisor, workers)
- ⏳ Analytics intégrés et métriques business

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
- Variable d'environnement `APP_DOMAIN` configurée (ex: `localhost:8000` en local, `kineseducation.academy` en production)

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

## 📄 Licence & droits d'utilisation

UNICOACH est un logiciel propriétaire distribué dans le cadre d’un partenariat avec Fitness Education Academy et/ou d’accords commerciaux spécifiques.
Les conditions d’utilisation, de reproduction et de sous-licence sont décrites dans le fichier [`licence.md`](./licence.md).
