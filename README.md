<!-- README technico-commercial -->
# UNICOACH × Fitness Education Academy (FEA)

Plateforme SaaS multi-tenant qui offre à chaque coach diplômé FEA un **site vitrine professionnel** et un **espace client complet** pour lancer et gérer son activité en quelques jours.

---

## 1) Pitch rapide
- **Clé en main** : site public personnalisé (sous-domaine), branding, contenus, preuves sociales, mentions légales prêtes à adapter.
- **Business ready** : CRM simplifié, suivi clients (mesures, photos, documents), messagerie sécurisée, support intégré.
- **Monétisation intégrée** : abonnement Lemon Squeezy avec tarif préférentiel FEA (20€ HTVA/mois vs 30€), option domaine personnalisé.

## 2) Pour qui ?
- **Coachs diplômés FEA** : un outil opérationnel pour vendre et suivre leurs clients dès la fin de la formation.
- **Équipe FEA** : un levier concret pour tenir la promesse “on vous aide à en vivre”, avec présence de marque discrète dans le panel.
- **Clients finaux des coachs** : un portail sécurisé et simple (lien + code) pour consulter programmes, documents, messages et progression.

## 3) Proposition de valeur
- **Pour FEA** : avantage concurrentiel (tarif négocié, codes promo FEA), présence de marque quotidienne, socle pour animer les alumni.
- **Pour les coachs** : gain de temps (onboarding guidé, modèles prêts), image pro sans développeur, suivi centralisé.
- **Pour les clients** : expérience moderne, historique des échanges et des progrès, confiance renforcée.

## 4) Fonctionnalités clés (synthèse)
- **Site vitrine** : Hero, À propos, Méthode, Tarifs, Transformations, FAQ, CTA ; branding (couleurs, logo, hero, layouts), preview temps réel.
- **Contenus & preuves** : galerie avant/après, FAQ drag & drop, textes marketing structurés, liens sociaux.
- **CRM coaching** : base clients avec code élève et lien sécurisé, fiche 360° (contexte, objectifs, matériel, alimentation, blessures, etc.).
- **Suivi & delivery** : mesures + photos avec analytics, notes de séance, messagerie coach ↔ client avec pièces jointes, partage/versioning de documents (program, nutrition, bilans).
- **Espace client** : accès par lien privé `/p/{token}` + code à 6 chiffres, programmes, nutrition, bilans, notes, profil, évolution, messagerie.
- **Monétisation** : plans tarifaires (activation/désactivation, réorg), facturation Lemon Squeezy (tarif FEA préférentiel), option domaine personnalisé.
- **Support** : tickets intégrés vers l’équipe UNICOACH/FEA.
- **Légal** : mentions légales & CGV préremplies à adapter par coach.

## 5) Parcours utilisateur (coach)
1. Création de compte (code promo FEA ou paiement standard).
2. Wizard de configuration (branding, images, contenus, sections avancées).
3. Publication du site sur un sous-domaine dédié.
4. Ajout des premiers clients, partage de documents, ouverture des premiers créneaux payants.

## 6) Stack & architecture
- **Backend** : Laravel 11 (PHP 8.2+), Sanctum, Spatie Activity Log & Backup, Medialibrary.
- **Frontend** : Vue 3 + Inertia, Vite, TailwindCSS (via stack Breeze/Inertia).
- **Base de données** : SQL (MySQL/MariaDB ou compatible) selon config Laravel.
- **Billing** : Lemon Squeezy (abonnements, portail client, domaines personnalisés).
- **Multi-tenant léger** : segmentation par sous-domaines coach, ressources isolées au niveau applicatif et stockage media via Medialibrary.

## 7) Prérequis
- PHP 8.2+, Composer
- Node.js 20+ / npm
- Base SQL (MySQL/MariaDB recommandée)
- Accès Lemon Squeezy (clés API) pour la facturation/domaine personnalisé

## 8) Mise en route locale (développeurs)
```bash
# Installer les dépendances
composer install
npm install

# Configurer l'environnement
cp .env.example .env
php artisan key:generate

# Préparer la base
php artisan migrate --graceful

# Lancer l'environnement de dev (serveur, queue, logs, Vite) – voir script composer "dev"
composer run dev
```

Tests & qualité :
```bash
php artisan test          # tests PHP
./vendor/bin/pint         # formattage PHP (si installé)
```

Build front :
```bash
npm run build
```

Sauvegardes (Spatie Backup) :
```bash
php artisan backup:run
```

## 9) Déploiement (rappels)
- `APP_ENV=production`, `APP_DEBUG=false`, clés Lemon Squeezy en prod.
- `php artisan migrate --force`
- `npm run build` puis publication des assets
- Jobs/queue actifs (messagerie, traitements médias, mails).
- Planifier les backups (Spatie) via cron.

## 10) Sécurité & conformité
- Accès client par lien privé + code 6 chiffres (pas de gestion de mot de passe pour les clients finaux).
- Séparation logique par sous-domaine coach ; vérifier la configuration DNS/SSL par tenant pour la prod.
- Médias gérés via Medialibrary ; choisir un stockage chiffré et sauvegardé.
- Secrets dans `.env` (ne pas committer).

## 11) Roadmap courte (extraits)
- Moteur de plans alimentaires relié à la base **Ciqual** (Anses) avec substitutions auto (ETA fin janv. 2026).
- Annuaire FEA des coachs utilisateurs UNICOACH (mise en avant prioritaire).
- Enrichissement support & analytics (progression clients, adoption coachs).

## 12) Tarification
- Diplômés FEA : **20€ HTVA / mois**
- Autres coachs : **30€ HTVA / mois**
- Option : domaine personnalisé (commande via Lemon Squeezy)

## 13) Support & contact
- Tickets depuis le dashboard coach (canal recommandé).
- Pour le partenariat FEA : point de contact commercial FEA + équipe M4 Entreprise (UNICOACH).

---

**Résumé** : UNICOACH matérialise la promesse FEA “on ne vous forme pas seulement, on vous aide à en vivre”, avec un site pro clé en main, un espace client complet et une facturation intégrée, tout en maintenant une présence de marque discrète au quotidien.
