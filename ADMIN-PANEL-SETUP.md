# Panel Admin - Installation Complète ✅

Un panel d'administration complet a été créé pour gérer les coachs et leurs sous-domaines.

## 🎯 Ce qui a été créé

### Backend

1. **Middleware `IsAdmin`**
   - Fichier : `app/Http/Middleware/IsAdmin.php`
   - Vérifie que l'utilisateur a le rôle `admin`
   - Enregistré dans `bootstrap/app.php` avec l'alias `admin`

2. **Contrôleur `AdminCoachController`**
   - Fichier : `app/Http/Controllers/Admin/AdminCoachController.php`
   - CRUD complet pour gérer les coachs
   - Méthodes : index, create, store, edit, update, destroy

3. **Routes Admin**
   - Préfixe : `/admin`
   - Protection : `auth`, `verified`, `admin`
   - Routes RESTful pour la gestion des coachs

4. **Mise à jour HandleInertiaRequests**
   - Partage du rôle utilisateur avec Inertia
   - Partage des messages flash (success/error)

### Frontend

5. **Layout Admin**
   - Fichier : `resources/js/Layouts/AdminLayout.vue`
   - Navigation dédiée avec badge "Admin"
   - Menu simplifié pour l'administration

6. **Pages Vue Admin**
   - `resources/js/Pages/Admin/Coaches/Index.vue` - Liste des coachs
   - `resources/js/Pages/Admin/Coaches/Create.vue` - Création de coach
   - `resources/js/Pages/Admin/Coaches/Edit.vue` - Modification de coach

7. **Intégration Dashboard**
   - Bannière bleue pour les admins dans le dashboard
   - Lien direct vers le panel admin

### Documentation

8. **Documentation complète**
   - `doc/admin-panel.md` - Guide complet du panel admin
   - Ce fichier de setup

## 🔐 Accès au Panel Admin

**URL** : `http://localhost:8000/admin/coaches`

**Compte Admin par défaut :**
- Email : `admin@fea-coach.com`
- Mot de passe : `password`

## ✨ Fonctionnalités

- ✅ Créer de nouveaux coachs avec compte utilisateur
- ✅ Configurer les sous-domaines personnalisés
- ✅ Gérer les couleurs du branding (primaire/secondaire)
- ✅ Activer/désactiver des coachs
- ✅ Modifier les informations et mots de passe
- ✅ Supprimer des coachs
- ✅ Auto-génération du sous-domaine depuis le nom
- ✅ Validation complète des données
- ✅ Messages de succès/erreur
- ✅ Interface moderne et responsive

## 🚀 Utilisation

### Pour tester immédiatement :

1. **Lancer le serveur de développement**
   ```bash
   npm run dev
   php artisan serve
   ```

2. **Se connecter en tant qu'admin**
   - Aller sur `http://localhost:8000/login`
   - Email : `admin@fea-coach.com`
   - Mot de passe : `password`

3. **Accéder au panel admin**
   - Cliquer sur le bouton "Panel Admin" dans la bannière bleue du dashboard
   - Ou aller directement sur `http://localhost:8000/admin/coaches`

4. **Créer un nouveau coach**
   - Cliquer sur "Créer un Coach"
   - Remplir le formulaire
   - Le sous-domaine sera auto-généré depuis le nom
   - Choisir les couleurs avec le color picker

### Exemple de création :

**Nom** : Sophie Martin  
**Email** : sophie.martin@example.com  
**Mot de passe** : password123  
**Sous-domaine** : sophie-martin (auto-généré)  
**Couleur primaire** : #ec4899  
**Couleur secondaire** : #f59e0b  
**Statut** : Actif ✓

## 📁 Fichiers créés/modifiés

### Nouveaux fichiers
```
app/Http/Middleware/IsAdmin.php
app/Http/Controllers/Admin/AdminCoachController.php
resources/js/Layouts/AdminLayout.vue
resources/js/Pages/Admin/Coaches/Index.vue
resources/js/Pages/Admin/Coaches/Create.vue
resources/js/Pages/Admin/Coaches/Edit.vue
doc/admin-panel.md
ADMIN-PANEL-SETUP.md
```

### Fichiers modifiés
```
bootstrap/app.php (middleware registration)
routes/web.php (admin routes)
app/Http/Middleware/HandleInertiaRequests.php (share role + flash)
resources/js/Pages/Dashboard.vue (admin banner)
```

## 🛡️ Sécurité

- ✅ Middleware de vérification du rôle admin
- ✅ Routes protégées par authentification
- ✅ Validation stricte des entrées
- ✅ Protection CSRF automatique (Laravel)
- ✅ Hachage des mots de passe
- ✅ Erreur 403 pour accès non autorisé

## 📚 Documentation

Pour plus de détails, consulter : `doc/admin-panel.md`

## ⚠️ Important

- Le compte admin est créé par le seeder (`database/seeders/CoachSeeder.php`)
- Les mots de passe par défaut doivent être changés en production
- La suppression d'un coach est irréversible (supprime aussi le compte utilisateur)

## 🎨 Personnalisation

Le panel utilise Tailwind CSS et peut être facilement personnalisé en modifiant les classes dans les composants Vue.

---

**Date de création** : 12 novembre 2025  
**Statut** : ✅ Opérationnel et prêt à l'emploi
