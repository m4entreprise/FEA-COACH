# Module Clients Complet - Ignite Coach

## Vue d'ensemble

Module complet de gestion des clients pour les coachs permettant de suivre :
- **Informations clients** : coordonnées, objectifs, notes internes
- **Mesures** : poids, mensurations, IMG, photos progression
- **Documents** : fichiers organisés par catégorie
- **Bilans** : suivi énergie, difficulté, progression
- **Activités** : timeline automatique de toutes les actions

---

## Architecture

### 1. Migrations (5 tables)

#### `clients` table
- Champs : first_name, last_name, email, phone, date_of_birth
- JSON : objectives (array), internal_notes (text)
- Enum : status (active/inactive/paused)
- Relations : belongsTo Coach, hasMany (measurements, documents, assessments, activities)
- SoftDeletes activé

#### `client_measurements` table
- Champs : weight (decimal), body_fat_percentage (decimal), measurement_date
- JSON : body_measurements (tour taille, hanches, bras, cuisses), photos (array URLs)
- Notes additionnelles possibles

#### `client_documents` table
- Champs : title, file_path, file_name, file_size, mime_type, uploaded_at
- Enum : category (medical, program, nutrition, contract, results, other)
- Storage : disk 'private' pour sécurité

#### `client_assessments` table
- Champs : energy_level (1-10), difficulty_level (1-10)
- Text : progress_notes, coach_comments
- Enum : status (pending, completed)
- Date : assessment_date

#### `client_activities` table
- Log automatique de toutes les actions
- Champs : type (string), metadata (JSON), description
- Types : client_created, client_updated, measurement_added, document_uploaded, etc.

---

## 2. Modèles avec Relations

### Client.php
```php
// Relations
- coach()              // BelongsTo
- measurements()       // HasMany (orderBy measurement_date desc)
- documents()          // HasMany (orderBy uploaded_at desc)
- assessments()        // HasMany (orderBy assessment_date desc)
- activities()         // HasMany (orderBy created_at desc)

// Attributs calculés
- full_name           // "Prénom Nom"
- age                 // Calcul depuis date_of_birth

// Casts
- objectives: array
- date_of_birth: date
```

### ClientMeasurement.php
```php
// Casts
- body_measurements: array
- photos: array
- weight: decimal:2
- body_fat_percentage: decimal:2
- measurement_date: date

// Scope
- forPeriod($startDate, $endDate)
```

### ClientDocument.php
```php
// Attributs calculés
- download_url        // Route vers téléchargement
- formatted_size      // "2.5 Mo"

// Boot hook
- Suppression automatique du fichier lors de delete()
```

### ClientAssessment.php
```php
// Scopes
- completed()
- pending()

// Méthodes
- markAsCompleted()
```

### ClientActivity.php
```php
// Scopes
- ofType($type)
- recent($days = 7)
```

---

## 3. Form Requests (Validation stricte)

### StoreClientRequest / UpdateClientRequest
- first_name, last_name : required, max 255
- email : nullable, email, unique (ignoré sur update)
- phone : nullable, max 20
- date_of_birth : nullable, date, before:today
- objectives : nullable, array
- internal_notes : nullable, max 5000
- status : required, in:active,inactive,paused

### StoreMeasurementRequest
- weight : nullable, numeric, min:20, max:300
- body_measurements : nullable, array (waist, hips, chest, arms, thighs)
- body_fat_percentage : nullable, numeric, min:0, max:100
- photos : nullable, array, chaque photo max 5MB (jpeg,jpg,png,webp)
- measurement_date : required, date, before_or_equal:today

### StoreDocumentRequest
- title : required, max 255
- category : required, in:medical,program,nutrition,contract,results,other
- file : required, max 5MB, mimes: pdf,doc,docx,xls,xlsx,txt,jpg,jpeg,png,webp

### StoreAssessmentRequest / UpdateAssessmentRequest
- energy_level : nullable, integer, min:1, max:10
- difficulty_level : nullable, integer, min:1, max:10
- progress_notes : nullable, max 2000
- coach_comments : nullable, max 2000
- status : required, in:pending,completed
- assessment_date : required, date, before_or_equal:today

---

## 4. Service ClientActivityService

Service central pour logger automatiquement toutes les actions sur les clients.

### Méthodes principales
```php
log($client, $type, $metadata, $description)
logClientCreated($client)
logClientUpdated($client, $changes)
logMeasurementAdded($client, $measurementId, $data)
logDocumentUploaded($client, $documentId, $title, $category)
logDocumentDeleted($client, $title)
logAssessmentCreated($client, $assessmentId, $data)
logAssessmentCompleted($client, $assessmentId)
logStatusChanged($client, $oldStatus, $newStatus)
```

### Descriptions automatiques
Le service génère des descriptions lisibles en français pour chaque type d'activité avec metadata contextuelles.

---

## 5. Controllers

### ClientController
**Routes :**
- GET `/dashboard/clients` : Liste tous les clients avec compteurs
- POST `/dashboard/clients` : Création d'un client
- GET `/dashboard/clients/{client}` : Fiche complète du client
- PATCH `/dashboard/clients/{client}` : Mise à jour
- DELETE `/dashboard/clients/{client}` : Suppression (SoftDelete)

**Sécurité :** Vérification que le client appartient bien au coach authentifié

**Features :**
- withCount(['measurements', 'documents', 'assessments'])
- Log automatique via ClientActivityService
- Redirection vers la fiche client après création

### ClientMeasurementController
**Routes :**
- POST `/dashboard/clients/{client}/measurements`
- PATCH `/dashboard/clients/{client}/measurements/{measurement}`
- DELETE `/dashboard/clients/{client}/measurements/{measurement}`

**Upload photos :** Storage dans 'measurements' (disk public)

### ClientDocumentController
**Routes :**
- POST `/dashboard/clients/{client}/documents`
- GET `/dashboard/clients/{client}/documents/{document}/download`
- DELETE `/dashboard/clients/{client}/documents/{document}`

**Storage :** disk 'private' pour sécurité des documents clients

### ClientAssessmentController
**Routes :**
- POST `/dashboard/clients/{client}/assessments`
- PATCH `/dashboard/clients/{client}/assessments/{assessment}`
- POST `/dashboard/clients/{client}/assessments/{assessment}/complete`
- DELETE `/dashboard/clients/{client}/assessments/{assessment}`

**Feature :** Action dédiée pour marquer un bilan comme complet

---

## 6. API Resources (Format JSON propre)

### ClientResource
Retourne toutes les infos client + relations chargées + compteurs

### ClientMeasurementResource
Inclut URLs publiques des photos via Storage::url()

### ClientDocumentResource
Inclut download_url, formatted_size, category_label traduit

### ClientAssessmentResource
Inclut status_label traduit, dates formatées

### ClientActivityResource
Inclut created_at_diff (diffForHumans)

---

## 7. Interface Utilisateur (Inertia/Vue)

### Index.vue (`/dashboard/clients`)
**Features :**
- 4 cards statistiques : Total, Actifs, En pause, Inactifs
- Recherche en temps réel (nom, email, phone)
- Filtre par statut
- Table responsive avec :
  * Avatar initiales
  * Statut avec badge coloré
  * Statistiques emoji (📊 mesures, 📄 documents, 📝 bilans)
  * Actions : Voir / Modifier / Supprimer
- Modal création/édition avec formulaire complet
- Empty states élégants

**Design :**
- Gradient purple pour le branding
- Dark mode complet
- Icons SVG Heroicons
- Animations smooth

### Show.vue (`/dashboard/clients/{client}`) - À créer
Structure recommandée :
```vue
<template>
  <!-- Header avec nom + statut + actions -->
  <!-- Tabs : Infos | Mesures | Documents | Bilans | Activité -->
  
  <!-- Section Informations -->
  <!-- Objectifs, notes internes, coordonnées -->
  
  <!-- Section Mesures -->
  <!-- Timeline des mesures avec graphiques progression -->
  <!-- Modal ajout mesure avec photos -->
  
  <!-- Section Documents -->
  <!-- Liste documents par catégorie -->
  <!-- Upload avec drag & drop -->
  
  <!-- Section Bilans -->
  <!-- Liste bilans avec filtres pending/completed -->
  <!-- Visualisation énergie/difficulté avec sliders -->
  
  <!-- Section Activité -->
  <!-- Timeline de toutes les actions -->
</template>
```

---

## 8. Routes Web

Toutes les routes sont protégées par middleware : `auth, verified, onboarding.completed, setup.completed`

```php
// Clients CRUD
GET    /dashboard/clients
POST   /dashboard/clients
GET    /dashboard/clients/{client}
PATCH  /dashboard/clients/{client}
DELETE /dashboard/clients/{client}

// Measurements
POST   /dashboard/clients/{client}/measurements
PATCH  /dashboard/clients/{client}/measurements/{measurement}
DELETE /dashboard/clients/{client}/measurements/{measurement}

// Documents
POST   /dashboard/clients/{client}/documents
GET    /dashboard/clients/{client}/documents/{document}/download
DELETE /dashboard/clients/{client}/documents/{document}

// Assessments
POST   /dashboard/clients/{client}/assessments
PATCH  /dashboard/clients/{client}/assessments/{assessment}
POST   /dashboard/clients/{client}/assessments/{assessment}/complete
DELETE /dashboard/clients/{client}/assessments/{assessment}
```

---

## 9. Configuration Storage

### Disks requis dans `config/filesystems.php`

```php
'disks' => [
    'public' => [
        // Pour les photos de mesures (accessibles via Storage::url())
    ],
    'private' => [
        // Pour les documents clients (téléchargement sécurisé uniquement)
        'driver' => 'local',
        'root' => storage_path('app/private'),
    ],
],
```

---

## 10. Prochaines Étapes

### Migrations
```bash
php artisan migrate
```

### Menu Navigation
Ajouter le lien "Clients" dans :
- `AuthenticatedLayout.vue` (desktop + mobile)
- Dashboard quick actions (card bleue)

### Développement UI
1. Créer `Show.vue` avec tabs et sections complètes
2. Ajouter graphiques pour évolution des mesures (Chart.js ou ApexCharts)
3. Implémenter upload drag & drop pour documents
4. Timeline visuelle des activités

### Fonctionnalités futures
- Export PDF des bilans
- Envoi d'emails automatiques au client
- Notifications push pour nouveaux bilans
- Comparaison photos avant/après côte à côte
- Dashboard analytics par client
- Templates de programmes personnalisables

---

## Documentation Technique

### Exemple d'utilisation du service

```php
use App\Services\ClientActivityService;

// Dans un contrôleur
public function __construct(
    private ClientActivityService $activityService
) {}

// Lors de la création d'une mesure
$measurement = $client->measurements()->create($validated);
$this->activityService->logMeasurementAdded($client, $measurement->id, $validated);
```

### Exemple de query avec relations

```php
$client = Client::with([
    'measurements' => fn($q) => $q->latest('measurement_date')->take(10),
    'documents' => fn($q) => $q->latest('uploaded_at'),
    'assessments' => fn($q) => $q->latest('assessment_date'),
    'activities' => fn($q) => $q->latest()->take(20),
])->findOrFail($id);
```

---

## Sécurité

### Vérifications obligatoires
Tous les contrôleurs vérifient :
1. Utilisateur authentifié
2. Utilisateur a un profil coach
3. Client appartient au coach authentifié

### Storage
- Documents clients : disk 'private' (pas d'accès direct)
- Photos mesures : disk 'public' (URLs générées)

### Validation
- Email unique par client
- Fichiers : max 5MB
- Types MIME restreints
- Dates passées uniquement

---

## Fichiers Créés

### Backend
✅ 5 Migrations (clients, measurements, documents, assessments, activities)
✅ 5 Modèles avec relations et casts
✅ 6 Form Requests
✅ 1 Service (ClientActivityService)
✅ 4 Controllers (Client, Measurement, Document, Assessment)
✅ 5 API Resources
✅ Routes complètes dans web.php

### Frontend
✅ Index.vue (liste clients avec stats, recherche, filtres, modal)
⏳ Show.vue (fiche client détaillée - à créer)

### Documentation
✅ MODULE-CLIENTS.md (ce fichier)

---

## Maintenance

### Commandes utiles

```bash
# Lancer les migrations
php artisan migrate

# Rollback dernière migration
php artisan migrate:rollback

# Créer un lien symbolique pour storage/public
php artisan storage:link

# Clear cache
php artisan cache:clear
php artisan config:clear
```

---

## Support

Pour toute question sur le module Clients, référez-vous à cette documentation ou consultez :
- Modèles : `app/Models/Client*.php`
- Controllers : `app/Http/Controllers/Dashboard/Client*.php`
- Vues : `resources/js/Pages/Dashboard/Clients/`
- Routes : `routes/web.php`
