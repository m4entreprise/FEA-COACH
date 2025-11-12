# Gestion des Plans Tarifaires ✅

## 🎯 Fonctionnalité ajoutée

Un système complet de gestion des plans tarifaires a été ajouté au dashboard des coachs.

## 📦 Fichiers créés

### Backend
- `app/Http/Controllers/Dashboard/PlansController.php` - Contrôleur CRUD pour les plans

### Frontend
- `resources/js/Pages/Dashboard/Plans.vue` - Interface de gestion des plans

### Routes
- `GET /dashboard/plans` - Liste des plans
- `POST /dashboard/plans` - Créer un plan
- `PATCH /dashboard/plans/{plan}` - Mettre à jour un plan
- `DELETE /dashboard/plans/{plan}` - Supprimer un plan

## ✨ Fonctionnalités

### 1. Liste des plans
- Affichage en grille responsive (1/2/3 colonnes)
- Carte pour chaque plan avec :
  - Badge de statut (Actif/Inactif)
  - Nom du plan
  - Prix (ou "Prix sur demande" si non renseigné)
  - Description
  - Lien CTA (Calendly, etc.)
  - Boutons Modifier et Supprimer

### 2. Création de plan
- Modal avec formulaire
- Champs :
  - **Nom** (requis) : Ex: "Découverte", "Suivi Mensuel"
  - **Prix** (optionnel) : Montant en euros avec 2 décimales
  - **Description** (optionnel) : Détails du plan
  - **URL CTA** (optionnel) : Lien vers Calendly, formulaire, etc.
  - **Statut** : Actif/Inactif (checkbox)

### 3. Modification de plan
- Même modal que la création
- Pré-rempli avec les données existantes
- Mise à jour en temps réel

### 4. Suppression de plan
- Confirmation avant suppression
- Suppression définitive

### 5. Sécurité
- Vérification que le plan appartient bien au coach
- Erreur 403 si tentative d'accès à un plan d'un autre coach
- Filtrage automatique par `coach_id`

## 🎨 Interface

### Navigation
Le menu "Plans" a été ajouté :
- Dans le menu principal (desktop)
- Dans le menu hamburger (mobile)
- Dans les "Actions rapides" du dashboard (carte jaune)

### Design
- Cards modernes avec hover effects
- Badge de statut coloré (vert/gris)
- Modal responsive pour création/édition
- Empty state élégant quand aucun plan

### Couleurs
- Plans : Jaune (`bg-yellow-100`)
- Actif : Vert
- Inactif : Gris

## 📊 Données

### Modèle Plan
```php
$fillable = [
    'coach_id',    // Relation avec le coach
    'name',        // Nom du plan
    'description', // Description détaillée (nullable)
    'price',       // Prix en décimal (nullable)
    'cta_url',     // URL de réservation (nullable)
    'is_active',   // Visible sur le site (boolean)
]
```

### Validation
- **name** : requis, max 255 caractères
- **description** : optionnel, max 1000 caractères
- **price** : optionnel, numérique, 0-99999.99
- **cta_url** : optionnel, URL valide, max 500 caractères
- **is_active** : booléen

## 🚀 Utilisation

### Pour tester

1. **Se connecter en tant que coach** :
   ```
   Email: pierre@example.com
   Password: password
   ```

2. **Accéder à la gestion des plans** :
   - Menu "Plans" dans la navigation
   - OU carte "Plans" dans le dashboard

3. **Créer un plan** :
   - Cliquer sur "Nouveau Plan"
   - Remplir le formulaire
   - Cliquer sur "Créer"

4. **Modifier un plan** :
   - Cliquer sur "Modifier" dans une carte
   - Modifier les champs
   - Cliquer sur "Mettre à jour"

5. **Supprimer un plan** :
   - Cliquer sur "Supprimer" dans une carte
   - Confirmer la suppression

### Exemples de plans

#### Plan 1 : Découverte
```
Nom: Découverte
Prix: 49.99
Description: Séance d'essai pour découvrir le coaching
URL CTA: https://calendly.com/coach/decouverte
Statut: Actif
```

#### Plan 2 : Suivi Mensuel
```
Nom: Suivi Mensuel
Prix: 199.99
Description: 4 séances par mois + Programme nutritionnel
URL CTA: https://calendly.com/coach/mensuel
Statut: Actif
```

#### Plan 3 : Transformation 3 mois
```
Nom: Transformation 3 mois
Prix: 549.99
Description: 12 séances + Plan nutritionnel personnalisé + Suivi quotidien
URL CTA: https://calendly.com/coach/transformation
Statut: Actif
```

#### Plan 4 : Premium VIP
```
Nom: Premium VIP
Prix: (vide - Prix sur demande)
Description: Séances illimitées + Disponibilité 7j/7
URL CTA: https://calendly.com/coach/vip
Statut: Inactif
```

## 🎯 Intégration site public

Les plans créés sont automatiquement affichés sur le site public du coach dans la section "Tarifs" (déjà implémentée).

**Filtrage** : Seuls les plans avec `is_active = true` sont affichés publiquement.

## 📝 Messages de succès

- ✅ "Plan créé avec succès."
- ✅ "Plan mis à jour avec succès."
- ✅ "Plan supprimé avec succès."

## 🔒 Sécurité

### Vérifications
1. Utilisateur authentifié et vérifié
2. Utilisateur a un profil coach associé
3. Plan appartient bien au coach (sur update/delete)

### Protection
- Middleware `auth` et `verified`
- Vérification `coach_id` dans le contrôleur
- Abort 403 si tentative d'accès non autorisé

## 📊 Données de test

Les seeders ont déjà créé des plans pour les coachs de test :
- **Pierre Martin** : 4 plans (3 actifs)
- **Sophie Dubois** : 4 plans (3 actifs)
- **Thomas Leroy** : 0 plan (compte inactif)

## ✅ Checklist

- [x] Créer PlansController avec CRUD complet
- [x] Ajouter les routes dans web.php
- [x] Créer la page Plans.vue avec modal
- [x] Ajouter le menu "Plans" dans AuthenticatedLayout
- [x] Ajouter la carte "Plans" dans le dashboard
- [x] Validation des données
- [x] Sécurité et vérifications
- [x] Messages de succès
- [x] Empty state
- [x] Responsive design
- [x] Documentation

---

**Date** : 12 novembre 2025  
**Statut** : ✅ Complété et fonctionnel
