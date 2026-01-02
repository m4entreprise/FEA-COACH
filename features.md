# UNICOACH – Toutes les fonctionnalités clés

Ce document présente **toutes les fonctionnalités principales d’UNICOACH** telles qu’implémentées dans le code actuel. Il est rédigé pour aider **Fitness Education Academy (FEA)** à expliquer et vendre la plateforme à ses étudiants / diplômés.

---

## 1. Vue d’ensemble

UNICOACH est une **plateforme SaaS multi‑tenant** qui permet à chaque coach sportif de :

- créer un **site vitrine professionnel** sur un sous‑domaine dédié ;
- gérer son **branding**, ses **contenus**, ses **offres**, ses **preuves sociales** (avant/après, avis, FAQ) ;
- centraliser ses **clients**, documents, programmes, mesures, photos et échanges ;
- offrir à chaque client un **espace en ligne sécurisé** pour suivre son programme et sa progression ;
- gérer son **abonnement** et des options premium (domaine personnalisé) via une facturation automatisée.

UNICOACH est conçu pour qu'**un coach sortant de FEA puisse lancer son activité en ligne très vite**, avec un rendu professionnel et une structure de travail digne d’un studio de coaching bien organisé.

---

## 2. Fonctionnalités pour les coachs diplômés FEA

### 2.1. Site vitrine professionnel sur sous‑domaine

- **Ce que c’est**  
  Chaque coach dispose d’un **site public dédié**, accessible via un sous‑domaine (ex. `prenom-nom.unicoach.app`). Le site inclut : Hero, À propos, Méthode, Tarifs, Transformations, FAQ, Contact.

- **Pourquoi c’est génial pour un diplômé FEA**  
  En sortant de formation, la plupart des coachs n’ont ni site, ni compétences techniques. Ici, le coach part avec **un site pro prêt à l’emploi**, conforme aux bonnes pratiques marketing et au positionnement FEA.

---

### 2.2. Onboarding guidé et wizard de configuration

- **Ce que c’est**  
  - Onboarding en **3 étapes** :
    - choix du type de compte (diplômé FEA / non diplômé) ;
    - informations légales (nom, TVA, adresse, etc.) ;
    - activation via **code promo FEA** ou paiement standard.
  - Wizard de configuration en **5 étapes** pour mettre en place : branding, images, contenus, sections avancées, finalisation.

- **Pourquoi c’est génial**  
  Un diplômé FEA est pris **par la main** : il n’a pas à deviner quoi remplir ni dans quel ordre. Le wizard transforme la création de site en **checklist simple**, ce qui réduit massivement la friction et le risque d’abandon.

---

### 2.3. Dashboard coach central

- **Ce que c’est**  
  Un **panel coach moderne** (Vue 3 + Inertia) qui propose :
  - une vue d’ensemble : complétion du profil, statut du site, nombre de plans, transformations ;
  - accès rapide à toutes les sections (site, branding, contenu, galerie, FAQ, clients, légal, support, abonnement) ;
  - un tutoriel d’onboarding visuel pour découvrir les fonctionnalités.

- **Pourquoi c’est génial**  
  Le coach a **un seul endroit** pour piloter son activité en ligne. Pas de menus techniques ou confus : tout est pensé pour un **usage quotidien simple**, même pour un coach peu à l’aise avec l’informatique.

---

### 2.4. Branding visuel complet (couleurs, logo, images, layouts)

- **Ce que c’est**  
  Dans la section Branding :
  - choix des **couleurs principales et secondaires** ;
  - upload de **logo** (professionnel) ;
  - upload d’**image de hero section** (bannière au dessus du site web) ;
  - choix parmi plusieurs **layouts de site** (thèmes) prédéfinis ;
  - prévisualisation du site public avec les modifications.

- **Pourquoi c’est génial**  
  Le coach crée une **identité visuelle forte** en quelques clics, sans graphiste ni développeur. Il obtient immédiatement un site qui reflète sa personnalité et son univers, ce qui renforce la **crédibilité** auprès des futurs clients.

---

### 2.5. Gestion du contenu marketing du site

- **Ce que c’est**  
  Éditeur de contenu pour toutes les sections clés :
  - titres & sous‑titres du Hero ;
  - texte « À propos » et « Ma méthode » ;
  - textes des étapes de la méthode (3 étapes) ;
  - titres/sous‑titres des tarifs, transformations, FAQ, CTA final ;
  - texte des boutons d’appel à l’action ;
  - indicateurs marketing (taux de satisfaction, note moyenne) ;
  - liens vers réseaux sociaux (Facebook, Instagram, YouTube, LinkedIn, TikTok…).

- **Pourquoi c’est génial**  
  Le coach peut **raconter son histoire** et structurer son offre comme dans un **copywriting professionnel**, sans toucher au code. Tout est prêt pour aligner le discours avec ce qu’il a appris chez FEA (positionnement, bénéfices, preuve sociale).

---

### 2.6. Galerie de transformations avant / après

- **Ce que c’est**  
  Une galerie dédiée où le coach peut :
  - ajouter des transformations (titre, description) ;
  - uploader des photos **avant/après** ;
  - organiser l’ordre d’affichage ;
  - prévisualiser le rendu sur le site public.

- **Pourquoi c’est génial**  
  Les résultats visuels sont l’un des arguments les plus puissants en coaching (supposition). La galerie permet au coach de **mettre en avant ses réussites** et de rassurer les prospects sur la qualité de son accompagnement.

---

### 2.7. Système d’offres & tarifs (plans)

- **Ce que c’est**  
  Gestion des **plans de coaching** :
  - création, édition, suppression ;
  - prix, description, lien d’appel à l’action (ex. : lien de paiement, formulaire, appel téléphonique) ;
  - activation/désactivation ;
  - réorganisation par **drag & drop**.

- **Pourquoi c’est génial**  
  Le coach structure clairement ses offres : pack débutant, suivi premium, coaching en ligne, etc.  
  Résultat : **moins de négociations au hasard**, plus de clarté dans ce qu’il vend et à quel prix.

---

### 2.8. FAQ dynamique

- **Ce que c’est**  
  Une section FAQ entièrement administrable :
  - ajout / édition / suppression de questions‑réponses ;
  - activation/désactivation par question ;
  - réorganisation de l’ordre par **drag & drop**.

- **Pourquoi c’est génial**  
  Le coach répond à l’avance aux objections fréquentes (prix, durée, matériel, remboursements…).  
  Cela réduit le temps passé à écrire toujours les mêmes réponses et **accélère la décision d’achat**.

---

### 2.9. Mentions légales & CGV prêtes à adapter

- **Ce que c’est**  
  - Écran dédié pour renseigner TVA, adresse légale, etc. ;
  - **modèle de Conditions Générales de Vente** prérempli, généré en fonction des données du coach ;
  - affichage automatique sur une page « Mentions légales / CGV » du site public.

- **Pourquoi c’est génial**  
  Beaucoup de coachs n’ont **aucune idée de ce qu’ils doivent écrire légalement**. UNICOACH leur fournit un **cadre structuré** à adapter, ce qui réduit le stress lié à la conformité et renforce la crédibilité du site.

---

### 2.10. Gestion des messages de contact (prospects)

- **Ce que c’est**  
  - formulaire de contact intégré sur le site public ;
  - centralisation des messages reçus dans le dashboard coach ;
  - marquage « lu / non lu », suppression.

- **Pourquoi c’est génial**  
  Tous les prospects qui remplissent le formulaire **arrivent dans le même outil**. Le coach ne perd plus d’opportunités dans ses DMs ou mails dispersés.

---

### 2.11. Base de clients intégrée (CRM simplifié)

- **Ce que c’est**  
  - gestion des clients depuis la section « Mes Clients » : création, modification, suppression ;
  - informations de base : nom, email, téléphone, adresse, TVA ;
  - **code élève** à 6 chiffres et **lien de partage sécurisé** générés automatiquement ;
  - stats synthétiques (clients avec email, téléphone, nombre total de notes, etc.).

- **Pourquoi c’est génial**  
  Au lieu de travailler avec des feuilles Excel et des notes dispersées, le coach dispose d’un **mini‑CRM spécialisé pour le coaching**, déjà relié à son site et à l’espace client.

---

### 2.12. Fiche client 360° : contexte, psychologie, cuisine, matériel…

- **Ce que c’est**  
  Pour chaque client, le coach peut stocker :
  - allergies, aliments non aimés ;
  - blessures, niveau de stress, qualité du sommeil ;
  - suivi du cycle menstruel, dernière période ;
  - budget courses, équipements cuisine disponibles, compléments ;
  - matériel sportif disponible, fréquence d’entraînement, durée des séances, niveau d’activité quotidienne ;
  - objectif principal, motivation profonde, style de coaching préféré (strict, soutenant, autonome) ;
  - commentaires généraux.

- **Pourquoi c’est génial**  
  C’est une **fiche personnage complète**, exactement ce que FEA enseigne en termes de **profiling du client** (supposition). Le coach peut proposer un programme réellement adapté au contexte de vie, et pas un simple PDF générique et sans incentive.

---

### 2.13. Suivi des mesures physiques & évolution

- **Ce que c’est**  
  - enregistrement régulier des mesures (poids, taille, tour de poitrine, taille, hanches, bras, cuisses) ;
  - upload des photos de progression (face, profil, dos) ;
  - calcul automatique de l’IMC ;
  - gestion intelligente d’une mesure par semaine (mise à jour si une mesure existe déjà sur la période) ;
  - graphiques et récapitulatif dans l’espace client (section « Évolution »).

- **Pourquoi c’est génial**  
  Le coach dispose d’un **tableau de bord objectif de la progression**. Il peut montrer noir sur blanc les résultats à son client, ce qui :
  - améliore l’adhésion au programme ;
  - aide à justifier la valeur du coaching dans le temps ;
  - permet de **justifier le prix** du coaching ;
  - professionnalise l’accompagnement.

---

### 2.14. Messagerie sécurisée coach ↔ client (avec pièces jointes)

- **Ce que c’est**  
  - échange de messages dans le dashboard coach et dans l’espace client ;
  - possibilité d’ajouter des **pièces jointes** (documents, vidéos, ressources) ;
  - suivi des messages non lus, marque comme « lu » automatiquement côté client et côté coach.

- **Pourquoi c’est génial**  
  Toutes les conversations restent **centralisées dans un seul endroit**, liées à la fiche client.  
  Fini les échanges éparpillés sur WhatsApp, Instagram, SMS… Le coach garde un **historique structuré** de l’accompagnement.

---

### 2.15. Notes de séance & journal de suivi interne

- **Ce que c’est**  
  - création, édition, suppression de **notes privées** par client ;
  - historique daté des notes ;
  - outil de prise de notes directement dans l’interface de gestion client.

- **Pourquoi c’est génial**  
  Le coach peut documenter chaque séance (ressenti, points d’attention, consignes pour la suivante).  
  Cela professionnalise l’accompagnement et permet de **reprendre le fil facilement** même avec beaucoup de clients.

---

### 2.16. Partage de documents (programmes, nutrition, bilans) avec versions

- **Ce que c’est**  
  - upload de documents par type : `program`, `nutrition`, `assessment` ;
  - gestion des **versions** (v1, v2, v3…) pour chaque document ;
  - logs de téléchargement (qui a téléchargé quoi, quand) ;
  - mise à disposition directe dans l’espace client.

- **Pourquoi c’est génial**  
  Plus besoin d’envoyer des PDFs par email. Le client a **tous ses documents au même endroit**, toujours à jour, et le coach garde une trace de ce qui a été partagé.

---

### 2.17. Espace client sur lien privé (sans gestion de mots de passe)

- **Ce que c’est**  
  - pour chaque client, un lien unique `/p/{token}` ;
  - sécurisation par **code à 6 chiffres** (share_code) fourni par le coach ;
  - une fois déverrouillé, le client accède à :
    - ses programmes sportifs ;
    - ses plans nutrition ;
    - ses bilans ;
    - ses notes ;
    - son profil ;
    - sa courbe d’évolution et ses photos ;
    - la messagerie avec son coach.

- **Pourquoi c’est génial**  
  Le coach offre à ses clients une **expérience d’application dédiée** sans devoir gérer des comptes, mots de passe, réinitialisations, etc.  
  C’est simple pour le client (un lien + un code), et sérieux pour le coach.

---

### 2.18. Abonnement & facturation (avec tarif préférentiel FEA)

- **Ce que c’est**  
  - intégration complète avec **Lemon Squeezy** pour la facturation récurrente ;
  - distinction des tarifs :
    - **20€ HTVA / mois** pour les diplômés FEA ;
    - **30€ HTVA / mois** pour les autres coachs ;
  - gestion de la période d’essai, du statut d’abonnement, des renouvellements ;
  - portail client Lemon Squeezy pour gérer sa carte, ses factures, etc.

- **Pourquoi c’est génial**  
  Le coach bénéficie d’un **tarif préférentiel négocié par FEA** (storytelling), tout en ayant une facturation professionnelle et automatisée.  
  Pas besoin de brancher Stripe ou autre : tout est déjà câblé.

---

### 2.19. Option nom de domaine personnalisé

- **Ce que c’est**  
  - possibilité d’acheter un **nom de domaine personnalisé** (ex. `www.prenomnom-coach.com`) directement depuis le panel ;
  - commande gérée via Lemon Squeezy, suivi dans le back‑office ;
  - suivi du statut du domaine (en attente, actif, expiré…).

- **Pourquoi c’est génial**  
  Le coach peut passer du sous‑domaine au **vrai domaine de marque** sans se soucier de la technique (DNS, SSL…).  
  C’est un **upsell naturel** pour FEA et un levier de professionnalisation pour le coach.

---

### 2.20. Support intégré avec l’équipe FEA / UNICOACH

- **Ce que c’est**  
  - système de **tickets de support** : création, réponses, clôture ;
  - historique des échanges avec l’équipe de support ;
  - statut des tickets (ouverts, fermés).

- **Pourquoi c’est génial**  
  Le coach n’est pas livré à lui‑même. Il sait qu’en cas de problème technique ou de question, il peut **contacter l’équipe technique directement depuis son dashboard**.

---

## 3. Fonctionnalités côté étudiant / client du coach

### 3.1. Accès simple et sécurisé à son espace

- **Ce que c’est**  
  - un lien unique reçu du coach (`/p/{token}`) ;
  - un **code à 6 chiffres** pour déverrouiller l’accès ;
  - session mémorisée pendant la durée de la consultation.

- **Pourquoi c’est génial**  
  L’étudiant n’a pas besoin de retenir un mot de passe supplémentaire. Il a **un portail clair**, accessible sur mobile, où il retrouve tout son suivi.

---

### 3.2. Tous ses programmes au même endroit

- **Ce que c’est**  
  - onglet **Programme sportif** : tous les programmes partagés par le coach, avec historique des versions ;
  - onglet **Programme alimentaire** : plans nutrition, menus, recommandations.

> **Note** : Nous sommes entrain de développer un moteur qui génèrera des plans alimentaires directement reliées à la base de données **Ciqual** de l'**Anses** avec substitut automatiquement recommandé en fonction de ce que le coach aura rempli (sortie prévue fin janvier 2026).

- **Pourquoi c’est génial**  
  L’étudiant ne cherche plus ses PDFs dans ses mails. Il sait que **tout est dans UNICOACH**, toujours à jour.

---

### 3.3. Bilans et documents importants

- **Ce que c’est**  
  - onglet **Bilans** pour tous les documents de type évaluation, compte rendu, tests, etc. ;
  - possibilité de télécharger les documents partagés.

- **Pourquoi c’est génial**  
  Il garde une trace structurée de son parcours, de ses bilans et des points clés vus avec le coach.

---

### 3.4. Messagerie avec son coach

- **Ce que c’est**  
  - espace de **discussion dédiée** avec le coach ;
  - possibilité d’envoyer des messages et des pièces jointes (photos, documents) ;
  - les messages du coach sont marqués « lus » lorsqu’il les consulte.

- **Pourquoi c’est génial**  
  Il se sent **accompagné en continu**, pas seulement pendant les séances. La communication est centralisée, ce qui favorise la **motivation**, la **responsabilisation** et la **satisfaction**.

---

### 3.5. Profil complet & fiche personnage

- **Ce que c’est**  
  - possibilité de mettre à jour :
    - ses blessures, allergies, contraintes ;
    - son niveau de stress, sommeil ;
    - ses objectifs et motivations ;
    - son budget, ses équipements ;
    - son style de coaching préféré.

- **Pourquoi c’est génial**  
  L’étudiant devient **acteur de son propre suivi** : en remplissant ces infos, il aide le coach à mieux adapter le programme. Cela renforce la personnalisation et la satisfaction.

---

### 3.6. Mesures & photos de progression

- **Ce que c’est**  
  - mise à jour de ses mesures (poids, taille, etc.) ;
  - upload de photos (face, profil, dos) ;
  - visualisation de l’évolution dans le temps.

- **Pourquoi c’est génial**  
  Il voit concrètement ses progrès, ce qui **booste sa motivation** et lui donne envie de continuer à s’investir dans le programme.

---

### 3.7. Vue Analytics de son évolution

- **Ce que c’est**  
  - page **Évolution** avec :
    - toutes les mesures dans l’ordre chronologique ;
    - première vs dernière mesure ;
    - nombre total de photos, nombre de relevés ;
    - graphiques synthétiques.

- **Pourquoi c’est génial**  
  L’étudiant a une **vision claire de sa transformation**. Il comprend que ce n’est pas juste un programme, mais un **processus mesuré** et suivi.

---

## 4. Intérêt spécifique pour Fitness Education Academy

UNICOACH n’est pas seulement une plateforme pratique pour les coachs. C’est un **levier stratégique** pour Fitness Education Academy : il matérialise, au quotidien, la promesse de l’école – *« on ne vous forme pas seulement, on vous aide à en vivre »*.

### 4.1. Une présence de marque discrète, visible 24h/24 dans le panel coach

- **Ce que c’est**  
  Le panel coach intègre une **présence discrète de la marque FEA** (branding / mention visible en continu), sans être intrusive. À chaque connexion, le coach travaille dans un environnement **associé à FEA**, sans que cela gêne son usage ni celui de ses clients.

- **Pourquoi c’est puissant pour FEA**  
  Cette présence fonctionne comme une **publicité permanente ultra‑qualitative** :
  - chaque coach voit FEA **tous les jours**, dans l’outil qu’il utilise pour gérer son business ;
  - la marque reste **top of mind** au moment où il pense à son métier, à ses clients, à son développement ;
  - l’école n’apparaît pas comme un simple souvenir de formation passée, mais comme un **partenaire actif** de son activité.

En pratique, c’est comme si FEA avait un **affichage discret dans le “bureau numérique” de chaque diplômé**. Sans push agressif, sans pop‑up marketing : juste une présence constante, crédible et légitime.

### 4.2. Tenir la promesse : « on vous aide à vivre de votre métier »

- **Avant l’inscription**  
  Dans son discours commercial, FEA peut dire :
  > *« Chez nous, vous ne repartez pas seulement avec un diplôme, mais avec un **vrai outil business opérationnel** pour lancer votre activité. »*

  Concrètement, FEA peut présenter UNICOACH comme :
  - un **site pro clé en main** personnalisé au coach ;
  - un **espace client** sécurisé prêt à accueillir les premiers clients payants ;
  - un système de **suivi, de mesure et de communication** aligné avec ce qui est enseigné en formation.

- **Après la formation**  
  Le récit devient très simple à vendre :
  - le coach termine son cursus FEA ;
  - il active son compte UNICOACH à tarif préférentiel ;
  - en quelques jours, il peut **ouvrir ses premiers créneaux payants** avec un site, un espace client et une structure de suivi déjà prêts.

FEA ne vend plus seulement de la pédagogie. FEA vend **un passage à l’action**. C’est un argument majeur face à d’autres écoles.

### 4.3. Un partenariat tarifaire structuré et différenciant

- **Partenariat tarifaire structuré**  
  La tarification préférentielle FEA est directement intégrée dans la logique de facturation d’UNICOACH. Pour l’école, c’est :
  - un avantage concret, facile à expliquer dans les brochures et webinaires ;
  - une justification simple de type *« en tant que diplômé FEA, vous payez moins cher votre outil métier »* ;
  - un moyen de **valoriser l’appartenance au réseau FEA**.

- **Codes promo FEA**  
  Les codes FEA permettent :
  - d’identifier clairement les coachs issus de la formation ;
  - de lier la réussite business du coach à l’écosystème FEA ;
  - de créer des **campagnes commerciales dédiées** (lancement d’une nouvelle promo, offres spéciales diplômés, etc.).

Pour le marketing de FEA, c’est un axe de communication très fort : *« on a négocié pour vous un outil premium, à un tarif spécial diplômés FEA »*.

### 4.4. Une expérience homogène, qui renforce l’image de l’école

En utilisant tous le même socle (site, espace client, structure de suivi), les coachs issus de FEA :

- bénéficient d’une **qualité minimale garantie** de présence en ligne ;
- évitent les sites bricolés ou incohérents avec ce qui est enseigné en cours ;
- renvoient une image moderne et professionnelle, alignée avec la marque FEA.

Pour FEA, chaque coach utilisateur d’UNICOACH devient une **vitrine indirecte de l’école** :

- quand il montre son site ou son espace client à d’autres professionnels, cela **reflète positivement sur la formation** ;
- la cohérence globale renforce l’idée que FEA ne forme pas seulement des coachs, mais des **entrepreneurs du coaching**.

### 4.5. Un socle pour la relation long terme avec les diplômés

Parce qu’UNICOACH est au cœur du quotidien du coach (gestion des clients, programmes, abonnements), FEA dispose d’un **point de contact naturel et durable** avec ses anciens élèves :

- la marque reste présente dans leur outil de travail jour après jour ;
- il est plus facile de maintenir un **lien relationnel** (événements, contenus, offres de formation continue, masterminds, etc.) en s’appuyant sur l’écosystème UNICOACH ;
- FEA peut renforcer son positionnement : *« nous sommes votre partenaire carrière, pas seulement votre école »*.

### 4.6. Proposition : un annuaire FEA qui met en avant les coachs utilisateurs d’UNICOACH

Dans le cadre du partenariat, FEA pourrait mettre en place sur son propre site un **annuaire officiel des coachs diplômés**. Les coachs qui utilisent UNICOACH seraient **mis en avant préférentiellement** dans cet annuaire.

- **Ce que ce pourrait être concrètement**  
  - une page publique du type *« Trouver un coach certifié FEA »* ;
  - pour chaque coach : photo, spécialités, localisation, lien vers son site UNICOACH, bouton de contact ;
  - un **badge** ou un filtre spécifique *« coach équipé d’UNICOACH »* ;
  - un **classement ou une mise en avant prioritaire** pour les coachs utilisant la plateforme.

- **Pourquoi c’est intéressant pour FEA**  
  - l’école devient aussi un **générateur de clients** pour ses diplômés, pas seulement un organisme de formation ;
  - FEA peut valoriser auprès des prospects : *« en sortant de chez nous, vous êtes visibles sur notre annuaire officiel »* ;
  - l’utilisation d’UNICOACH devient un **critère d’excellence** : coach structuré, digitalisé, capable de suivre sérieusement ses clients.

- **Pourquoi c’est intéressant pour les coachs**  
  - plus de **visibilité** grâce à la marque FEA ;
  - un **avantage concret** à adopter UNICOACH : être mieux placé dans l’annuaire que les coachs qui ne l’utilisent pas ;
  - un argument supplémentaire auprès de leurs prospects : *« je suis coach certifié FEA et mis en avant dans l’annuaire officiel, avec un espace client digital »*.

> **Geste commercial M4 Entreprise** : M4 Entreprise s’engage à **développer l’annuaire sur le site de FEA sans coût additionnel tant que le partenariat est en vigueur**. Si le partenariat dure **1 an ou plus**, l’annuaire sera considéré comme **pleinement appartenant à FEA**.

Cette proposition reste souple : elle peut être lancée dans un premier temps sous forme de **MVP simple** (liste filtrable), puis enrichie au fil du temps (avis, filtres avancés, mise en avant des meilleures transformations, etc.).

---

En résumé, UNICOACH donne à FEA un **outil clé en main pour professionnaliser la mise sur le marché de ses diplômés** *et* un **levier marketing différenciant** :

- les coachs ont un site, une structure de travail et un suivi client haut de gamme, directement reliés à la marque FEA ;
- l’école bénéficie d’une **présence de marque discrète mais permanente** dans l’outil métier quotidien de ses anciens élèves ;
- FEA peut se positionner comme **le partenaire complet de la carrière de ses coachs**, depuis la formation jusqu’au développement concret de leur activité.
