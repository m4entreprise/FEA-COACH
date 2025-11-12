# Schéma de base de données - FEA-COACH

Documentation de la structure de la base de données.

## Tables principales

### `users`

Utilisateurs du système (coachs et admins).

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| name | varchar | Nom complet |
| email | varchar | Email unique |
| password | varchar | Mot de passe hashé |
| role | varchar | 'admin' ou 'coach' |
| coach_id | bigint | FK vers coaches (nullable) |
| email_verified_at | timestamp | Date de vérification email |
| remember_token | varchar | Token "se souvenir de moi" |
| created_at | timestamp | |
| updated_at | timestamp | |

**Relations:**
- `belongsTo` Coach (si role = 'coach')

---

### `coaches`

Profils des coachs avec contenu personnalisable.

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| user_id | bigint | FK vers users (nullable) |
| name | varchar | Nom du coach |
| slug | varchar | Slug unique pour sous-domaine |
| subdomain | varchar | Sous-domaine personnalisé (nullable) |
| color_primary | varchar | Couleur primaire (hex) |
| color_secondary | varchar | Couleur secondaire (hex) |
| hero_title | text | Titre principal du hero |
| hero_subtitle | text | Sous-titre du hero |
| about_text | longtext | Texte "À propos" |
| method_text | longtext | Texte "Ma méthode" |
| cta_text | varchar | Texte du bouton CTA |
| is_active | boolean | Coach actif/inactif |
| created_at | timestamp | |
| updated_at | timestamp | |

**Relations:**
- `belongsTo` User
- `hasMany` CoachTransformation
- `hasMany` Plan
- **Media Library**: logo, hero (images)

---

### `coach_transformations`

Galerie de transformations (avant/après) par coach.

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| coach_id | bigint | FK vers coaches |
| title | varchar | Titre de la transformation |
| description | text | Description |
| order | integer | Ordre d'affichage |
| created_at | timestamp | |
| updated_at | timestamp | |

**Relations:**
- `belongsTo` Coach
- **Media Library**: before, after (images)

---

### `plans`

Plans tarifaires proposés par chaque coach.

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| coach_id | bigint | FK vers coaches |
| name | varchar | Nom du plan |
| description | text | Description |
| price | decimal(10,2) | Prix en euros |
| cta_url | varchar | URL de réservation (Calendly, etc.) |
| is_active | boolean | Plan visible/caché |
| created_at | timestamp | |
| updated_at | timestamp | |

**Relations:**
- `belongsTo` Coach

---

## Tables Spatie

### `media`

Gestion des fichiers uploadés (Spatie Media Library).

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| model_type | varchar | Type du modèle (Coach, CoachTransformation) |
| model_id | bigint | ID du modèle |
| uuid | varchar | UUID unique |
| collection_name | varchar | Collection (logo, hero, before, after) |
| name | varchar | Nom du fichier |
| file_name | varchar | Nom physique |
| mime_type | varchar | Type MIME |
| disk | varchar | Disque de stockage |
| size | bigint | Taille en octets |
| manipulations | json | Manipulations d'image |
| custom_properties | json | Propriétés custom |
| responsive_images | json | Images responsive |
| order_column | integer | Ordre |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### `activity_log`

Logs des actions effectuées (Spatie Activity Log).

| Colonne | Type | Description |
|---------|------|-------------|
| id | bigint | Clé primaire |
| log_name | varchar | Nom du log |
| description | text | Description de l'action |
| subject_type | varchar | Type du sujet |
| subject_id | bigint | ID du sujet |
| causer_type | varchar | Type de l'auteur |
| causer_id | bigint | ID de l'auteur |
| properties | json | Propriétés additionnelles |
| event | varchar | Type d'événement |
| batch_uuid | varchar | UUID de batch |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## Tables Laravel

### `cache` & `cache_locks`

Système de cache de Laravel.

### `jobs` & `failed_jobs`

Gestion des queues et jobs échoués.

### `password_reset_tokens`

Tokens de réinitialisation de mot de passe.

### `personal_access_tokens`

Tokens d'accès API (Laravel Sanctum).

### `sessions`

Sessions utilisateurs (si driver = database).

---

## Index et contraintes

### Index uniques

- `users.email`
- `coaches.slug`
- `coaches.subdomain`

### Clés étrangères

- `coaches.user_id` → `users.id` ON DELETE CASCADE
- `users.coach_id` → `coaches.id` ON DELETE CASCADE
- `coach_transformations.coach_id` → `coaches.id` ON DELETE CASCADE
- `plans.coach_id` → `coaches.id` ON DELETE CASCADE

---

## Collections Media Library

### Coach

- **logo**: Image du logo (single file)
- **hero**: Image hero principale (single file)

### CoachTransformation

- **before**: Photo avant transformation (single file)
- **after**: Photo après transformation (single file)

---

## Commandes utiles

```bash
# Voir la structure
php artisan migrate:status

# Afficher les tables
php artisan db:show

# Afficher une table spécifique
php artisan db:table coaches

# Compter les enregistrements
php artisan tinker --execute="
echo 'Coaches: ' . App\Models\Coach::count() . PHP_EOL;
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'Plans: ' . App\Models\Plan::count() . PHP_EOL;
echo 'Transformations: ' . App\Models\CoachTransformation::count() . PHP_EOL;
"

# Afficher les coaches avec leurs relations
php artisan tinker --execute="
App\Models\Coach::with('user', 'plans', 'transformations')->get()
"
```

---

_Document créé le 12 novembre 2025_
