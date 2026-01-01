# Vue Coach Panel 

Inventaire fonctionnel des vues liées au panel coach (classiques et beta), pour servir de base à un redesign UI/UX ultérieur.

## 1. Dashboard global

### 1.1 `Dashboard` (classique)
- **Chemin** : `resources/js/Pages/Dashboard.vue`
- **Contrôleur** : `DashboardController@index`
- **Route** : `GET /dashboard` → `dashboard`
- **Rôle** :
  - Affiche le tableau de bord principal côté coach.
  - Statistiques de profil, dernières transformations, navigation vers les sous-vues (contenu, branding, etc.).
  - Gère aussi un mode admin simplifié.

### 1.2 `Coach/DashboardCoachBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/DashboardCoachBeta.vue`
- **Contrôleur** : `DashboardController@beta`
- **Route** : `GET /dashboard-coach-beta` → `dashboard.coach.beta`
- **Rôle** :
  - Nouveau panel coach beta avec sidebar par catégories :
    - **Général / Aperçu** : stats, transformations récentes.
    - **Site vitrine** : Branding, Contenu, Galerie, FAQ.
    - **Business** : Plans, Clients, Mentions légales, Messages.
    - **Compte** : Abonnement, Support, Profil.
  - Utilise `?beta=1` sur tous les liens vers les sous-vues.
  - Top bar : accès rapide au site vitrine, abonnement, etc.

## 2. Site vitrine

### 2.1 Branding

#### 2.1.1 `Dashboard/Branding` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Branding.vue`
- **Contrôleur** : `Dashboard\BrandingController@edit/update`
- **Routes** :
  - `GET /dashboard/branding` → `dashboard.branding`
  - `POST /dashboard/branding` → `dashboard.branding.update`
- **Fonctionnalités** :
  - Choix des couleurs primaires / secondaires.
  - Upload du logo et de l’image hero (médias `logo` et `hero`).
  - Choix du layout du site vitrine (layouts disponibles + layout par défaut).

#### 2.1.2 `Coach/BrandingBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/BrandingBeta.vue`
- **Contrôleur** : `BrandingController@edit/update` (rend cette vue si `beta=1`)
- **Spécificités** :
  - Layout full-screen sombre cohérent avec `DashboardCoachBeta`.
  - Même formulaire que la vue classique.
  - Envoi du formulaire vers `dashboard.branding.update` avec `beta=1`.

### 2.2 Contenu

#### 2.2.1 `Dashboard/Content` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Content.vue`
- **Contrôleur** : `Dashboard\ContentController@edit/update/uploadProfilePhoto/deleteProfilePhoto`
- **Routes** :
  - `GET /dashboard/content` → `dashboard.content`
  - `POST /dashboard/content` → `dashboard.content.update`
  - `POST /dashboard/content/profile-photo` → `dashboard.content.profile-photo.upload`
  - `DELETE /dashboard/content/profile-photo` → `dashboard.content.profile-photo.delete`
- **Fonctionnalités** :
  - Gestion du **texte de la page vitrine** :
    - Section hero (titre, sous-titre).
    - Section "À propos".
    - Section méthode (titre/sous-titre, 3 étapes avec titres + descriptions).
    - Titre / sous-titre des sections tarifs & transformations.
    - Section CTA finale (titre/sous-titre).
    - Texte du bouton principal + CTA intermédiaire (titre/sous-titre).
  - **Statistiques affichées** sur le site : taux de satisfaction, note moyenne.
  - **Liens de réseaux sociaux** : Facebook, Instagram, Twitter/X, LinkedIn, YouTube, TikTok.
  - Gestion de la **photo de profil coach** (upload, preview, suppression).
  - Gestion intégrée d’un mini-module FAQ (via routes `dashboard.faq.*`).

#### 2.2.2 `Coach/ContentBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/ContentBeta.vue`
- **Contrôleur** : `ContentController@edit/update/uploadProfilePhoto/deleteProfilePhoto` (rend cette vue si `beta=1`)
- **Spécificités** :
  - UI remaniée en layout sombre, cards, vue de progression (`completionPercentage`).
  - Même structure de contenu que la vue classique.
  - Toutes les requêtes vers les routes `dashboard.content.*` et `dashboard.faq.*` incluent `beta=1`.

### 2.3 Galerie

#### 2.3.1 `Dashboard/Gallery` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Gallery.vue`
- **Contrôleur** : `Dashboard\GalleryController@index/store/destroy`
- **Routes** :
  - `GET /dashboard/gallery` → `dashboard.gallery`
  - `POST /dashboard/gallery` → `dashboard.gallery.store`
  - `DELETE /dashboard/gallery/{transformation}` → `dashboard.gallery.destroy`
- **Fonctionnalités** :
  - Liste des transformations avant / après avec images (médias `before` et `after`).
  - Modal de création d’une transformation : titre, description, upload `before`, upload `after`.
  - Suppression de transformation.

#### 2.3.2 `Coach/GalleryBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/GalleryBeta.vue`
- **Contrôleur** : `GalleryController@index/store/destroy` (rend cette vue si `beta=1`)
- **Spécificités** :
  - UI sombre, cartes compactes pour chaque transformation, labels "AVANT/ APRÈS".
  - Modal beta pour la création avec previews.
  - Requêtes vers `dashboard.gallery.store` et `dashboard.gallery.destroy` avec `beta=1`.

### 2.4 FAQ

#### 2.4.1 `Dashboard/Faq` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Faq.vue`
- **Contrôleur** : `Dashboard\FaqController@index/store/update/destroy`
- **Routes** :
  - `GET /dashboard/faq` → `dashboard.faq`
  - `POST /dashboard/faq` → `dashboard.faq.store`
  - `PATCH /dashboard/faq/{faq}` → `dashboard.faq.update`
  - `DELETE /dashboard/faq/{faq}` → `dashboard.faq.destroy`
- **Fonctionnalités** :
  - Liste des FAQ (question, réponse, ordre, actif/inactif).
  - Modal de création/édition avec validation.
  - Suppression de FAQ.

#### 2.4.2 `Coach/FaqBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/FaqBeta.vue`
- **Contrôleur** : `FaqController@index` (beta) + `store/update/destroy` avec propagation `beta` dans les redirects.
- **Spécificités** :
  - UI sombre, cartes FAQ compactes.
  - Modal beta de création/édition.
  - Requêtes vers `dashboard.faq.*` avec `beta=1`.

## 3. Business

### 3.1 Plans

#### 3.1.1 `Dashboard/Plans` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Plans.vue`
- **Contrôleur** : `Dashboard\PlansController@index/store/update/destroy`
- **Routes** :
  - `GET /dashboard/plans` → `dashboard.plans`
  - `POST /dashboard/plans` → `dashboard.plans.store`
  - `PATCH /dashboard/plans/{plan}` → `dashboard.plans.update`
  - `DELETE /dashboard/plans/{plan}` → `dashboard.plans.destroy`
- **Fonctionnalités** :
  - Liste de plans tarifaires : nom, description, prix, URL CTA, actif/inactif.
  - Modal de création/édition.
  - Suppression de plan.

#### 3.1.2 `Coach/PlansBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/PlansBeta.vue`
- **Contrôleur** : `PlansController@index` (beta) + actions avec propagation `beta`.
- **Spécificités** :
  - Grid de cartes plans avec style sombre.
  - Modal beta de création/édition : nom, prix, description, URL, actif.
  - Requêtes vers `dashboard.plans.store/update/destroy` avec `beta=1`.

### 3.2 Clients

#### 3.2.1 `Dashboard/Clients` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Clients.vue`
- **Contrôleur** : `Dashboard\ClientController@index/store/update/destroy/storeNote/updateNote/destroyNote`
- **Routes** :
  - `GET /dashboard/clients` → `dashboard.clients.index`
  - `POST /dashboard/clients` → `dashboard.clients.store`
  - `PATCH /dashboard/clients/{client}` → `dashboard.clients.update`
  - `DELETE /dashboard/clients/{client}` → `dashboard.clients.destroy`
  - Notes :
    - `POST /dashboard/clients/{client}/notes` → `dashboard.clients.notes.store`
    - `PATCH /dashboard/clients/notes/{note}` → `dashboard.clients.notes.update`
    - `DELETE /dashboard/clients/notes/{note}` → `dashboard.clients.notes.destroy`
- **Fonctionnalités** :
  - Stats : nombre de clients, nombre de notes, clients avec email/téléphone.
  - Recherche par nom / email / téléphone.
  - Fiche client : prénom, nom, email, téléphone, adresse, TVA, notes.
  - Modal création/édition de client.
  - Modal notes par client : création/édition/suppression de notes.

#### 3.2.2 `Coach/ClientsBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/ClientsBeta.vue`
- **Contrôleur** : `ClientController@index` (beta) + actions avec propagation `beta`.
- **Spécificités** :
  - Même logique : stats, recherche, fiches clients, notes.
  - UI sombre avec cartes clients.
  - Requêtes vers `dashboard.clients.*` et `dashboard.clients.notes.*` avec `beta=1`.

### 3.3 Messages de contact

#### 3.3.1 `Dashboard/Contact` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Contact.vue`
- **Contrôleur** : `Dashboard\ContactController@index/markAsRead/destroy`
- **Routes** :
  - `GET /dashboard/contact` → `dashboard.contact`
  - `PATCH /dashboard/contact/{contactMessage}/read` → `dashboard.contact.read`
  - `DELETE /dashboard/contact/{contactMessage}` → `dashboard.contact.destroy`
- **Fonctionnalités** :
  - Liste des messages reçus via le site public : nom, email, téléphone, contenu, date, statut lu/non lu.
  - Marquer comme lu.
  - Suppression.

#### 3.3.2 `Coach/ContactBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/ContactBeta.vue`
- **Contrôleur** : `ContactController@index` (beta) + actions avec propagation `beta`.
- **Spécificités** :
  - UI sombre, cartes pour chaque message.
  - Actions : marquer comme lu / supprimer.
  - Requêtes vers `dashboard.contact.read/destroy` avec `beta=1`.

### 3.4 Mentions légales

#### 3.4.1 `Dashboard/Legal` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Legal.vue`
- **Contrôleur** : `Dashboard\LegalController@edit/update`
- **Routes** :
  - `GET /dashboard/legal` → `dashboard.legal`
  - `POST /dashboard/legal` → `dashboard.legal.update`
- **Fonctionnalités** :
  - Edition du numéro de TVA (stocké sur `user`).
  - Edition du texte des mentions légales/CGV (stocké sur `coach`).
  - Boutons : recharger le modèle, copier le texte, afficher un aperçu.

#### 3.4.2 `Coach/LegalBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/LegalBeta.vue`
- **Contrôleur** : `LegalController@edit` (beta) + `update` (via back())
- **Spécificités** :
  - UI sombre simplifiée mais avec les mêmes contrôles : TVA + textarea géant.
  - Requêtes vers `dashboard.legal.update` avec `beta=1`.

## 4. Compte & abonnement

### 4.1 Abonnement

#### 4.1.1 `Dashboard/Subscription` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Subscription.vue`
- **Contrôleur** : `Dashboard\SubscriptionController@index/createCheckoutSession/customerPortal/cancelSubscription`
- **Routes** :
  - `GET /dashboard/subscription` → `dashboard.subscription`
  - `POST /dashboard/subscription/checkout` → `dashboard.subscription.checkout`
  - `POST /dashboard/subscription/portal` → `dashboard.subscription.portal`
  - `POST /dashboard/subscription/cancel` → `dashboard.subscription.cancel`
- **Fonctionnalités** :
  - Affichage de l’état d’essai (jours restants, date de fin).
  - Affichage de l’état d’abonnement (actif/inactif).
  - Infos utilisateur (nom, email).
  - CTA pour souscrire ou gérer l’abonnement via Lemon Squeezy.
  - Bloc descriptif de la formule FEA Coach Pro.

#### 4.1.2 `Coach/SubscriptionBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/SubscriptionBeta.vue`
- **Contrôleur** : `SubscriptionController@index` (beta)
- **Spécificités** :
  - UI sombre, cartes resserrées.
  - Même logique d’état d’essai / abonnement.
  - Requêtes vers `dashboard.subscription.checkout/portal` avec `beta=1`.

### 4.2 Support

#### 4.2.1 `Dashboard/Support` (classique)
- **Chemin** : `resources/js/Pages/Dashboard/Support.vue`
- **Contrôleur** : `Dashboard\SupportTicketController@index/store/reply/close`
- **Routes** :
  - `GET /dashboard/support` → `dashboard.support`
  - `POST /dashboard/support` → `dashboard.support.store`
  - `POST /dashboard/support/{supportTicket}/reply` → `dashboard.support.reply`
  - `POST /dashboard/support/{supportTicket}/close` → `dashboard.support.close`
- **Fonctionnalités** :
  - Création d’un ticket : sujet, catégorie, message.
  - Liste de tickets avec statut (ouvert/clos) et dates.
  - Conversation par ticket (messages support / utilisateur).
  - Réponse et fermeture de ticket.

#### 4.2.2 `Coach/SupportBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/SupportBeta.vue`
- **Contrôleur** : `SupportTicketController@index` (beta) + actions avec propagation `beta`.
- **Spécificités** :
  - Layout sombre, deux colonnes : formulaire nouveau ticket + zone conversation.
  - Requêtes vers `dashboard.support.store/reply/close` avec `beta=1`.

### 4.3 Profil utilisateur

#### 4.3.1 `Profile/Edit` (classique)
- **Chemin** : `resources/js/Pages/Profile/Edit.vue`
- **Contrôleur** : `ProfileController@edit/update/destroy`
- **Routes** :
  - `GET /profile` → `profile.edit`
  - `PATCH /profile` → `profile.update`
  - `DELETE /profile` → `profile.destroy`
- **Fonctionnalités** :
  - Formulaire d’édition des informations de profil (nom, email,…).
  - Formulaire de changement de mot de passe.
  - Formulaire de suppression de compte.

#### 4.3.2 `Coach/ProfileBeta` (beta)
- **Chemin** : `resources/js/Pages/Coach/ProfileBeta.vue`
- **Contrôleur** : `ProfileController@edit/update` avec support de `beta=1` via query/referrer.
- **Spécificités** :
  - UI sombre avec 3 sections : infos compte, mot de passe, suppression.
  - Réutilise les partials : `UpdateProfileInformationForm`, `UpdatePasswordForm`, `DeleteUserForm`.
  - Redirection après mise à jour conserve `?beta=1` si on vient de la vue beta.

## 5. Pistes UI/UX "wow effect" pour les vues enfants (beta)

Cette section sert de check-list pour harmoniser toutes les vues **Coach/*Beta.vue** avec le niveau d'exigence visuelle de `Coach/DashboardCoachBeta.vue`.

### 5.1 Principes transverses

- **Cohérence visuelle**
  - Réutiliser les mêmes familles de couleurs (slate + accents violet/rose/émeraude) et les mêmes rayons / ombres que dans le dashboard beta.
  - Introduire des **icônes Lucide** pertinentes sur chaque carte / section, comme dans le dashboard.
- **Hiérarchie et respirations**
  - Toujours avoir : titre de page clair → sous-titre contextuel → sections en cartes bien séparées.
  - Limiter la densité d'informations par carte, préférer plusieurs blocs logiques.
- **Micro-copy & feedback**
  - Ajouter des petites phrases explicatives (1–2 lignes) sous les titres pour guider l'utilisateur.
  - Soigner les états vides, success / error, loaders (textes utiles + ton cohérent).
- **Motion & interactions**
  - Prévoir des hover states subtils sur les cartes / boutons (légère translation, intensité de gradient).
  - Utiliser des transitions pour les modales (fade/scale) si la future stack le permet.

### 5.2 Site vitrine

#### Branding (`Coach/BrandingBeta.vue`)
- **Rôle** : identité visuelle (couleurs, logo, hero, layout).
- **Idées wow** :
  - Prévisualisation live du site (ou d'un mini mockup) avec les couleurs/logo sélectionnés.
  - Palette de couleurs avec pastilles animées, suggestions de palettes.
  - Indicateurs d'accessibilité (contraste) simplifiés.

#### Contenu (`Coach/ContentBeta.vue`)
- **Rôle** : textes de la page vitrine (hero, à propos, méthode, stats, CTA, réseaux).
- **Idées wow** :
  - Garder / renforcer la carte de **complétion du contenu** (progress bar + tips contextuels).
  - Ajouter des exemples/placeholder plus inspirants (copywriting) pour les sections clés.
  - Indiquer sur chaque champ où il sera utilisé sur le site (petit label "Apparaît dans : Hero / À propos / etc.").

#### Galerie (`Coach/GalleryBeta.vue`)
- **Rôle** : transformations avant / après.
- **Idées wow** :
  - Ajouter un **mode comparaison slider** (avant/après sur un même visuel) si la stack future le permet.
  - Mettre en avant les meilleurs cas (badge "Top transformation").
  - Ajouter une ligne de copy en haut qui explique comment ces visuels impactent la conversion.

#### FAQ (`Coach/FaqBeta.vue`)
- **Rôle** : gestion des questions/réponses pour le site.
- **Idées wow** :
  - Afficher un compteur clair : nombre de FAQ actives vs totales.
  - Ajouter des suggestions de questions types (templates) dans la modale.
  - Permettre un drag & drop de l'ordre d'affichage (dans la future UI si possible).

### 5.3 Business

#### Plans (`Coach/PlansBeta.vue`)
- **Rôle** : offres / pricing affichés sur le site.
- **Idées wow** :
  - Représenter chaque plan comme une **pricing card** premium (prix mis en avant, bénéfices en bullets).
  - Ajouter un badge "Recommandé" / "Populaire".
  - Visualiser l'URL CTA (icône externe, prévisualisation possible).

#### Clients (`Coach/ClientsBeta.vue`)
- **Rôle** : base clients + notes.
- **Idées wow** :
  - Remonter quelques **KPI** en haut : % clients avec email, last activity, etc.
  - Rendre les cartes clients plus narratives (avatar, badge "N notes", dernière interaction).
  - Dans la modale de notes, prévoir un layout type "journal" avec timeline.

#### Messages de contact (`Coach/ContactBeta.vue`)
- **Rôle** : messages entrants du site.
- **Idées wow** :
  - Indicateur "Nouveaux" vs "Traités" en haut.
  - Actions rapides : bouton "Répondre par email" bien mis en valeur.
  - États vides illustrés (icône, phrase motivante).

#### Mentions légales (`Coach/LegalBeta.vue`)
- **Rôle** : TVA + texte CGV/mentions.
- **Idées wow** :
  - Afficher un mini bandeau "Statut conformité" (texte à personnaliser).
  - Séparer visuellement TVA / CGV / sections clés avec ancres ou onglets.
  - Prévoir une zone "Aperçu côté site" mieux intégrée (preview pane à droite).

### 5.4 Compte & abonnement

#### Abonnement (`Coach/SubscriptionBeta.vue`)
- **Rôle** : état d'essai, abonnement, CTA vers checkout/portal.
- **Idées wow** :
  - Renforcer la narration de la période d'essai (compte à rebours, jalons).
  - Clarifier les bénéfices de la formule dans un comparatif (Essai vs Pro).
  - Ajouter un bandeau d'alerte doux quand l'essai approche de la fin.

#### Support (`Coach/SupportBeta.vue`)
- **Rôle** : tickets support + conversation.
- **Idées wow** :
  - Transformer la liste de tickets en **timeline de conversations**.
  - Ajouter des chips de statut/catégorie plus visibles.
  - Mettre en avant le temps de réponse moyen (simple texte statique ou futur KPI).

#### Profil (`Coach/ProfileBeta.vue`)
- **Rôle** : infos compte, mot de passe, suppression.
- **Idées wow** :
  - Ajouter un encart "Profil coach" vs "Compte utilisateur" pour clarifier ce qui est modifié ici.
  - Mieux raconter les conséquences de la suppression de compte (micro-copy empathique).
  - Icônes et couleurs différenciées pour chaque bloc (infos, sécurité, danger zone).

---

Ce document peut servir de référence complète pour :
- cartographier toutes les vues/flux du panel coach,
- vérifier les dépendances backend (contrôleurs, routes),
- préparer un redesign UI/UX en gardant le même périmètre fonctionnel,
- guider le travail de mise au niveau "wow effect" de l'ensemble des vues enfants beta.
