# Audit 1 – Vue d’ensemble de la codebase
 
## 1. Contexte
La codebase audité correspond à une plateforme SaaS construite sur **Laravel 11 (PHP 8.2)** avec un frontend **Vue 3 + Inertia.js + Tailwind CSS**, packagé par **Vite**.

L’application permet à des **coachs** de disposer :
- d’un **site public** accessible via un sous-domaine (`{coach_slug}.domaine`), géré par `CoachSiteController` ;
- d’un **espace sécurisé de gestion** (dashboard) pour configurer leur branding, leur contenu, leurs offres, leurs mentions légales, et gérer leurs clients.

Le parcours utilisateur repose sur :
- un **onboarding** guidé en plusieurs étapes (`OnboardingController`, routes `/onboarding/*`) qui détermine notamment le statut FEA du coach, collecte les informations légales et prépare le paiement ;
- un **setup wizard** (`SetupWizardController`, routes `/setup/*`) pour configurer le profil coach et son site ;
- un **dashboard** pour l’exploitation quotidienne (`/dashboard/*`).

La monétisation se fait par **abonnement récurrent**, avec :
- intégration principale à **Lemon Squeezy** (création de sessions de paiement, webhooks d’abonnement) ;
- historiquement, intégration à **Fungies.io** (checkout, portail client, gestion d’abonnements), aujourd’hui abandonnée au profit exclusif de Lemon Squeezy ;
- une logique métier spécifique pour les **diplômés FEA**, qui bénéficient d’un tarif préférentiel et d’un flux de code promo dédié.

Le système exploite également plusieurs packages tiers Laravel (Spatie Activitylog, Backup, Medialibrary, Sanctum, Ziggy…) qui structurent certaines responsabilités transverses (logs d’activité, sauvegardes, gestion de médias, auth API, génération de routes JS).

## 2. Objectifs de l’audit
L’audit vise à fournir une **vue complète et argumentée** de la santé technique de la plateforme, avec un focus sur les axes suivants :

- **Architecture & conception**
  - Comprendre l’organisation du code (dossiers `app/`, `routes/`, `config/`, `database/`, `resources/`, `tests/`).
  - Identifier comment sont structurés les domaines métier (onboarding, setup wizard, dashboard, sites publics, abonnement, support, administration).
  - Évaluer la séparation des responsabilités (Controllers vs Services/Models, logique métier vs logique de présentation).

- **Qualité de code & maintenabilité**
  - Apprécier la lisibilité, la cohérence du style, la duplication de code et l’usage des conventions Laravel.
  - Identifier les zones à risque de dette technique (classes trop grandes, logique imbriquée, manque de tests, patterns artisanaux).

- **Sécurité & conformité**
  - Vérifier l’authentification/autorisation, la surface d’exposition des routes (sites publics vs back-office), les validations de données et la gestion des webhooks.
  - Examiner les points en lien avec la protection des données (données personnelles des coaches / clients, abonnements, logs).

- **Performance & scalabilité**
  - Identifier les flux potentiellement critiques (onboarding, dashboard, webhooks, génération de sites publics).
  - Repérer les risques N+1, les requêtes lourdes, les usages (ou absences) de cache/queues.

- **Frontend & UX**
  - Comprendre l’architecture front Vue 3/Inertia, l’organisation des pages/components et l’intégration avec Laravel.
  - Évaluer la cohérence de l’expérience utilisateur (flows onboarding, gestion d’abonnement, navigation dashboard/site public).

- **Tests, observabilité & opérations**
  - Examiner la présence et la qualité des tests (unitaires/fonctionnels).
  - Vérifier la gestion des logs, sauvegardes, scripts de run local (`composer dev`, `npm run dev`) et de déploiement.

L’objectif final est de **produire des recommandations priorisées** (quick wins vs chantiers structurants) pour sécuriser la plateforme et faciliter son évolution.

## 3. Périmètre

**Périmètre inclus dans l’audit**

- **Backend Laravel**
  - Dossiers `app/`, `routes/`, `config/`, `bootstrap/`, `database/`.
  - Modèles et relations, contrôleurs HTTP, services métiers (ex. `LemonSqueezyService`), middlewares, events/queues s’ils existent.
  - Routes principales :
    - sites publics de coachs (wildcard domain),
    - onboarding (`/onboarding/*`),
    - setup wizard (`/setup/*`),
    - dashboard (`/dashboard/*`),
    - admin (`/admin/*`),
    - webhooks d’abonnement/paiement.

- **Frontend & assets**
  - Code Vue 3/Inertia dans `resources/js`, vues Blade éventuelles dans `resources/views`, styles dans `resources/css`.
  - Configuration Vite, Tailwind, PostCSS et scripts NPM.

- **Intégrations externes**
  - Lemon Squeezy (création de sessions de checkout, gestion des abonnements via webhooks, vérification de signature).
  - Code d’intégration **Fungies.io** encore présent (checkout, gestion d’abonnements, portail client, vérification de signature), mais fournisseur aujourd’hui remplacé fonctionnellement par Lemon Squeezy (dette à nettoyer).
  - Stripe via `config/services.php` et utilisation dans l’onboarding (clé publique exposée côté front pour le paiement).

- **Scripts & configuration projet**
  - `composer.json`, `package.json`, configuration applicative dans `config/`.
  - Scripts artisan personnalisés, scripts de développement/déploiement présents dans le dépôt.

**Hors périmètre (ou à documenter séparément)**

- Infrastructure d’hébergement et CI/CD détaillée (serveurs, conteneurs, reverse proxy, certificats TLS, pipelines externes) si non décrite dans le dépôt.
- Outils de monitoring/alerting externes (APM, logs centralisés, dashboards) non référencés dans le code ou la configuration.
- Documentation métier/commerciale qui ne se trouve pas dans ce repository (contrats, supports marketing, process internes FEA).

## 4. Synthèse exécutive (provisoire)

### 4.1 Santé globale par axe (maturité)

Échelle indicative : **1 = très faible**, **5 = très bon**.

| Axe                           | Score | Commentaire synthétique |
|-------------------------------|:-----:|-------------------------|
| Architecture & conception     | 3/5   | Structure globale claire par domaines (Onboarding, Setup, Dashboard, Sites publics, Webhooks) mais contrôleurs très denses et absence de services métier dédiés. |
| Base de données & persistance | 3/5   | Schéma globalement normalisé et cohérent avec le modèle métier, mais table `coaches` surchargée et quelques migrations legacy (Fungies) à clarifier. |
| Sécurité & conformité         | 3/5   | Auth Laravel standard, séparation admin, webhooks signés ; enjeux RGPD importants et logs à assainir. |
| Performance & scalabilité     | 2/5   | Eager loading en place mais peu de queues/caches et composants Vue lourds ; risque de saturation si trafic en hausse. |
| Frontend & UX                | 3/5   | Flows onboarding/setup bien pensés et Inertia cohérent ; dette sur la taille des composants et aspects a11y/perf non encore traités systématiquement. |
| Tests & qualité logicielle    | 2/5   | Auth et profil couverts, mais peu de tests sur les parcours métier critiques et les intégrations externes. |
| Opérations, CI/CD & logs      | 2/5   | Logging riche et backups Spatie présents ; absence de pipeline CI/CD et de monitoring applicatif explicite. |

### 4.2 Top 5 des risques identifiés

1. **RGPD & données personnelles**  
   Volume important de données perso (coachs, clients, abonnements) sans outils visibles d’export/suppression ni politique formalisée de rétention des logs et sauvegardes.

2. **Parcours paiements & webhooks critiques mais peu testés**  
   Flows Lemon Squeezy au cœur du business (onboarding, abonnements), avec traitement majoritairement synchrone et très peu de tests automatisés.

3. **Table `coaches` et composants Vue “god object”**  
   Forte concentration de champs et de responsabilités côté `coaches` et `Dashboard.vue`/`Welcome.vue`, rendant plus difficiles les évolutions futures et augmentant le risque de régressions.

4. **Couverture de tests insuffisante sur les cas métier critiques**  
   Onboarding, subscription, multi-tenancy par coach, support et webhooks sont peu ou pas couverts, limitant la confiance lors des changements.

5. **Absence de CI/CD et de monitoring applicatif standardisé**  
   Pas de pipeline automatique visible ni d’outil de suivi d’erreurs en prod, ce qui complique la détection précoce de régressions ou d’incidents.

Ces risques sont pris en compte dans la **roadmap de remédiation** (section 6) qui distingue quick wins, chantiers moyen terme et chantiers structurants.

## 5. Analyse détaillée

### 5.1 Architecture & structure du code

#### 5.1.1 Découpage global

- **Framework** : Laravel 11, architecture MVC classique.
- **Domaines principaux** identifiés via les contrôleurs et les routes :
  - **Sites publics de coachs** : `CoachSiteController`, domaine wildcard `{coach_slug}.domain` avec middleware de résolution de coach.
  - **Onboarding** : `OnboardingController`, groupe de routes `/onboarding/*` (choix FEA, infos légales, paiement).
  - **Setup Wizard** : `SetupWizardController`, groupe `/setup/*`, 5 étapes pour configurer le site coach.
  - **Dashboard coach** : `DashboardController` + contrôleurs dans `App\Http\Controllers\Dashboard\*` (branding, contenu, galerie, plans, clients, FAQ, légal, abonnement, support).
  - **Administration** : contrôleurs dans `App\Http\Controllers\Admin\*` (gestion coaches, demandes de codes promo, support).
  - **Abonnement & paiements** : intégration actuelle via Lemon Squeezy (`OnboardingController`, `LemonSqueezyService`, `LemonSqueezyWebhookController`) ; code historique lié à `SubscriptionController` et `FungiesService` toujours présent mais voué à être décommissionné.

Les routes sont organisées par **groupes cohérents** (admin, onboarding, setup, dashboard, webhooks) avec middlewares adaptés (`auth`, `verified`, `admin`, `onboarding.completed`, `setup.completed`, etc.), ce qui fournit une bonne base de séparation fonctionnelle.

#### 5.1.2 Modèles & relations clés

- **`User`**
  - Hérite de `Authenticatable`, utilise `HasFactory`, `Notifiable`.
  - Champs fonctionnels : `role`, `coach_id`, `first_name`, `last_name`, `vat_number`, `legal_address`, `is_fea_graduate`, `fea_promo_code`, champs d’onboarding/setup (`onboarding_completed`, `setup_completed`, `setup_step`, `has_completed_onboarding`), champs d’abonnement (`subscription_status`, `fungies_customer_id`, `fungies_subscription_id`, `trial_ends_at`, périodes d’abonnement, `cancel_at_period_end`).
  - Relation : `coach(): BelongsTo`.

- **`Coach`**
  - Porte l’essentiel de la **configuration du site vitrine** (slug, subdomain, couleurs, contenus marketing, CTA, sections de méthode, pricing, transformations, mentions légales, réseaux sociaux, layout).
  - Relations :
    - `user(): BelongsTo` (propriétaire du profil coach).
    - `transformations(): HasMany` (`CoachTransformation`, avec `order`).
    - `plans(): HasMany` (offres/coaching, tarifs, URL de CTA).
    - `faqs(): HasMany` (foire aux questions).
    - `contactMessages(): HasMany` (messages reçus via le site public).
    - `clients(): HasMany` (clients coachés et leurs notes).
  - Implémente `Spatie\MediaLibrary\HasMedia` via `InteractsWithMedia` pour gérer les médias (logo, hero, profil) avec **fallbacks** (images par défaut).
  - Accesseur `site_layout_or_default` basé sur `config('coach_site')` permettant de sélectionner dynamiquement le layout.

- **`Client`**
  - Relié à `Coach` (`coach_id`), avec informations de contact et TVA.
  - Relation `notes(): HasMany` vers `ClientNote`.

Globalement, le modèle de domaine est **centré sur le coach** (site + contenu + offres + relation client) avec l’utilisateur comme propriétaire/authentifié.

#### 5.1.3 Contrôleurs & logique métier

- **`DashboardController`**
  - Construit la vue principale du dashboard :
    - Charge le `coach` lié à l’utilisateur avec ses relations (`plans`, `transformations`, `faqs`, `user`).
    - Calcule des **statistiques** (nombre de plans, transformations, complétion de profil, statut d’activation, etc.).
    - Calcule un bloc `subscription` (statut, période d’essai, jours restants) à partir des champs du `User`.
    - Utilise une méthode privée `calculateProfileCompletion()` pour dériver un pourcentage de complétion et la liste des champs manquants, avec routes de correction associées.

- **`SetupWizardController`**
  - Orchestration en 5 étapes de la configuration du site (branding, images, contenu principal, sections avancées, finalisation).
  - Gère des actions `demo` (pré-remplissage de contenus) et `save` (validation + mise à jour des colonnes sur `Coach`).
  - Met à jour `user.setup_step` et `user.setup_completed`.

- **`CoachSiteController`**
  - Récupère le `Coach` injecté par un middleware dédié à partir du host.
  - Charge paresseusement les relations nécessaires (`user`, `transformations`, `plans` actifs, `faqs` actives) et choisit le layout via la config `coach_site`.
  - Expose : page publique, formulaire de contact, page de mentions légales.

- **Onboarding & Abonnements**
  - `OnboardingController` gère le flow d’enrôlement (statut FEA, données légales, promo code, paiement Lemon Squeezy) et la création de profil coach.
  - `SubscriptionController` (dashboard) contient encore la logique d’abonnement via Fungies (création de checkout, portail client, annulation), qui devrait être revue/supprimée dans le cadre de la migration complète vers Lemon Squeezy.
  - `LemonSqueezyWebhookController` et `FungiesWebhookController` traitent les webhooks, ce dernier étant lié à l’ancienne intégration Fungies.

**Constats préliminaires**

- **Points positifs**
  - Découpage global lisible par domaine (Onboarding, Setup, Dashboard, Admin, Sites publics, Webhooks).
  - Utilisation correcte de **relations Eloquent** et d’eager loading ciblé pour limiter les N+1 (cf. `CoachSiteController`).
  - Utilisation de **Spatie Medialibrary** pour la gestion des médias et de `config('coach_site')` pour abstraire les layouts.

- **Points de vigilance / dette potentielle**
  - Certains contrôleurs sont **très denses** et mélangent orchestration de flux, logique métier et détails d’intégration (ex. `OnboardingController`, `SetupWizardController`, `DashboardController`). À terme, cela plaide pour l’extraction vers des **services / actions** dédiés.
  - L’instanciation directe de services (`new LemonSqueezyService()` dans l’onboarding) coexiste avec l’injection de dépendances dans d’autres contrôleurs (`__construct(LemonSqueezyService $service)` dans le webhook), ce qui crée une **incohérence de style** et complique le test unitaire.
  - La logique de calcul d’état d’essai / abonnement (statut, `trial_ends_at`, etc.) semble **dupliquée** entre `DashboardController` et `SubscriptionController`, ce qui augmente le risque de divergence.

Ces points seront approfondis et illustrés dans les sections de recommandations.

### 5.2 Base de données & persistance

#### 5.2.1 Vue d’ensemble du schéma

Le schéma est construit via une série de **migrations incrémentales** qui enrichissent progressivement les tables `users` et `coaches` et ajoutent les entités métier :

- **`users`**
  - Schéma de base (id, name, email, password, timestamps, sessions) fourni par Laravel.
  - Migration `add_onboarding_fields_to_users_table` :
    - Ajout des champs de profil légal et d’onboarding (`first_name`, `last_name`, `vat_number`, `legal_address`, `is_fea_graduate`, `fea_promo_code`, `onboarding_completed`, `stripe_customer_id`, `subscription_status`).
  - Migration `add_has_completed_onboarding_to_users_table` :
    - Ajout de `has_completed_onboarding` pour distinguer la fin réelle de l’onboarding.
  - Migration `add_lemonsqueezy_fields_to_users_table` :
    - Ajout conditionnel des champs : `fungies_customer_id`, `fungies_subscription_id`, `trial_ends_at`, `subscription_current_period_end`, `subscription_current_period_start`, `cancel_at_period_end`.

- **`coaches`**
  - Migration `create_coaches_table` :
    - Lien vers `users` via `user_id` (FK `onDelete('cascade')`).
    - Colonnes principales : `name`, `slug` (unique), `subdomain` (unique, nullable), couleurs, texte hero & présentation, CTA, `is_active`.
  - D’autres migrations (non détaillées ici mais listées dans `database/migrations`) ajoutent les champs de **méthode**, **pricing**, **transformations**, **CTA intermédiaires**, **statistiques**, **mentions légales**, **réseaux sociaux**, **layout de site**, ce qui élargit fortement la table `coaches`.

- **Autres tables métier**
  - `coach_transformations` : transformations associées à un coach (`coach_id` en FK, titre, description, ordre).
  - `plans` : plans d’abonnement/offres d’un coach (`coach_id` en FK, nom, description, prix, `cta_url`, flag `is_active`).
  - `clients` : clients rattachés à un coach (`coach_id` en FK, identité, contact, adresse, TVA).
  - D’autres tables (faqs, contact_messages, support_tickets, support_messages, promo_code_requests, promo_code_batches, etc.) existent également, chacune liée à `coaches` ou `users` selon le cas (à détailler dans l’audit complet DB).

De manière générale, le schéma reflète bien le **modèle conceptuel** observé côté code : un **User** propriétaire d’un **Coach** et d’un site, avec un ensemble de contenus, offres et relations clients.

#### 5.2.2 Intégrité référentielle & indexation

- Utilisation généralisée de `foreignId()->constrained()->onDelete('cascade')` pour :
  - `coach_id` dans `coaches_transformations`, `plans`, `clients`.
  - `user_id` dans `coaches` (nullable mais avec cascade). 
- Clés uniques sur `coaches.slug` et `coaches.subdomain`, ce qui est pertinent pour un modèle multi-tenant basé sur le slug / sous-domaine.
- Les colonnes `user_id` et `coach_id` sont indexées automatiquement par `foreignId`, ce qui est adapté aux requêtes fréquentes depuis les contrôleurs.

Points à vérifier/approfondir dans la suite de l’audit :

- Présence d’**index additionnels** éventuels sur des colonnes fortement filtrées (ex. `is_active`, `status`, ordres, timestamps) pour optimiser des listes volumineuses.
- Cohérence entre les **types de colonnes** et leur usage (ex. `text` vs `longText` pour les contenus marketing, taille de `decimal(10, 2)` pour les prix).

#### 5.2.3 Cohérence schéma ↔ modèles / code

- Le modèle `User` comprend déjà dans son `$fillable` des champs comme `fungies_customer_id`, `fungies_subscription_id`, `subscription_status`, `trial_ends_at`, etc., cohérents avec la migration `add_lemonsqueezy_fields_to_users_table`.
- La migration `add_fungies_fields_to_users_table` est **actuellement vide** (aucune colonne ajoutée ni supprimée). Cela laisse penser qu’elle a servi de placeholder avant d’être remplacée par `add_lemonsqueezy_fields_to_users_table`.
  - **Risque** : selon l’historique d’exécution des migrations sur les environnements, la structure réelle peut varier (ex. environnements où cette migration a été modifiée manuellement). Ce point devra être contrôlé en base.
- Les contrôleurs d’onboarding et de souscription s’appuient sur de nombreux champs de `users` et `coaches` (statut FEA, TVA, statut d’abonnement, dates de période d’essai, contenus marketing). La **cohérence** générale champs ↔ usage semble bonne sur l’échantillon analysé, mais mérite un passage systématique (tous les modèles vs toutes les migrations) dans l’étape DB complète.

#### 5.2.4 Observations préliminaires

- **Forces**
  - Schéma globalement **normalisé** autour d’entités claires (User, Coach, Client, Plan, Transformation, FAQ, Support…).
  - Utilisation systématique des **clés étrangères** et du `cascade` pour garder la base propre lors des suppressions.
  - Bon alignement avec le modèle orienté “coach propriétaire de son site et de ses contenus/clients”.

- **Points de vigilance**
  - Table `coaches` très large, enrichie par de multiples migrations successives : risque de **table “god object”** pour tout le contenu marketing, ce qui peut devenir difficile à maintenir si le nombre de champs continue de croître.
  - Migration `add_fungies_fields_to_users_table` vide : doit être soit nettoyée, soit documentée pour éviter les incohérences entre environnements.
  - Nécessité de vérifier l’existence d’**indices complémentaires** et de revues de requêtes pour les parties les plus actives (dashboard, site public, listing clients).

Ces éléments seront approfondis dans la section « Recommandations & priorisation » avec des propositions concrètes (éventuelle factorisation de la table `coaches`, migration de certains blocs dans des tables dédiées, clarification des migrations liées à Fungies/LemonSqueezy).

### 5.3 Sécurité & conformité

#### 5.3.1 Authentification & autorisation

- **Auth Laravel standard (session)**
  - `config/auth.php` : guard par défaut `web` basé sur les sessions, provider Eloquent `App\Models\User`.
  - Routes d’auth gérées par les contrôleurs Breeze (`routes/auth.php`) : inscription, login, reset password, email verification, logout.
  - Password reset : tokens stockés dans la table `password_reset_tokens` avec expiration 60 minutes et throttling.

- **Rôles & accès admin**
  - Middleware `IsAdmin` : vérifie `user()->role === 'admin'`, sinon `403 Unauthorized. Admin access required.`
  - Les routes `/admin/*` sont protégées par `['auth', 'verified', 'admin']` (cf. `routes/web.php`), ce qui limite correctement l’accès à l’admin.

- **Onboarding et Setup obligatoires**
  - Middleware `EnsureOnboardingCompleted` :
    - Redirige les utilisateurs non-admin dont `onboarding_completed` est `false` vers `onboarding.step1`, sauf si la route actuelle est déjà de type `onboarding.*`.
  - Middleware `EnsureSetupCompleted` :
    - Pour les utilisateurs non-admins avec `onboarding_completed = true` et `setup_completed = false`, redirige vers `setup.index` si la route n’est pas `setup.*`.
  - En pratique, cela **force** un parcours sécurisé minimal avant d’accéder au dashboard.

- **Sanctum & API**
  - `config/sanctum.php` : configuration par défaut, `guard` = `['web']`, domaines « stateful » couvrant localhost et variantes.
  - Aucune route API spécifique n’a été observée dans les extraits consultés, l’application semble majoritairement **server-side + Inertia**.

#### 5.3.2 Webhooks & intégrations externes

- **Lemon Squeezy**
  - `LemonSqueezyWebhookController` :
    - Récupère la charge brute (`$request->getContent()`) et la signature `X-Signature`.
    - Vérifie la signature via `LemonSqueezyService::verifyWebhookSignature()` utilisant `hash_hmac('sha256', payload, config('lemonsqueezy.webhook_secret'))` + `hash_equals`.
    - En cas d’échec de vérification : log `warning` et réponse `401 Invalid signature`.
  - Routes de webhooks `POST /webhooks/lemonsqueezy` :
    - Sans CSRF (`->withoutMiddleware(VerifyCsrfToken::class)`), ce qui est normal pour un webhook.
    - Sécurisation reposant donc exclusivement sur le secret de signature.

- **Fungies.io (intégration historique)**
  - `FungiesWebhookController` :
    - Vérifie la signature `X-Fungies-Signature` via `FungiesService::verifyWebhookSignature()` (HMAC SHA-256 avec `config('fungies.webhook_secret')`).
    - Journalise les événements (`Log::info`, `Log::warning`, `Log::error`) et route les events via un `match` sur `event`.
  - Route `POST /webhooks/fungies` : même pattern que Lemon Squeezy (pas de CSRF, signature obligatoire) ; selon tes informations, cette intégration n’est plus utilisée côté métier et la route constitue donc un point à fermer ou à documenter comme legacy.

- **Gestion des secrets & clés API**
  - `config/fungies.php` : lit `FUNGIES_API_KEY`, `FUNGIES_WRITE_API_KEY`, `FUNGIES_WEBHOOK_SECRET`, `FUNGIES_PLAN_ID`, SKUs, URLs de redirection depuis `.env` ; à traiter comme configuration legacy à nettoyer une fois la migration vers Lemon Squeezy entièrement actée.
  - `config/lemonsqueezy.php` (non lu ici mais présumé similaire) + usage dans `LemonSqueezyService`.
  - **Point positif** : pas de secret en dur dans le code ; tout passe par `config()` et `.env`.

#### 5.3.3 Validation des données & surface d’exposition

- **Validation côté backend**
  - Utilisation généralisée de `$request->validate([...])` dans les contrôleurs (Onboarding, CoachSite contact, etc.).
  - Longueurs maximales et formats (`email`, `string`, `max:255`, etc.) définis pour de nombreux champs.
  - Les webhooks parsant du JSON vérifient la structure (`is_array`, présence d’attributs clés) et loggent les anomalies.

- **Routes publiques exposées**
  - Sites coachs : domaine wildcard `{coach_slug}.domain` avec `ResolveCoachFromHost` qui impose `is_active = true` et fait `firstOrFail()` (404 si non trouvé).
  - Pages légales publiques (`/cgvu`, `/politique-confidentialite`) et routes d’auth invitée (`/login`, `/register`, mot de passe oublié…).
  - Webhooks : uniquement sur des endpoints dédiés `/webhooks/lemonsqueezy` et `/webhooks/fungies` protégés par signatures.

- **XSS & CSRF**
  - Les vues publiques sont majoritairement générées via Blade et Vue/Inertia : par défaut, Blade échappe le HTML avec `{{ }}`.
  - Les formulaires non-webhook sont protégés par CSRF (middleware global Laravel) ; seuls les webhooks sont explicitement exemptés.

#### 5.3.4 RGPD & données personnelles

- Les modèles `User`, `Coach`, `Client` et les tables associées contiennent de nombreuses **données personnelles** ou pseudo-personnelles (nom, email, adresse, TVA, messages, notes client, statistiques de coaching).
- Un document `annexe RGPD.md` est présent à la racine du projet (non audité en détail ici) et devra être **corrélé** avec :
  - la durée de conservation effective des logs (config `logging.php`, politique de rotation),
  - les sauvegardes (présence de `spatie/laravel-backup`),
  - les mécanismes d’export/suppression des données (droit à l’oubli, anonymisation des logs et sauvegardes).

**Constats préliminaires (Sécurité)**

- **Forces**
  - Auth Laravel standard robuste, gestion complète du cycle compte/mot de passe/vérification d’email.
  - Séparation nette des rôles via `IsAdmin` et groupe de routes admin.
  - Pipeline d’onboarding/setup obligatoire pour les coaches (réduction des cas d’états incohérents).
  - Webhooks correctement protégés par signature HMAC et journalisation détaillée des erreurs.

- **Points de vigilance**
  - Surface RGPD importante (coachs + clients + abonnements) : besoin de vérifier concrètement les mécanismes de droit d’accès/suppression.
  - `ResolveCoachFromHost` expose les sites coachs par simple présence d’un slug actif : il faudra vérifier comment les slugs sont générés, limités et protégés contre les collisions/énumérations.
  - Pas encore de mécanisme explicite vu pour la **limitation de données dans les logs** (risque de fuite de données sensibles si des payloads complets sont logués en erreur).

### 5.4 Performance & scalabilité

#### 5.4.1 Requêtes & N+1

- De nombreux contrôleurs utilisent l’eager loading (`with([...])`, `loadMissing([...])`) pour limiter les N+1 (ex. `DashboardController`, `CoachSiteController`).
- Cependant :
  - Certains calculs du dashboard comptent les plans, transformations, faqs via `->count()` ré-exécutés sur les relations ; sur de gros volumes, cela peut générer plusieurs requêtes supplémentaires.
  - Les transformations récentes et autres listes sont paginées/limitées (`limit(3)` dans le dashboard), ce qui est positif.

#### 5.4.2 Cache & queues

- Le projet dispose des tables `jobs` / `failed_jobs` et utilise en dev un script `composer dev` qui lance `php artisan queue:listen`, ce qui indique une **infrastructure prête pour les jobs asynchrones**.
- Les contrôleurs observés (onboarding, webhooks, dashboard) semblent encore traiter beaucoup de logique **en ligne** :
  - Exemples : traitement d’événements de webhooks, mise à jour d’utilisateurs, création de coachs sont gérés directement dans les contrôleurs.
  - Des envois d’emails (ex. envoi du lien de paiement FEA) sont faits dans le flux HTTP.
- Aucun usage explicite de caches applicatifs (ex. `Cache::remember`) n’a encore été identifié dans les extraits lus.

#### 5.4.3 Frontend & assets

- Build Vite standard (`npm run dev`, `npm run build`), Vue 3, Tailwind 3, axios.
- Fichiers `Dashboard.vue` (~42Ko) et `Welcome.vue` (~31Ko) suggèrent des **composants monolithiques** potentiellement lourds à charger et maintenir.

**Constats préliminaires (Performance)**

- **Forces**
  - Eager loading mis en place sur plusieurs endpoints critiques.
  - Limitation du nombre de résultats sur certains blocs (dernières transformations dans le dashboard).
  - Stack moderne (Laravel 11 + Vite + Vue 3) permettant facilement la mise en place de splits de bundle et de caches.

- **Points de vigilance**
  - Risque de **saturation** si les webhooks et emails restent synchrones et si le trafic augmente.
  - Taille de certains composants Vue (Dashboard, Welcome) pouvant impacter le temps de chargement initial et la lisibilité du code.
  - Absence visible de stratégie de **cache applicatif** ou HTTP ciblé pour les sites publics de coachs.

### 5.5 Frontend & UX

#### 5.5.1 Architecture front

- Structure `resources/js/Pages` :
  - `Admin/*`, `Auth/*`, `Dashboard/*`, `Legal/*`, `Onboarding/*`, `Profile/*`, `Setup/*`, `Dashboard.vue`, `Welcome.vue`.
- Utilisation d’Inertia : le backend renvoie des composants Vue nommés (`Inertia::render('Onboarding/Step1')`, `Dashboard`, etc.), ce qui assure une **navigation SPA** fluide pour l’utilisateur.

#### 5.5.2 Flows UX principaux

- **Onboarding**
  - Étapes claires (type de compte FEA, infos légales, paiement) avec validation côté backend.
  - Gestion des cas FEA/non-FEA (code promo, tarifs différenciés) et retours d’erreur explicites.

- **Setup Wizard**
  - 5 étapes : branding, images, contenu principal, sections avancées, finalisation.
  - Possibilité de pré-remplir (`action = 'demo'`) pour fournir un exemple de configuration, ce qui est un bon levier UX.

- **Dashboard & site public**
  - Dashboard fournit une vue synthétique : complétion du profil, statut d’activation, stats.
  - Site public structuré autour de sections marketing (hero, méthode, transformations, pricing, CTA…), ajustables dans le dashboard.

**Constats préliminaires (Frontend/UX)**

- **Forces**
  - Architecture SPA avec Inertia cohérente, navigation fluide entre étapes d’onboarding/setup/dashboard.
  - Découpage des pages par domaines (`Onboarding/*`, `Setup/*`, `Dashboard/*`, etc.) facilitant la compréhension.
  - Wizard de configuration avec exemples « demo » pour aider les non-techniques.

- **Points de vigilance**
  - Taille des composants Vue principaux (Dashboard, Welcome) : risque de **composants “god”** difficiles à tester et à faire évoluer ; à confirmer en audit front détaillé.
  - Accessibilité (a11y), responsive avancé, i18n et cohérence graphique ne peuvent pas être évalués pleinement sans revue des templates et CSS.

### 5.6 Tests & qualité logicielle

#### 5.6.1 État actuel des tests

- **Tests Feature**
  - `tests/Feature/Auth/*` : six tests couvrant le cycle auth standard (connexion, inscription, reset password, confirmation, email verification).
  - `ProfileTest` : teste l’accès à `/profile`, la mise à jour du profil, la suppression de compte (avec et sans bon mot de passe).
  - `ExampleTest` : test basique de réponse 200 sur `/`.

- **Tests Unit**
  - `tests/Unit/ExampleTest` : test trivial `true is true`.

#### 5.6.2 Couverture fonctionnelle

- Les tests actuels couvrent essentiellement :
  - Authentification, réinitialisation de mot de passe, vérification d’email.
  - Gestion du profil utilisateur (update, suppression, vérification du mot de passe).
- **Manquent** (à ce stade de l’audit) :
  - Tests pour l’onboarding complet (FEA vs non-FEA, codes promo, paiement Lemon Squeezy).
  - Tests pour le setup wizard (étapes branding, images, contenu, etc.).
  - Tests pour les routes du dashboard (gestion des plans, clients, FAQ, galerie, légal, abonnement).
  - Tests pour les sites publics (affichage du site coach, formulaire de contact).
  - Tests pour les webhooks (Lemon Squeezy, Fungies) et les intégrations externes.

**Constats préliminaires (Tests)**

- **Forces**
  - Base solide fournie par Breeze pour les tests d’auth.
  - Utilisation de `RefreshDatabase` pour les Feature tests principaux (profil).

- **Points de vigilance**
  - Couverture très faible des **cas métier critiques** : onboarding, abonnement, payments, multi-tenancy par coach, support, etc.
  - Absence de tests unitaires ciblant les services d’intégration (LemonSqueezyService, FungiesService) ou les calculs métier (complétion de profil, gestion des statuts d’abonnement).

### 5.7 Opérations, CI/CD & déploiement

#### 5.7.1 Logs & observabilité

- `config/logging.php` : configuration Laravel standard avec canaux `stack`, `single`, `daily`, `slack`, `papertrail`, `stderr`, `syslog`, etc.
- De nombreux contrôleurs utilisent `Log::info`, `Log::warning`, `Log::error` autour des appels externes (webhooks, paiements, checkout), ce qui est **positif pour le diagnostic**.
- Pas d’intégration APM ou monitoring externe explicitement visible dans le code (ex. Sentry, NewRelic…).

#### 5.7.2 Sauvegardes & maintenance

- Présence de `spatie/laravel-backup` dans `composer.json` et de `config/backup.php` (non détaillé ici) : indique la volonté d’avoir des **sauvegardes automatisées**.
- Des scripts ou guides spécifiques (ex. `CHECKLIST-FUNGIES-ACTIVATION.md`, scripts de déploiement onboarding) existent dans la racine, à auditer plus finement pour comprendre les procédures d’exploitation.

#### 5.7.3 CI/CD & scripts

- `composer.json` définit un script `dev` qui lance en parallèle :
  - `php artisan serve`
  - `php artisan queue:listen`
  - `php artisan pail` (logs temps réel)
  - `npm run dev` (Vite)
  → Bon support pour un environnement **dev intégré**.
- Des scripts de déploiement spécifiques (`deploy-onboarding.bat`, `deploy-onboarding.sh`) sont présents et devront être audités pour :
  - vérifier les étapes exécutées (migrations, caches, assets, queues, backups),
  - s’assurer de la robustesse en cas d’échec partiel (rollback, reprise).

**Constats préliminaires (Opérations/CI-CD)**

- **Forces**
  - Logging relativement riche sur les points sensibles (paiements, webhooks, onboarding).
  - Outil de backup Spatie intégré, qui, bien configuré, peut couvrir une bonne partie des besoins de sauvegarde.
  - Scripts de développement locaux facilitant la vie des devs.

- **Points de vigilance**
  - Absence d’information explicite sur un pipeline CI/CD (GitHub Actions, GitLab CI, etc.) dans le repo.
  - Nécessité de vérifier les scripts de déploiement et la politique de backups/restores dans un contexte RGPD.

## 6. Recommandations & priorisation

### 6.1 Quick wins

Objectif : réduire rapidement le risque et améliorer l’expérience sans refonte majeure.

- **[Sécurité / Logs]** Limiter les données sensibles dans les logs
  - Revoir les `Log::error()` et `Log::info()` liés aux webhooks/paiements pour éviter de logger des payloads complets (données perso, numéros de TVA, etc.).
  - Introduire un helper de log qui **masque/anonymise** les champs sensibles.

- **[Architecture]** Unifier l’injection des services externes
  - Remplacer les `new LemonSqueezyService()` par **injection via constructeur** (DI Laravel) pour harmoniser avec les webhooks et faciliter les tests.
  - Appliquer la même approche aux autres services (Fungies, etc.).

- **[DB]** Clarifier la migration `add_fungies_fields_to_users_table`
  - Documenter dans le code ou supprimer la migration vide, en s’assurant que tous les environnements utilisent la même version du schéma.

- **[UX]** Mieux exposer les erreurs critiques côté UI
  - Vérifier que les messages d’erreur renvoyés par les contrôleurs (paiement, onboarding, webhooks) sont **traduits en retours clairs** dans les pages Vue (flash `success`/`error`, validation visuelle sur les formulaires).

- **[Tests]** Étendre légèrement la couverture sur des cas simples
  - Ajouter au moins 1–2 tests Feature rapides pour :
    - la redirection onboarding/setup (`EnsureOnboardingCompleted`, `EnsureSetupCompleted`),
    - l’accès admin protégé par `IsAdmin`.

### 6.2 Chantiers moyens terme

Objectif : renforcer la robustesse sur 1–3 sprints sans réécrire toute l’application.

- **[Sécurité / RGPD]** Opérationnaliser le RGPD
  - Mettre en place des **commandes ou écrans** pour :
    - exporter les données complètes d’un coach (User + Coach + Clients + messages + abonnements),
    - supprimer/anonymiser un coach et ses clients à la demande.
  - Aligner la politique de logs et de backups (durées de rétention, anonymisation) avec `annexe RGPD.md`.

- **[Architecture]** Extraire la logique métier des gros contrôleurs
  - Identifier 2–3 flux critiques (onboarding, création de checkout, traitement des webhooks) et extraire la logique dans des **services / actions** dédiés.
  - Exemple : `CreateCheckoutSessionAction`, `HandleLemonSqueezySubscriptionCreated`, `CompleteOnboardingForUser`.

- **[Performance]** Introduire les queues sur les tâches lourdes
  - Basculer l’envoi des emails (ex. lien de paiement FEA) dans des **jobs en queue**.
  - Envisager de traiter une partie des traitements webhooks via des jobs (validation légère en temps réel, travail lourd en arrière-plan).

- **[Frontend]** Démanteler les gros composants Vue
  - Découper `Dashboard.vue` et `Welcome.vue` en **sous-composants** (widgets, sections) pour améliorer lisibilité, tests et performances.
  - Mettre en place une organisation claire par **domaines** (Onboarding, Setup, Dashboard, SitePublic) avec composants réutilisables.

- **[Tests]** Cibler quelques scénarios métier clés
  - Ajouter des tests Feature couvrant :
    - un parcours d’onboarding complet (FEA vs non-FEA, avec et sans promo),
    - au moins un flux d’abonnement via Lemon Squeezy (création de checkout, traitement du webhook de souscription, annulation / fin de période),
    - affichage du site public d’un coach et envoi d’un message de contact.

### 6.3 Chantiers structurants

Objectif : préparer la plateforme à une montée en charge et à une évolution produit significative.

- **[Modèle de données]** Réduire la taille de la table `coaches`
  - Envisager une **refactorisation vers plusieurs tables** :
    - `coach_branding` (couleurs, logo, layout),
    - `coach_marketing_sections` (hero, méthode, CTA, pricing, transformations, etc.),
    - éventuellement tables spécifiques par grande zone (méthode, pricing, CTA) si la complexité augmente.
  - Avantage : meilleure maintenabilité, possibilité d’évoluer certaines zones sans toucher à toute la table.

- **[Domaine]** Clarifier les Bounded Contexts
  - Formaliser les domaines : **Identité & comptes**, **Onboarding**, **Site coach**, **Abonnements**, **Support**, **Clients/CRM léger**.
  - Adapter la structure du code (`app/`) pour refléter ces contextes (ex. dossiers par domaine, services dédiés par contexte).

- **[Observabilité & CI/CD]** Mettre en place un pipeline complet
  - Introduire une CI (GitHub Actions, GitLab CI…) pour :
    - lancer tests PHP + JS à chaque PR,
    - exécuter linters (Laravel Pint, ESLint/Prettier) et éventuellement PHPStan/Larastan.
  - Ajouter un outil de monitoring/erreur (Sentry ou équivalent) pour les exceptions en prod.

- **[Expérience produit]** Concevoir une stratégie de performance front & SEO pour les sites coachs
  - Étudier la possibilité de **pré-rendre** certaines pages publiques (SSR, static generation) ou de mettre en place un **cache HTTP** ciblé.
  - Améliorer systématiquement les temps de chargement des sites coachs (taille bundle, images, critical CSS).

## 7. Plan d’action proposé

### 7.1 Phase 1 – Sécurisation rapide & hygiène (1–2 semaines)

- **Sécurité & logs**
  - Auditer et réduire les données sensibles dans les logs (webhooks, paiements, onboarding).
  - Vérifier les slugs coach (unicité, format, collisions) et documenter les règles.

- **Architecture légère**
  - Unifier l’injection des services externes (LemonSqueezy, Fungies) via DI.

- **Tests minimums**
  - Ajouter quelques tests Feature pour les middlewares clés (`IsAdmin`, `EnsureOnboardingCompleted`, `EnsureSetupCompleted`).

### 7.2 Phase 2 – Renforcement métier & UX (2–4 semaines)

- **Onboarding & abonnements**
  - Écrire des tests de bout en bout sur les parcours critiques (onboarding FEA/non-FEA, création d’abonnement, annulation).
  - Passer les emails et une partie du traitement webhook en **jobs**.

- **Frontend & UX**
  - Refactor des composants Vue principaux (Dashboard, Welcome) en sous-composants.
  - Améliorer la gestion des erreurs et des états de chargement côté UI.

- **RGPD (premier niveau)**
  - Documenter les durées de rétention des logs et des backups.
  - Préparer les premiers outils (manuels ou commandes artisan) pour exporter/supprimer les données d’un utilisateur.

### 7.3 Phase 3 – Structuration & scalabilité (4+ semaines)

- **Refonte progressive du modèle `Coach`**
  - Concevoir la nouvelle structure de données (tables dédiées) pour le contenu marketing/coaching.
  - Mettre en place des migrations de transition (coexistence ancien/nouveau schéma, scripts de migration de données).

- **Domaine & codebase**
  - Restructurer `app/` en domaines explicites (Onboarding, Subscription, CoachSite, Support, etc.).
  - Extraire la logique métier complexe dans des services/actions et réduire la taille des contrôleurs.

- **CI/CD & observabilité**
  - Mettre en place un pipeline CI : tests automatiques, linters, éventuellement analyzers statiques.
  - Intégrer un outil d’erreur/monitoring coté prod.

### 7.4 Suivi & réévaluation

- À la fin de chaque phase, **réévaluer** :
  - la dette résiduelle (technique, sécu, perf),
  - les nouveaux besoins métier (évolution produit, nouvelles intégrations),
  - les priorités de la phase suivante.

- Mettre à jour `audit1.md` et les `auditN.md` thématiques pour garder une **trace vivante** de l’état de la plateforme et des décisions d’architecture.

