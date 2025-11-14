# Ignite Coach – Documentation fonctionnelle et technique de la plateforme

> **Nom marketing : Ignite Coach**  
> **Nom du repository / codebase : FEA-COACH**  
> Plateforme SaaS multi-tenant pour coachs sportifs permettant à chaque coach davoir son propre site public personnalisé et un dashboard de gestion.

---

## 1. Vue densemble

- **Objectif principal**  
  Permettre à un coach sportif de disposer en quelques minutes dun site web professionnel, hébergé sur un sous-domaine dédié, entièrement personnalisable via un dashboard.

- **Cibles**  
  - **Coach sportif** : gère son contenu, ses couleurs, ses images, ses plans tarifaires, ses FAQ, etc.  
  - **Visiteur** : consulte le site public du coach, découvre loffre, les transformations, la FAQ et contacte le coach.  
  - **Administrateur** : gère les coachs, leurs comptes utilisateurs, leurs sous-domaines, les demandes de promo FEA, etc.

- **Type dapplication**  
  - Backend Laravel (API + vues Blade)  
  - Dashboard SPA-like avec Inertia.js + Vue 3  
  - Sites publics générés en Blade, stylés avec TailwindCSS + Alpine.js

---

## 2. Rôles et types dutilisateurs

- **Visiteur anonyme**
  - Accède uniquement aux sites publics des coachs (ex : `pierre-martin.localhost:8000`).
  - Peut lire tout le contenu public : hero, à propos, méthode, plans, transformations, FAQ, CTA.

- **Coach**
  - Possède un **compte utilisateur** attaché à un **profil coach** (`coach_id`).
  - Accède au dashboard (routes `/dashboard/*`).
  - Rôle généralement `coach` dans le modèle `User`.

- **Administrateur**
  - Rôle `admin` dans `User`.
  - Accès au panel `/admin/*` protégé par middleware `IsAdmin`.
  - Peut créer/éditer/supprimer des coachs, gérer leurs sous-domaines, activer/désactiver, etc.
  - Exempté du middleware donboarding.

- **Coach diplômé FEA vs non-diplômé** (onboarding)
  - Attribut `is_fea_graduate` sur `users`.
  - Conditionne la 3ᵉ étape donboarding :
    - Diplômé FEA → flux de **code promo FEA**.
    - Non diplômé → flux **paiement Stripe** (29€/mois, à intégrer côté Stripe UI).

---

## 3. Architecture technique

- **Backend**
  - Laravel 11 (PHP 8.2+).
  - Base de données MySQL/MariaDB, **single database multi-tenant** (toutes les données pour tous les coachs dans la même base, filtrées par `coach_id`).
  - Packages clés :
    - `spatie/laravel-medialibrary` : gestion médias (logo, hero, photo de profil, transformations).
    - `spatie/laravel-activitylog` : logs dactivité.
    - `spatie/laravel-backup` : sauvegardes de la base et des fichiers.
    - `laravel/breeze` + `inertiajs/inertia-laravel` : auth + frontend SPA.

- **Frontend dashboard**
  - Inertia.js + Vue 3.
  - Design moderne, mode sombre supporté.
  - Pages typiques :
    - `/dashboard` (vue densemble + stats).
    - `/dashboard/content` (contenu du site public, FAQ, photo de profil, stats, méthode, CTA, etc.).
    - `/dashboard/plans` (plans tarifaires du coach).
    - `/dashboard/faq` (page historique, la gestion complète étant également intégrée à `/dashboard/content`).

- **Frontend public**
  - Blade + TailwindCSS + Alpine.js.
  - Design responsive, mobile-first.
  - CSS variables pour les couleurs dynamiques (primaire/secondaire) dérivées du profil du coach.

- **Multi-tenancy**
  - Pattern : single DB, filtrage par `coach_id`.
  - Middleware (ex : `ResolveCoachFromHost`) détecte le coach en fonction du **sous-domaine**.
  - Exemple : `pierre-martin.localhost:8000` → le middleware charge le coach `slug = 'pierre-martin'`.

---

## 4. Gestion des domaines et sous-domaines

- **Variable denvironnement `APP_DOMAIN`**
  - Configure le domaine racine pour les URLs publiques.
  - Exemples :
    - Dev : `APP_DOMAIN=localhost:8000`
    - Prod : `APP_DOMAIN=kineseducation.academy` (ou autre domaine réel)

- **URLs publiques**
  - Format : `{coach-slug}.{APP_DOMAIN}`.
  - Local : `pierre-martin.localhost:8000`.
  - Prod : `pierre-martin.kineseducation.academy`.

- **Lien "Voir le site" dans le dashboard**
  - Le middleware Inertia partage `appDomain` dans toutes les pages.
  - Le dashboard construit automatiquement lURL du site public à partir du `slug` du coach et de `appDomain`.

- **Configuration DNS & Nginx en production**
  - DNS wildcard : `*.domain.com` pointant vers le serveur.
  - Bloc Nginx utilisant une regex `server_name ~^(?<subdomain>.+)\.domain\.com$;` qui route vers la même app Laravel.

---

## 5. Modèle de données (vue fonctionnelle)

### 5.1 Table `users`

- Champs principaux liés à Ignite Coach :
  - `id`, `name`, `email`, `password`.
  - `role` : `admin` ou `coach`.
  - `coach_id` : lien vers le profil du coach (nullable pour un admin pur).
  - **Onboarding** :
    - `first_name`, `last_name`.
    - `vat_number` (optionnel).
    - `legal_address` (texte complet).
    - `is_fea_graduate` (booléen).
    - `fea_promo_code` (code type `FEA-XXXXX` lorsque le compte est activé via FEA).
    - `onboarding_completed` (booléen, conditionne laccès au dashboard).
    - `stripe_customer_id` (identifiant Stripe, pour abonnement payant non-diplômés FEA).
    - `subscription_status` (ex : `active_promo`, `active_paid`, `canceled`, etc.).

### 5.2 Table `coaches`

- **Identité & multi-tenancy**
  - `id`.
  - `slug` : utilisé pour les sous-domaines.
  - `is_active` : coach actif / site public accessible ou non.

- **Branding**
  - Couleurs (primaire, secondaire, éventuellement variantes) utilisées par les CSS variables.
  - Logo, image hero et **photo de profil** gérés via Spatie Media Library (collections médias, une seule photo de profil active).

- **Contenu texte principal** (page /dashboard/content)
  - Hero :
    - `hero_title`
    - `hero_subtitle`
  - À propos :
    - `about_text`
  - Méthode :
    - `method_title`
    - `method_subtitle`
    - `method_text` (description générale)
    - `method_step1_title`, `method_step1_description`
    - `method_step2_title`, `method_step2_description`
    - `method_step3_title`, `method_step3_description`
  - CTA principal :
    - `cta_text` (texte du bouton principal, utilisé dans la section CTA et réutilisé pour certains boutons).

- **Statistiques personnalisables** (section Statistiques)
  - `satisfaction_rate` (entier %, 0–100, par défaut 100).
  - `average_rating` (décimal 0–5 avec 1 décimale, par défaut 5.0).
  - Affichés dans la section "À propos" du site public.

- **Section Tarifs**
  - `pricing_title` (titre de la section tarifs).
  - `pricing_subtitle` (sous-titre).
  - Ne modifient **pas** les plans eux-mêmes, uniquement le header de section.

- **Section Transformations**
  - `transformations_title`.
  - `transformations_subtitle`.
  - Ne modifient pas les éléments de transformations individuellement, uniquement le header de section.

- **Section Appel à laction finale**
  - `final_cta_title`.
  - `final_cta_subtitle` (max ~500 caractères).
  - Texte affiché dans le dernier bloc avant le footer, avec fond en dégradé.
  - Le texte du bouton reste `cta_text`.

### 5.3 Table `plans`

- Reliée à `coaches` (clé `coach_id`).
- Champs principaux :
  - `name` (nom du plan).
  - `description`.
  - `price` (optionnel, libre – peut être une valeur num ou string formatée selon limplémentation actuelle).
  - `cta_url` (URL de prise de rendez-vous, ex : Calendly).
  - `is_active` (booléen : uniquement les plans actifs sont affichés sur le site public).

### 5.4 Table `coach_transformations`

- Galerie de transformations avant/après du coach.
- Champs typiques : `title`, `description`, images avant/après via Media Library, ordre daffichage, statut.
- Affichées dans la section "Transformations" du site public.

### 5.5 Table `faqs`

- Reliée à `coaches` (clé `coach_id`).
- Champs :
  - `question`.
  - `answer`.
  - `order` (ordre daffichage croissant).
  - `is_active` (FAQ visible sur le site public ou non).
- Sur le site public :
  - Seules les FAQ actives sont visibles.
  - Tri par `order`, puis par date de création.

### 5.6 Autres tables importantes

- `media` : gérée par Spatie Media Library (logo, hero, photo de profil, transformations, etc.).
- `activity_log` : logs dactivité (qui a modifié quoi et quand).
- Tables de jobs / queues et de backup gérées par les packages Spatie.

---

## 6. Flux fonctionnels principaux

### 6.1 Onboarding dun nouveau coach

Lonboarding remplace linscription classique. En pratique :

1. **Création du compte par un admin**
   - Ladmin crée un coach via `/admin/coaches` (panel dadministration).
   - Le système crée automatiquement un utilisateur associé (rôle `coach`, `coach_id` pointant vers le coach).
   - Un email/password par défaut peut être définis (voir seeders ou conventions internes).

2. **Première connexion du coach**
   - Le coach se connecte via `/login` (page login stylée Ignite Coach).
   - Si `onboarding_completed = false`, le middleware dédié le redirige vers `/onboarding/step1`.
   - Les admins sont exemptés de ce middleware.

3. **Étape 1  Choix du type de compte** (`/onboarding/step1`)
   - Deux cartes :
     - "Je suis diplômé FEA".
     - "Je ne suis pas diplômé FEA".
   - Le choix est enregistré dans `is_fea_graduate`.

4. **Étape 2  Informations personnelles** (`/onboarding/step2`)
   - Formulaire complet : prénom, nom, email, TVA (optionnel), adresse légale.
   - Sauvegardé dans le modèle `User`.

5. **Étape 3 FEA  Demande de code promo** (`/onboarding/step3` si `is_fea_graduate = true`)

   - Lobjectif est dactiver le compte avec un code du type `FEA-XXXXXXXX` et doffrir une période promo.
   - Le système utilise un **workflow de demande de code promo** :
     - Le coach ne saisit pas directement un code valide, mais **dépose une demande** avec un message.
     - Une entité de type `PromoCodeRequest` (ou équivalent) stocke : user, message, statut (`pending`, `approved`, `rejected`), commentaires admin, etc.

   - **États possibles côté coach (Step3.vue)** :
     - **Aucune demande existante** :
       - Affichage dun formulaire avec champ message + bouton "Demander un code".
     - **Statut `pending`** :
       - Message "Demande en cours de vérification".
       - Rappel du message envoyé et de la date de la demande.
       - Indication "Vous recevrez un email".
       - **Protection contre doublons** : la fonction `requestPromoCode()` empêche de créer une nouvelle demande tant quune demande est en `pending`. Message derreur : "Vous avez déjà une demande en cours de traitement".
     - **Statut `approved`** :
       - Message de succès "Compte activé !".
       - Redirection automatique vers le dashboard après quelques secondes.
     - **Statut `rejected`** :
       - Message "Demande non approuvée".
       - Affichage de la raison fournie par ladmin.
       - Bouton "Faire une nouvelle demande" permettant de recréer une demande.

   - **Traitement admin de la demande** (`PromoCodeRequestController::approve()`)
     - Ladmin voit la liste des demandes dans une interface dédiée (`/admin/promo-requests`).
     - Sur approbation :
       - Génération dun code `FEA-XXXXXXXX`.
       - Mise à jour du statut de la demande : `approved`.
       - **Activation automatique du compte utilisateur** :
         - `fea_promo_code` mis à jour.
         - `subscription_status` passé à `active_promo`.
         - `onboarding_completed` = `true`.
       - **Création automatique du profil Coach** si inexistant (crée la fiche coach + liaison `coach_id`).
       - Le coach est redirigé vers le dashboard lors de son retour sur Step3.

6. **Étape 3 non-FEA  Paiement Stripe** (`/onboarding/step3` si `is_fea_graduate = false`)

   - Un formulaire de paiement Stripe (29€/mois) est prévu.
   - Le contrôleur `OnboardingController` dispose dune méthode `processPayment()` prévue pour :
     - Créer/attacher un `stripe_customer_id`.
     - Mettre à jour `subscription_status` (type `active_paid`).
     - Marquer `onboarding_completed = true`.
   - **État actuel** : lintégration Stripe côté frontend (Stripe Elements) et le flux complet sont à finaliser.

7. **Redirection après onboarding**
   - Une fois `onboarding_completed = true`, lutilisateur coach est redirigé vers `/dashboard`.
   - La navbar (ex : `Welcome.vue`) adapte dynamiquement son label :
     - Si connecté et `onboarding_completed = false` → lien "Continuer mon inscription" vers `onboarding.step1`.
     - Si connecté et `onboarding_completed = true` → lien "Dashboard" vers `/dashboard`.

### 6.2 Connexion & authentification

- Login via `/login` (page `Login.vue` custom Ignite Coach) :
  - Design moderne : gradient, card glassmorphism, focus states, loader, messages derreur stylisés.
  - Liens dinscription classique supprimés (register désactivé).
  - Lien "Retour à laccueil" vers la landing page publique.

- Inscription directe :
  - Route `register` désactivée ou redirigée vers lonboarding.
  - Les comptes sont normalement créés par ladmin ou via des mécanismes contrôlés.

### 6.3 Dashboard coach

#### 6.3.1 Page `/dashboard` (vue densemble)

- Contrôleur dédié (DashboardController) charge :
  - Profil du coach (`Coach` + relations : plans actifs, transformations, etc.).
  - Taux de complétion du profil basé sur ~10 critères (ex : logo, couleurs, hero, à propos, méthode, CTA, photo de profil).

- Interface :
  - Cartes de statistiques :
    - Complétion du profil avec barre de progression.
    - Nombre de plans actifs / total.
    - Nombre de transformations.
    - Statut du site (actif/inactif, visibilité publique).
  - Messages dalerte si le profil coach est manquant ou incomplet.

#### 6.3.2 Page `/dashboard/content` (contenu du site + FAQ + photo de profil)

Cette page est le **centre de configuration** du site public.

- **En-tête**
  - Carte de progression avec barre animée (0–100%) calculée à partir de 5 champs clés : `hero_title`, `hero_subtitle`, `about_text`, `method_text`, `cta_text`.
  - Message flash de succès après sauvegarde.
  - Info card avec conseils généraux.

- **Section Hero** (couleur indigo)
  - Champs : titre & sous-titre.
  - Compteur de caractères en temps réel.
  - Conseils sur le ton (impactant et clair).

- **Section Photo de profil**
  - Upload via Spatie Media Library, collection `profile` (single file).
  - UI :
    - Preview circulaire (128x128) de la photo actuelle ou fallback.
    - Bouton "Choisir une photo" puis "Enregistrer la photo" (affiché uniquement après sélection).
    - Bouton de suppression (croix rouge) en overlay sur la photo.
    - Tips : format carré, max 2MB, JPG/PNG/WEBP.
  - Actions :
    - POST `/dashboard/content/profile-photo` → upload & remplacement.
    - DELETE `/dashboard/content/profile-photo` → suppression.

- **Section À propos** (vert)
  - Champ `about_text` (textarea avec compteur de caractères).
  - Conseils : parler de lexpertise, du parcours, de la niche, etc.

- **Section Méthode** (violet)
  - Champs : `method_title`, `method_subtitle`, `method_text`.
  - Sous-section "Les 3 étapes" :
    - 3 cartes, chacune avec `method_stepX_title` + `method_stepX_description`.
    - Placeholders par défaut (Évaluation, Plan daction, Accompagnement).

- **Section Statistiques** (bleu)
  - Champs :
    - `satisfaction_rate` (0–100).
    - `average_rating` (0–5, step 0.1).
  - Preview :
    - Affichage "X% satisfaits".
    - Affichage "X★ note moyenne".
  - Tips : rester honnête et crédible.

- **Section Tarifs**
  - Champs : `pricing_title`, `pricing_subtitle`.
  - Texte uniquement, les plans sont gérés séparément dans `/dashboard/plans`.

- **Section Transformations**
  - Champs : `transformations_title`, `transformations_subtitle`.
  - Texte uniquement, les transformations sont gérées dans le menu Transformations.

- **Section Appel à laction finale** (CTA final)
  - Champs : `final_cta_title`, `final_cta_subtitle`.
  - Cette section apparaît juste avant le footer du site public, sur fond gradient primary → secondary.
  - Le texte du bouton reste `cta_text` (défini dans la section CTA principale).

- **Section FAQ (gestion complète intégrée)**
  - Tableau ou grille listant toutes les FAQ du coach (actives ou non).
  - Statistiques : nombre total, nombre actives, statut global.
  - Actions :
    - Bouton "Nouvelle question" → ouvre un modal pour créer une FAQ (question, réponse, ordre, actif ou non).
    - Bouton "Modifier" → ouvre le même modal prérempli.
    - Bouton "Supprimer" → confirmation puis suppression.
  - Rechargement partiel via Inertia (seules `faqs` et les compteurs sont rechargés).
  - La page `/dashboard/faq` existe, mais lintention est de pouvoir tout gérer depuis `/dashboard/content`.

- **Preview temps réel**
  - Section de preview synthétique présentant : hero, sous-titre, CTA et statistiques de complétion.
  - Permet au coach de visualiser limpact de ses modifications avant daller voir le site public.

#### 6.3.3 Page `/dashboard/plans` (plans tarifaires)

- Gestion complète des plans dabonnement ou doffres commerciales du coach.
- Fonctionnalités :
  - Création de plan : nom, description, prix (optionnel), URL CTA (souvent un lien Calendly), statut actif/inactif.
  - Modification : édition de tous les champs.
  - Suppression avec confirmation.
  - Affichage en grille responsive (1/2/3 colonnes selon la taille décran).
  - Modal pour création/édition.
  - Sécurité : chaque plan appartient à un coach, les contrôleurs vérifient que le `plan->coach_id` correspond bien au coach connecté.

- Impact site public :
  - Seuls les plans **actifs** sont affichés dans la section "Tarifs".
  - Le titre et sous-titre de la section viennent des champs `pricing_title` / `pricing_subtitle` du coach.

#### 6.3.4 Transformations (menu Transformations)

- Page dédiée pour gérer les transformations avant/après :
  - Ajout dune transformation avec : titre, description, images avant/après (upload via Media Library), ordre, statut.
  - Suppression avec confirmation.
  - Les transformations actives sont affichées dans la section "Transformations" du site public.

### 6.4 Site public du coach

- Généré via Blade, avec une vue principale type `coach-site/index.blade.php`.
- Sections typiques :
  - **Hero** : titre, sous-titre, CTA, catégories, arrière-plan hero.
  - **À propos** : texte riche + stats (transformations, satisfaction_rate, average_rating).
  - **Méthode** : titre, sous-titre, description, 3 étapes avec icônes.
  - **Tarifs** :
    - Header : `pricing_title` + `pricing_subtitle`.
    - Grille de plans actifs (`plans` du coach).
  - **Transformations** :
    - Header : `transformations_title` + `transformations_subtitle`.
    - Cartes de transformations avant/après.
  - **FAQ** :
    - Accordéon Alpine.js listant les FAQ actives triées par `order` puis date.
  - **CTA final** :
    - Fond gradient, texte `final_cta_title` + `final_cta_subtitle`.
    - Bouton utilisant `cta_text` pour inviter à passer à laction.

- Tous ces contenus sont dynamiques et proviennent des données du `Coach` et de ses relations (`plans`, `faqs`, `transformations`).

### 6.5 Panel dadministration `/admin`

- Accès réservé aux utilisateurs `role = admin` via middleware `IsAdmin`.
- Principales fonctionnalités :

- **Gestion des coachs** (`/admin/coaches`)
  - Liste de tous les coachs avec statut actif/inactif, sous-domaine, propriétaire, etc.
  - Création :
    - Création du coach (nom, slug/sous-domaine, couleurs, etc.).
    - Création automatique dun utilisateur associé avec rôle `coach`.
  - Édition :
    - Modification des informations du coach et éventuellement de lutilisateur.
  - Suppression.
  - Bannière admin dans le dashboard principal pour signaler que lutilisateur actuel est en mode admin.

- **Gestion des demandes de code promo FEA** (`/admin/promo-requests`)
  - Liste toutes les demandes en `pending`, `approved`, `rejected`.
  - Approve / Reject :
    - Approve → génère un code FEA, active le compte, complète lonboarding, crée le profil coach si besoin.
    - Reject → enregistre une raison, affichée ensuite à lutilisateur dans Step3.

---

## 7. Sécurité et droits daccès

- **Authentification**
  - Basée sur Laravel Breeze + Sanctum.
  - CSRF, XSS, validation des inputs.

- **Rôles**
  - `admin` : accès aux routes `/admin/*` + dashboard.
  - `coach` : accès au dashboard (`/dashboard/*`), bloqué si `onboarding_completed = false`.

- **Middlewares clés**
  - `auth` : protège la plupart des routes dashboard & admin.
  - `IsAdmin` : restreint les routes `/admin` aux admins.
  - Middleware type `EnsureOnboardingCompleted` : bloque laccès au dashboard tant que lonboarding nest pas terminé (sauf pour les admins).
  - Middleware de résolution de coach depuis le host (multi-tenant).

- **Séparation des données**
  - Toutes les entités liées à un coach contiennent un `coach_id`.
  - Les contrôleurs vérifient systématiquement que les ressources manipulées appartiennent bien au coach connecté.

---

## 8. Sauvegardes, logs et observabilité

- **Sauvegardes** (Spatie Backup)
  - Sauvegardes de la base + fichiers.
  - Commandes artisan préconfigurées (`backup:run`, etc.).

- **Logs dactivité** (Spatie Activity Log)
  - Journalisation des actions critiques (création, modification, suppression de ressources sensibles).
  - Possibilité dauditer qui a modifié un contenu et quand.

- **Logs applicatifs**
  - Classiques logs Laravel dans `storage/logs`.

---

## 9. Configuration, environnements et déploiement

Cette section résume les points clés. Voir le `README.md` et `DOMAIN-CONFIG.md` pour le pas-à-pas complet.

- **Environnement de développement**
  - `php artisan migrate:fresh --seed` pour créer la BDD + comptes de test.
  - `npm run dev` pour le frontend, `php artisan serve` pour le backend.
  - Ajout des sous-domaines de test dans le fichier hosts Windows (ex : `pierre-martin.localhost`).

- **Environnement de production**
  - Serveur Linux (Ubuntu 22.04 recommandé) avec Nginx, PHP-FPM, MySQL, Redis, Supervisor.
  - DNS wildcard + certificat SSL wildcard.

- **Configuration multi-tenant**
  - `APP_DOMAIN` défini dans `.env` (ex : `kineseducation.academy`).
  - Nginx configuré avec `server_name ~^(?<subdomain>.+)\.domain\.com$;`.

---

## 10. Ajouter une nouvelle fonctionnalité côté coach

Résumé des bonnes pratiques pour étendre Ignite Coach.

- **1. Modèle & migration**
  - Créer une migration avec les colonnes nécessaires (liées à `coach_id` si cest spécifique au coach).
  - Mettre à jour le modèle correspondant (`$fillable`, casts, relations).

- **2. Contrôleur**
  - Créer un contrôleur dans `app/Http/Controllers/Dashboard`.
  - Toujours filtrer les ressources par `coach_id` du coach connecté.

- **3. Routes**
  - Ajouter des routes dans `routes/web.php` sous un prefix `/dashboard` + middleware `auth`.

- **4. Vue Inertia**
  - Créer une page Vue 3 dans `resources/js/Pages/Dashboard` ou un sous-dossier.
  - Respecter le design existant (cards, mode sombre, feedback, modals Inertia).

- **5. Intégration au site public**
  - Si la fonctionnalité doit être visible sur le site public, mettre à jour le `CoachSiteController` (ou contrôleur équivalent) pour charger les nouvelles données.
  - Adapter la vue Blade `coach-site/index.blade.php` pour rendre ces données.

---

## 11. Résumé

Ignite Coach est une plateforme SaaS multi-tenant pour coachs sportifs, construite sur Laravel + Inertia + Vue. 

Elle fournit :

- **Un onboarding structuré** pour les coachs (diplômés FEA ou non) avec gestion de codes promo et future intégration Stripe.
- **Un dashboard riche** permettant de gérer : branding, contenus, photo de profil, statistiques, méthode, plans, transformations et FAQ.
- **Un site public moderne** pour chaque coach, entièrement alimenté par les données du dashboard.
- **Un panel d'administration** pour gérer les coachs, leurs sous-domaines et les demandes de code promo.

Cette documentation offre une vision densemble du fonctionnement fonctionnel et technique. Pour entrer dans le détail dune zone précise (ex : FAQ, plans, onboarding), se référer également aux fichiers de contrôleurs, aux migrations correspondantes ainsi quaux documents spécifiques dans le dossier `doc/` lorsquils existent.
