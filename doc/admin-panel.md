# Panel Administrateur - FEA-COACH

Documentation du panel d'administration pour la gestion des coachs et leurs sous-domaines.

## 🎯 Fonctionnalités

Le panel admin permet de :

- ✅ Créer de nouveaux coachs avec leur compte utilisateur
- ✅ Configurer les sous-domaines personnalisés
- ✅ Gérer les informations des coachs (nom, email, couleurs)
- ✅ Activer/désactiver des coachs
- ✅ Modifier les mots de passe
- ✅ Supprimer des coachs

## 🔐 Accès au Panel Admin

### Compte Administrateur

**Email**: `admin@fea-coach.com`  
**Mot de passe**: `password`  
**Rôle**: `admin`

### URL d'accès

- Panel admin : `http://localhost:8000/admin/coaches`
- Depuis le dashboard : Bannière bleue "Accès Administrateur"

## 📋 Structure du Panel

### 1. Liste des Coachs (`/admin/coaches`)

Affiche tous les coachs avec :
- Nom et slug
- Email
- Sous-domaine
- Statut (Actif/Inactif)
- Date de création
- Actions (Modifier, Supprimer)

### 2. Création de Coach (`/admin/coaches/create`)

Formulaire pour créer un nouveau coach :

**Champs requis :**
- Nom du Coach
- Email (doit être unique)
- Mot de passe (minimum 8 caractères)
- Sous-domaine (lettres minuscules, chiffres et tirets uniquement)
- Couleur primaire (format hex: `#RRGGBB`)
- Couleur secondaire (format hex: `#RRGGBB`)
- Statut actif (checkbox)

**Processus de création :**
1. Création du compte utilisateur avec le rôle `coach`
2. Création du profil coach lié à l'utilisateur
3. Initialisation des valeurs par défaut pour le contenu
4. Redirection vers la liste des coachs

### 3. Modification de Coach (`/admin/coaches/{id}/edit`)

Permet de modifier :
- Nom du coach
- Email
- Mot de passe (optionnel - laissez vide pour conserver l'actuel)
- Sous-domaine
- Couleurs (primaire et secondaire)
- Statut actif

## 🛡️ Sécurité

### Middleware `IsAdmin`

Le middleware vérifie que :
- L'utilisateur est authentifié
- L'utilisateur a le rôle `admin`
- Retourne une erreur 403 si non autorisé

**Fichier**: `app/Http/Middleware/IsAdmin.php`

### Protection des Routes

Toutes les routes admin sont protégées par :
```php
Route::middleware(['auth', 'verified', 'admin'])
```

## 🎨 Interface Utilisateur

### Layout Admin

Layout dédié avec :
- Navigation simplifiée pour l'admin
- Badge "Admin" dans le header
- Menu "Gestion Coachs"

**Fichier**: `resources/js/Layouts/AdminLayout.vue`

### Pages Vue

- **Index**: `resources/js/Pages/Admin/Coaches/Index.vue`
- **Create**: `resources/js/Pages/Admin/Coaches/Create.vue`
- **Edit**: `resources/js/Pages/Admin/Coaches/Edit.vue`

## 🔧 Backend

### Contrôleur

**Fichier**: `app/Http/Controllers/Admin/AdminCoachController.php`

**Méthodes :**
- `index()` - Liste tous les coachs
- `create()` - Affiche le formulaire de création
- `store()` - Enregistre un nouveau coach
- `edit($coach)` - Affiche le formulaire d'édition
- `update($coach)` - Met à jour un coach
- `destroy($coach)` - Supprime un coach et son compte utilisateur

### Routes

```php
// Préfixe: /admin
Route::get('/coaches', [AdminCoachController::class, 'index'])
    ->name('admin.coaches.index');
    
Route::get('/coaches/create', [AdminCoachController::class, 'create'])
    ->name('admin.coaches.create');
    
Route::post('/coaches', [AdminCoachController::class, 'store'])
    ->name('admin.coaches.store');
    
Route::get('/coaches/{coach}/edit', [AdminCoachController::class, 'edit'])
    ->name('admin.coaches.edit');
    
Route::patch('/coaches/{coach}', [AdminCoachController::class, 'update'])
    ->name('admin.coaches.update');
    
Route::delete('/coaches/{coach}', [AdminCoachController::class, 'destroy'])
    ->name('admin.coaches.destroy');
```

## ✨ Fonctionnalités Automatiques

### Génération du Slug

Le slug est automatiquement généré à partir du nom lors de la création et mise à jour :
- Conversion en minuscules
- Suppression des accents
- Remplacement des espaces par des tirets
- Suppression des caractères spéciaux

### Génération du Sous-domaine

Sur la page de création, le sous-domaine est auto-généré depuis le nom lorsque l'utilisateur quitte le champ "Nom du Coach".

### Messages Flash

Messages de succès affichés après :
- Création d'un coach
- Mise à jour d'un coach
- Suppression d'un coach

## 📝 Validation

### Règles de Validation

**Création :**
- `name`: requis, max 255 caractères
- `email`: requis, email valide, unique
- `password`: requis, min 8 caractères
- `subdomain`: requis, unique, regex `^[a-z0-9\-]+$`
- `color_primary`: requis, regex `^#[0-9A-Fa-f]{6}$`
- `color_secondary`: requis, regex `^#[0-9A-Fa-f]{6}$`
- `is_active`: booléen

**Mise à jour :**
- Mêmes règles que création
- `email`: unique sauf pour le coach courant
- `subdomain`: unique sauf pour le coach courant
- `password`: optionnel (si vide, conserve l'actuel)

## 🚀 Utilisation Pratique

### Créer un Nouveau Coach

1. Se connecter en tant qu'admin
2. Accéder au panel admin
3. Cliquer sur "Créer un Coach"
4. Remplir le formulaire :
   - Nom : `Marie Durand`
   - Email : `marie@example.com`
   - Mot de passe : `password123`
   - Sous-domaine : `marie-durand` (auto-généré)
   - Couleurs : Choisir avec le color picker
   - Cocher "Coach actif"
5. Cliquer sur "Créer le Coach"

### Modifier un Coach Existant

1. Dans la liste des coachs
2. Cliquer sur "Modifier" pour le coach souhaité
3. Modifier les champs nécessaires
4. Laisser le mot de passe vide pour le conserver
5. Cliquer sur "Enregistrer"

### Désactiver un Coach

1. Modifier le coach
2. Décocher "Coach actif"
3. Enregistrer
4. Le site du coach ne sera plus accessible publiquement

### Supprimer un Coach

1. Dans la liste des coachs
2. Cliquer sur "Supprimer"
3. Confirmer la suppression
4. ⚠️ **Action irréversible** - Supprime le coach ET le compte utilisateur

## 🔄 Intégration avec le Dashboard

### Bannière Admin

Les utilisateurs admin voient une bannière bleue en haut du dashboard standard avec :
- Message indiquant l'accès admin
- Bouton "Panel Admin" pour accéder directement

### Badge Admin

Dans le header du layout admin, un badge "Admin" est affiché à côté du nom de l'utilisateur.

## 📦 Données Partagées avec Inertia

Le middleware `HandleInertiaRequests` partage :

```php
'auth' => [
    'user' => [
        'id' => ...,
        'name' => ...,
        'email' => ...,
        'role' => ..., // 'admin' ou 'coach'
    ]
],
'flash' => [
    'success' => ...,
    'error' => ...,
]
```

## 🧪 Tests

### Tester le Panel Admin

```bash
# Se connecter en tant qu'admin
Email: admin@fea-coach.com
Password: password

# Accéder au panel
http://localhost:8000/admin/coaches

# Créer un coach de test
# Vérifier qu'il apparaît dans la liste
# Modifier ses informations
# Le désactiver puis le réactiver
# Supprimer le coach de test
```

### Tester les Restrictions

```bash
# Se connecter en tant que coach (non-admin)
Email: pierre@example.com
Password: password

# Tenter d'accéder au panel admin
http://localhost:8000/admin/coaches
# Devrait retourner une erreur 403 Forbidden
```

## 🎨 Personnalisation

### Ajouter d'Autres Fonctionnalités Admin

1. Créer de nouveaux contrôleurs dans `app/Http/Controllers/Admin/`
2. Ajouter les routes dans le groupe admin
3. Créer les vues dans `resources/js/Pages/Admin/`
4. Ajouter les liens dans `AdminLayout.vue`

### Modifier les Couleurs du Panel

Les couleurs sont définies avec Tailwind CSS dans les composants Vue. Modifier les classes pour changer le thème.

---

_Document créé le 12 novembre 2025_
