# Guide de test - FEA-COACH

**Date :** 12 novembre 2025  
**Version :** 0.8 (Phase 9-10 complétée)

---

## 🚀 Démarrage rapide

### 1. Prérequis
- ✅ Base de données MySQL configurée
- ✅ PHP 8.2+ installé
- ✅ Composer installé
- ✅ Node.js installé
- ✅ Extensions PHP : EXIF, GD, etc.

### 2. Installation (si pas déjà fait)
```bash
# Installer dépendances PHP
composer install

# Installer dépendances JS
npm install

# Copier .env
cp .env.example .env

# Générer clé app
php artisan key:generate

# Migrations + seeders
php artisan migrate:fresh --seed

# Compiler assets
npm run build
# OU mode dev (hot reload)
npm run dev
```

### 3. Démarrage des serveurs

**Terminal 1 - Laravel :**
```bash
php artisan serve
```
Serveur disponible sur : `http://127.0.0.1:8000`

**Terminal 2 - Vite (optionnel, dev uniquement) :**
```bash
npm run dev
```

---

## 👥 Comptes de test

### Coachs actifs

#### 1. Pierre Martin
- **Email :** `pierre.martin@example.com`
- **Password :** `password`
- **Sous-domaine :** `pierre-martin`
- **URL site public :** `http://pierre-martin.localhost:8000`
- **Couleurs :** Bleu (#3B82F6) / Vert (#10B981)

#### 2. Sophie Dubois
- **Email :** `sophie.dubois@example.com`
- **Password :** `password`
- **Sous-domaine :** `sophie-dubois`
- **URL site public :** `http://sophie-dubois.localhost:8000`
- **Couleurs :** Rose (#EC4899) / Violet (#8B5CF6)

### Coach inactif

#### 3. Thomas Leroy
- **Email :** `thomas.leroy@example.com`
- **Password :** `password`
- **Sous-domaine :** `thomas-leroy`
- **URL site public :** `http://thomas-leroy.localhost:8000` (devrait être inaccessible)
- **Statut :** `is_active = false`

### Admin

#### 4. Admin
- **Email :** `admin@example.com`
- **Password :** `password`
- **Role :** `admin`
- **Pas de coach associé**

---

## 🧪 Scénarios de test

### A. Sites publics (Blade + Alpine.js)

#### Test 1 : Accès au site d'un coach
1. Ouvrir `http://pierre-martin.localhost:8000`
2. ✅ La page doit se charger avec le thème bleu/vert
3. ✅ Vérifier que toutes les sections sont présentes :
   - Hero (titre, sous-titre, 2 boutons)
   - À propos (texte, stats)
   - Ma méthode (3 étapes)
   - Tarifs (4 plans)
   - Résultats (galerie transformations)
   - FAQ (4 questions)
   - Contact/CTA final

#### Test 2 : Navigation
1. Cliquer sur les liens du menu
2. ✅ Scroll smooth vers les sections
3. ✅ Menu mobile fonctionne (responsive)
4. ✅ Logo cliquable dans le header

#### Test 3 : Interactivité Alpine.js
1. Ouvrir le menu mobile (sur petit écran)
2. ✅ Animation smooth
3. ✅ Menu se ferme au clic sur un lien
4. Tester la FAQ
5. ✅ Accordéon fonctionne
6. ✅ Une seule question ouverte à la fois

#### Test 4 : Théming dynamique
1. Accéder au site de Sophie : `http://sophie-dubois.localhost:8000`
2. ✅ Les couleurs sont différentes (rose/violet)
3. ✅ Tous les boutons et éléments utilisent les bonnes couleurs
4. ✅ CSS variables appliquées correctement

#### Test 5 : Coach inactif
1. Accéder à `http://thomas-leroy.localhost:8000`
2. ✅ Page d'erreur ou redirection (selon middleware)

---

### B. Dashboard (Vue 3 + Inertia)

#### Test 6 : Connexion
1. Aller sur `http://localhost:8000/login`
2. Se connecter avec `pierre.martin@example.com` / `password`
3. ✅ Redirection vers `/dashboard`
4. ✅ Page d'accueil dashboard s'affiche
5. ✅ Stats affichées (Actif, pierre-martin, Voir mon site)
6. ✅ 3 cards (Branding, Contenu, Galerie)

#### Test 7 : Page Branding
1. Cliquer sur "Branding" ou aller à `/dashboard/branding`
2. ✅ Formulaire de branding s'affiche
3. Modifier la couleur primaire
4. ✅ Color picker fonctionne
5. ✅ Input texte synchronisé
6. Uploader un logo
7. ✅ Preview s'affiche immédiatement
8. Uploader une image hero
9. ✅ Preview s'affiche
10. Cliquer "Enregistrer"
11. ✅ Message de succès
12. ✅ Données sauvegardées

**Vérification :**
- Retourner sur le site public
- ✅ Nouvelles couleurs appliquées
- ✅ Nouveau logo affiché

#### Test 8 : Page Contenu
1. Aller à `/dashboard/content`
2. ✅ Tous les champs sont pré-remplis
3. Modifier le titre hero
4. Modifier le texte "À propos"
5. ✅ Compteur de caractères fonctionne
6. Enregistrer
7. ✅ Message de succès

**Vérification :**
- Retourner sur le site public
- ✅ Nouveaux textes affichés

#### Test 9 : Page Galerie
1. Aller à `/dashboard/gallery`
2. ✅ Liste des transformations existantes
3. Cliquer "Ajouter une transformation"
4. ✅ Modal s'ouvre
5. Remplir le formulaire :
   - Titre : "Test transformation"
   - Description : "Description test"
   - Uploader image "before"
   - Uploader image "after"
6. ✅ Previews s'affichent
7. Cliquer "Ajouter"
8. ✅ Modal se ferme
9. ✅ Nouvelle transformation dans la liste
10. ✅ Message de succès

**Vérification :**
- Retourner sur le site public
- ✅ Nouvelle transformation visible dans la galerie

#### Test 10 : Suppression transformation
1. Sur la page galerie
2. Cliquer "Supprimer" sur une transformation
3. ✅ Confirmation demandée
4. Confirmer
5. ✅ Transformation supprimée
6. ✅ Message de succès

#### Test 11 : Validation
1. Page Branding : essayer d'enregistrer une couleur invalide
2. ✅ Message d'erreur affiché
3. Page Contenu : essayer de vider le titre hero
4. ✅ Message d'erreur (champ requis)
5. Page Galerie : essayer d'ajouter sans images
6. ✅ Validation HTML5 (required)

#### Test 12 : Navigation dashboard
1. Tester tous les liens du menu
2. ✅ Dashboard → Branding → Contenu → Galerie
3. ✅ Active state correct sur chaque page
4. ✅ Version mobile responsive
5. ✅ Menu hamburger fonctionne

---

### C. Multi-tenancy

#### Test 13 : Isolation des données
1. Se connecter en tant que Pierre Martin
2. Aller sur la galerie
3. Noter les transformations visibles
4. Se déconnecter
5. Se connecter en tant que Sophie Dubois
6. Aller sur la galerie
7. ✅ Seules les transformations de Sophie sont visibles
8. ✅ Pas de fuite de données

#### Test 14 : Sous-domaines
1. Vérifier que `pierre-martin.localhost:8000` affiche le site de Pierre
2. Vérifier que `sophie-dubois.localhost:8000` affiche le site de Sophie
3. ✅ Chaque site affiche les bonnes données
4. ✅ Couleurs différentes
5. ✅ Contenus différents

---

## 🐛 Tests d'erreurs

### Test 15 : Sous-domaine invalide
1. Accéder à `http://invalid-coach.localhost:8000`
2. ✅ Erreur 404 ou message approprié

### Test 16 : Upload fichier trop lourd
1. Essayer d'uploader une image > 5MB
2. ✅ Message d'erreur validation

### Test 17 : Upload mauvais format
1. Essayer d'uploader un fichier PDF comme logo
2. ✅ Message d'erreur validation

### Test 18 : Accès non autorisé
1. Se déconnecter
2. Essayer d'accéder à `/dashboard/branding`
3. ✅ Redirection vers login

---

## 📱 Tests responsive

### Breakpoints à tester
- 📱 Mobile : 375px - 640px
- 📱 Tablet : 640px - 1024px
- 💻 Desktop : 1024px+

### Éléments à vérifier
- ✅ Navigation mobile (hamburger)
- ✅ Grid transformations (1/2/3 colonnes)
- ✅ Grid plans (1/2/4 colonnes)
- ✅ Images responsive
- ✅ Textes lisibles
- ✅ Boutons accessibles
- ✅ Forms utilisables

---

## ⚡ Tests de performance

### À vérifier
1. Temps de chargement page < 3s
2. Images optimisées
3. CSS minifié (build production)
4. JS minifié (build production)
5. Pas d'erreurs console
6. Pas de warnings Lighthouse majeurs

### Outils
- Chrome DevTools (Network, Lighthouse)
- GTmetrix (optionnel)
- WebPageTest (optionnel)

---

## 🔍 Checklist finale

### Sites publics
- [ ] Toutes les sections s'affichent
- [ ] Navigation fonctionne
- [ ] Théming dynamique opérationnel
- [ ] Alpine.js (menu mobile, FAQ) fonctionne
- [ ] Images chargent correctement
- [ ] Responsive (mobile, tablet, desktop)
- [ ] Pas d'erreurs console

### Dashboard
- [ ] Login fonctionne
- [ ] Page d'accueil dashboard OK
- [ ] Branding : upload logo OK
- [ ] Branding : upload hero OK
- [ ] Branding : couleurs OK
- [ ] Contenu : tous les champs OK
- [ ] Galerie : ajout transformation OK
- [ ] Galerie : suppression OK
- [ ] Validation fonctionne partout
- [ ] Messages succès/erreur affichés
- [ ] Navigation entre pages OK
- [ ] Responsive dashboard OK

### Multi-tenancy
- [ ] Chaque coach a son site
- [ ] Données isolées (pas de fuite)
- [ ] Sous-domaines fonctionnent
- [ ] Middleware résolution coach OK

---

## 🚨 Problèmes connus

### Sous-domaines locaux (Windows)
Si les sous-domaines ne fonctionnent pas :

1. Éditer `C:\Windows\System32\drivers\etc\hosts` (admin)
2. Ajouter :
```
127.0.0.1 pierre-martin.localhost
127.0.0.1 sophie-dubois.localhost
127.0.0.1 thomas-leroy.localhost
```

### Images manquantes
Si les images ne s'affichent pas :
1. Vérifier `php artisan storage:link`
2. Vérifier les permissions du dossier `storage/`
3. Vérifier la config `filesystems.php`

### Erreurs Vite
Si les assets ne chargent pas :
1. S'assurer que `npm run dev` ou `npm run build` a été exécuté
2. Vérifier `public/build/manifest.json` existe
3. Vider le cache navigateur

---

## 📞 Support

### Logs à vérifier
- `storage/logs/laravel.log` - Erreurs Laravel
- Console navigateur - Erreurs JS
- Network tab - Requêtes échouées

### Commandes utiles
```bash
# Recréer la BDD
php artisan migrate:fresh --seed

# Vider les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild assets
npm run build

# Storage link
php artisan storage:link
```

---

**Bon test ! 🚀**
