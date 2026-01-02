# Audit de lancement – UNICOACH

Date : janvier 2026

Ce document dresse un **état des lieux fonctionnel et technique** de la plateforme UNICOACH au moment du lancement officiel, afin de :

- donner de la visibilité au partenaire **Fitness Education Academy (FEA)** ;
- clarifier le périmètre livré et les limites connues ;
- identifier les principaux risques et priorités de roadmap post‑lancement.

---

## 1. Contexte et objectifs

UNICOACH est une **plateforme SaaS multi‑tenant** permettant à chaque coach sportif d’avoir son propre site web professionnel, accessible via un sous‑domaine dédié, et de gérer son activité (contenus, offres, clients, documents, etc.).

Le projet a été conçu pour être déployé en partenariat avec **Fitness Education Academy**, afin de fournir aux coachs diplômés FEA un outil concret pour lancer et structurer leur activité en ligne.

---

## 2. Périmètre fonctionnel au lancement

### 2.1. Côté coach (dashboard)

Fonctionnalités principales disponibles pour les coachs :

- **Dashboard central** (Vue 3 + Inertia) avec tutoriel d’onboarding intégré.
- **Branding du site** :
  - choix des couleurs principales/secondaires ;
  - upload de logo et image hero avec prévisualisation ;
  - mode sombre.
- **Gestion du contenu du site** :
  - sections Hero, À propos, Méthode, Tarifs, Transformations, FAQ, Appels à l’action ;
  - texte des boutons et messages clés ;
  - photo de profil du coach.
- **Galerie de transformations** :
  - upload avant/après ;
  - suppression ;
  - prévisualisation.
- **Plans tarifaires** :
  - création/édition/suppression ;
  - activation/désactivation ;
  - réorganisation.
- **FAQ dynamique** :
  - création/édition/suppression ;
  - réorganisation et affichage sur le site public.
- **Gestion des clients** :
  - création/édition/suppression de fiches clients ;
  - notes de suivi ;
  - mesures & photos (évolution, bilans) ;
  - messagerie coach ↔ client avec pièces jointes ;
  - documents partagés (programmes, bilans, etc.).
- **Messagerie & support** :
  - tickets de support côté coach vers l’équipe UNICOACH/FEA ;
  - suivi de statut des tickets.
- **Mentions légales et CGV** :
  - écran dédié pour personnaliser les textes légaux avec les informations du coach (TVA, adresse, etc.).
- **Abonnement & domaine personnalisé** :
  - écran de gestion d’abonnement (statut, période d’essai, résiliation fin de période) ;
  - déclenchement d’un achat de domaine personnalisé (Custom Domain) via Lemon Squeezy.

### 2.2. Côté client final

Pour les clients des coachs :

- **Site public du coach** (Blade + Tailwind + Alpine.js) :
  - design responsive, SEO‑friendly ;
  - sections complètes : Hero, À propos, Méthode, Tarifs, Transformations, FAQ, Contact ;
  - formulaire de contact relié au dashboard coach.
- **Espace client sécurisé via lien partagé** :
  - visualisation des documents partagés ;
  - accès aux programmes (sportif/nutrition), bilans, notes, profil, évolution ;
  - messagerie client ↔ coach.

### 2.3. Côté administrateur (FEA / plateforme)

- **Panel admin dédié** (`/admin`) avec middleware `admin` :
  - gestion des coachs (création, édition, suppression, activation/désactivation) ;
  - gestion des sous‑domaines et domaines personnalisés ;
  - suivi des demandes de contact reçues depuis les sites publics.
- **Gestion des codes promo FEA** :
  - génération de batches de codes promo `FEA-XXXXXXXX` ;
  - gestion des demandes de validation FEA par les utilisateurs ;
  - envoi automatique d’emails contenant un lien de paiement Lemon Squeezy au tarif diplômé.

### 2.4. Onboarding et facturation

- **Onboarding en 3 étapes** après création de compte :
  1. type de compte (diplômé FEA / non diplômé) ;
  2. informations légales (nom, TVA, adresse, etc.) ;
  3. activation par code promo FEA ou paiement standard.
- **Facturation** :
  - intégration Lemon Squeezy pour la création de sessions de paiement ;
  - webhook sécurisé (`POST /webhooks/lemonsqueezy`) pour synchroniser les abonnements (création, mise à jour, annulation, expiration) ;
  - logique de tarif préférentiel FEA : 20€ HTVA/mois vs 30€ HTVA/mois standard, géré par variantes Lemon Squeezy ;
  - gestion d’un produit « nom de domaine personnalisé » via un `order_created` Lemon Squeezy.

---

## 3. Architecture technique (vue synthétique)

- **Backend** : Laravel 11 (PHP 8.2+), architecture MVC classique.
- **Frontend public** : Blade + TailwindCSS + Alpine.js.
- **Dashboard coach / admin** : Inertia.js + Vue 3 + Vite.
- **Base de données** : MySQL/MariaDB, modèle **single database multi‑tenant** avec filtrage par `coach_id`.
- **Multi‑tenant** :
  - résolution du coach via middleware `ResolveCoachFromHost` (sous‑domaine) ;
  - configuration basée sur `APP_DOMAIN` et DNS wildcard.
- **Médias** : Spatie Media Library (upload, stockage local/S3, liens sécurisés).
- **Logs & sauvegardes** : Spatie Activity Log et Spatie Backup.

Cette architecture est cohérente avec un **SaaS B2B/B2C** de taille moyenne, orienté forte personnalisation visuelle et gestion de contenu.

---

## 4. Sécurité (état actuel)

Éléments en place :

- Authentification Laravel Breeze (Inertia + Vue) + gestion des rôles (`admin`, coach, etc.).
- Protection CSRF (hors webhook public, correctement exempté et sécurisé par signature HMAC).
- Protection XSS et injection SQL via l’ORM Eloquent et le templating Blade.
- Validation systématique des inputs côté backend (Requests, contrôleurs).
- Lien de partage client avec token unique pour accéder à l’espace client.
- Vérification de signature HMAC (`X-Signature`) pour les webhooks Lemon Squeezy.

Points à renforcer (recommandations génériques) :

- définir une **politique de complexité et de rotation des mots de passe** alignée avec la politique sécurité FEA ;
- documenter les bonnes pratiques côté coach (choix de mots de passe, partage des accès, etc.) ;
- mettre en place un **monitoring centralisé** des logs (erreurs, webhooks, actions sensibles).

---

## 5. Qualité, tests et déploiement

### 5.1. Tests

- Le projet contient une base de tests (Feature et Unit) mais la couverture n’est **pas encore exhaustive**.
- Le README indique que la mise en place de **tests automatisés plus complets** (feature & unit) est prévue.

Recommandations :

- formaliser un **plan de tests fonctionnels** complet couvrant : inscription, onboarding, paiement, webhooks, création/édition de contenu, gestion des clients, etc. ;
- étendre progressivement la **couverture de tests automatisés** sur les scénarios critiques (paiement, webhooks, multi‑tenant, partage de documents, droits d’accès).

### 5.2. Déploiement

- Le README décrit une **cible de production** standard (VPS Ubuntu, Nginx, PHP-FPM, MySQL, Redis, Supervisor, SSL wildcard) et recommande l’usage de Laravel Forge.
- La configuration exacte (CI/CD, stratégie de backup/restauration, supervision) dépendra de l’infrastructure choisie.

Recommandations :

- mettre en place un **pipeline CI/CD** (tests + build + déploiement automatisé) ;
- documenter les procédures de **sauvegarde/restauration** et de **montée de version** ;
- prévoir un environnement de **pré‑production** pour valider les nouvelles versions avant mise en ligne.

---

## 6. Risques principaux et limites connues

Sur base de l’état actuel du code et du README :

1. **Facturation Lemon Squeezy (MVP)**  
   - la logique principale (création de sessions, webhooks, mise à jour des abonnements) est en place ;
   - la checklist E2E et les tests en sandbox doivent être réalisés et documentés de manière systématique (cas réussis, annulations, échecs de paiement, expiration, changement de carte, etc.).

2. **Tests automatisés partiels**  
   - la couverture actuelle est suffisante pour un MVP technique, mais insuffisante pour un très grand volume d’utilisateurs/coachs sans renforcement ;
   - risque : régressions lors des évolutions futures (nouveaux écrans, conditions commerciales, intégrations supplémentaires).

3. **Performance et montée en charge**  
   - l’architecture Laravel + MySQL + Redis est adaptée, mais les optimisations (caches ciblés, indexation, queues intensives) devront être ajustées en fonction de la charge réelle ;
   - risque : lenteurs perçues si un nombre important de coachs et de clients se connectent simultanément sans tuning spécifique.

4. **Analytics et pilotage business**  
   - le README mentionne des analytics/métriques business « à venir » ;
   - sans ces métriques, il est plus difficile pour FEA de mesurer l’adoption, l’engagement et le ROI par promotion de coachs.

5. **Documentation fonctionnelle**  
   - le code est structuré et le README technique est détaillé ;
   - pour le lancement à plus grande échelle, une **documentation fonctionnelle** simple (guides pas‑à‑pas pour les coachs et pour FEA) sera importante pour limiter la charge de support.

---

## 7. Recommandations prioritaires post‑lancement

1. **Verrouiller la chaîne de facturation**  
   - finaliser et documenter les scénarios de tests Lemon Squeezy (sandbox) ;
   - suivre les premiers paiements réels avec un monitoring rapproché (logs + tableaux de bord Lemon Squeezy) ;
   - s’assurer que les statuts d’abonnement dans UNICOACH reflètent correctement la réalité de facturation.

2. **Renforcer la qualité et la stabilité**  
   - étendre la couverture de tests automatisés sur les parcours critiques ;
   - mettre en place une procédure claire de déploiement (et rollback) ;
   - suivre les erreurs applicatives en production via un outil centralisé (Sentry, Bugsnag, etc.).

3. **Accompagner le partenariat FEA**  
   - définir un processus clair d’onboarding des coachs diplômés (de la sortie de formation à l’activation du site) ;
   - prévoir des templates de contenus de base pour les coachs (textes, visuels, mentions légales types) ;
   - mettre en place un reporting simple pour FEA : nombre de coachs actifs, sites en ligne, répartition par promotion, etc.

4. **Préparer les évolutions produit**  
   - planifier les analytics intégrés (tableaux de bord coach + panel FEA) ;
   - réfléchir à d’éventuels add‑ons premium (ex. : domaines personnalisés, modules avancés de suivi clients, automatisations marketing) ;
   - consolider la roadmap 6–12 mois avec priorisation claire (technique vs business).

---

## 8. Conclusion

La plateforme UNICOACH dispose d’un **socle fonctionnel et technique solide** pour un lancement MVP piloté avec Fitness Education Academy :

- l’architecture multi‑tenant est en place ;
- le site public des coachs et le dashboard sont opérationnels ;
- l’onboarding, la gestion de contenu, des clients et la facturation (via Lemon Squeezy) sont implémentés.

Les travaux à mener dans les prochains mois concernent principalement :

- la sécurisation complète de la chaîne de facturation,
- le renforcement des tests et de la supervision,
- l’enrichissement de la couche analytics et de la documentation fonctionnelle.

Cela permettra de passer progressivement d’un **MVP maîtrisé** à un **produit robuste et industrialisé** pour un déploiement plus large auprès des coachs diplômés FEA.
