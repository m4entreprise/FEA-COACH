# Plan d'intégration Stripe - Réservation & Paiement de séances

**Version** : 1.0  
**Date** : Janvier 2026  
**Statut** : Spécification  
**Tarification** : Module premium à **5€ HTVA/mois** (en plus de l'abonnement de base)

---

## 🎯 Vision & Objectifs

### Proposition de valeur
Permettre aux coachs UNICOACH d'**encaisser directement les paiements** de leurs clients pour leurs séances de coaching, sans friction technique, avec un système de réservation intégré.

### Objectifs business
- Transformer UNICOACH en plateforme business complète (site + CRM + paiement)
- Générer un revenu additionnel de 5€/mois par coach utilisateur
- Différenciation concurrentielle forte
- Faciliter le passage à l'action des coachs diplômés FEA

### Métriques de succès
- **Adoption** : 30% des coachs actifs en 3 mois
- **Utilisation** : 5+ réservations/coach/mois en moyenne
- **Satisfaction** : NPS > 8/10
- **Churn** : < 5% sur le module premium

---

## 💰 Modèle économique

### Tarification UNICOACH

| Formule | Base mensuelle | Module Paiements | Total |
|---------|----------------|------------------|-------|
| Coach FEA | 20€ HTVA | +5€ HTVA | **25€ HTVA** |
| Coach non-FEA | 30€ HTVA | +5€ HTVA | **35€ HTVA** |

### Commissions sur transactions

**Phase 1 (lancement - 6 premiers mois)** : 0% de commission
- Argument commercial : "On ne prend rien sur vos revenus"
- Focus adoption maximale

**Phase 2 (après 6 mois)** : 2% de commission (optionnel)
- Annonce anticipée aux utilisateurs
- Clause d'antériorité pour early adopters (restent à 0% à vie)

### Frais Stripe (à charge du coach)
- **Cartes européennes** : 1,4% + 0,25€
- **Cartes hors UE** : 2,9% + 0,25€
- **Apple/Google Pay** : 1,4% + 0,25€

### Exemples financiers

**Coach facturant 50€/séance, 20 séances/mois**
- Chiffre d'affaires : 1 000€
- Frais Stripe : ~19€ (1,9%)
- Commission UNICOACH : 0€ (phase 1) ou 20€ (phase 2)
- Abonnement UNICOACH : 25€
- **Net coach** : 956€ (phase 1) ou 936€ (phase 2)
- **ROI module** : +956€ CA vs 5€ coût = excellent

---

## 🏗️ Architecture technique

### 1. Stripe Connect - Platform Model

**Choix architecture** : Stripe Connect avec **Standard Accounts**

#### Pourquoi Standard Accounts ?
✅ Chaque coach a son propre dashboard Stripe  
✅ Conformité légale simplifiée (Stripe gère le KYC)  
✅ Coach garde contrôle total de ses fonds  
✅ UNICOACH évite le statut d'établissement de paiement  
✅ Paiements versés directement au coach (2-7 jours)  

#### Flux de connexion
```
Coach dashboard → "Activer les paiements"
    ↓
Vérification : abonnement inclut module paiements
    ↓
Redirection Stripe Connect Onboarding
    ↓
Coach crée/lie compte Stripe (KYC automatique)
    ↓
Stripe renvoie stripe_account_id
    ↓
Stockage en BDD → Coach peut recevoir paiements
```

### 2. Base de données - Nouvelles tables

#### Table `stripe_accounts`
```sql
CREATE TABLE stripe_accounts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    coach_id BIGINT UNSIGNED UNIQUE NOT NULL,
    stripe_account_id VARCHAR(255) UNIQUE NOT NULL,
    onboarding_completed BOOLEAN DEFAULT FALSE,
    charges_enabled BOOLEAN DEFAULT FALSE,
    payouts_enabled BOOLEAN DEFAULT FALSE,
    details_submitted BOOLEAN DEFAULT FALSE,
    country VARCHAR(2),
    currency VARCHAR(3) DEFAULT 'EUR',
    business_type VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
);
```

#### Table `service_types`
```sql
CREATE TABLE service_types (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    coach_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'EUR',
    is_active BOOLEAN DEFAULT TRUE,
    booking_enabled BOOLEAN DEFAULT TRUE,
    max_advance_booking_days INT DEFAULT 60,
    min_advance_booking_hours INT DEFAULT 24,
    order INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
);
```

#### Table `availability_slots`
```sql
CREATE TABLE availability_slots (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    coach_id BIGINT UNSIGNED NOT NULL,
    day_of_week TINYINT NOT NULL, -- 0=dimanche, 1=lundi, ...
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (coach_id, day_of_week, start_time)
);
```

#### Table `bookings`
```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    coach_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NULL,
    service_type_id BIGINT UNSIGNED NOT NULL,
    
    -- Informations client (si non enregistré dans CRM)
    client_first_name VARCHAR(255),
    client_last_name VARCHAR(255),
    client_email VARCHAR(255) NOT NULL,
    client_phone VARCHAR(50),
    
    -- Réservation
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    duration_minutes INT NOT NULL,
    
    -- Paiement
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'EUR',
    stripe_payment_intent_id VARCHAR(255),
    stripe_charge_id VARCHAR(255),
    payment_status ENUM('pending', 'succeeded', 'failed', 'refunded') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    
    -- Gestion
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    cancellation_reason TEXT,
    cancelled_at TIMESTAMP NULL,
    cancelled_by ENUM('coach', 'client', 'system') NULL,
    
    -- Notes
    client_notes TEXT,
    coach_notes TEXT,
    
    -- Reminders
    reminder_sent_at TIMESTAMP NULL,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (service_type_id) REFERENCES service_types(id) ON DELETE CASCADE,
    
    INDEX idx_coach_date (coach_id, booking_date),
    INDEX idx_client_email (client_email),
    INDEX idx_payment_status (payment_status),
    INDEX idx_status (status)
);
```

#### Table `booking_cancellation_policies`
```sql
CREATE TABLE booking_cancellation_policies (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    coach_id BIGINT UNSIGNED UNIQUE NOT NULL,
    hours_before_free_cancellation INT DEFAULT 24,
    refund_percentage_before_deadline INT DEFAULT 100,
    refund_percentage_after_deadline INT DEFAULT 0,
    no_show_refund_percentage INT DEFAULT 0,
    policy_text TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (coach_id) REFERENCES coaches(id) ON DELETE CASCADE
);
```

### 3. Modèles Laravel

#### StripeAccount.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeAccount extends Model
{
    protected $fillable = [
        'coach_id',
        'stripe_account_id',
        'onboarding_completed',
        'charges_enabled',
        'payouts_enabled',
        'details_submitted',
        'country',
        'currency',
        'business_type',
    ];

    protected $casts = [
        'onboarding_completed' => 'boolean',
        'charges_enabled' => 'boolean',
        'payouts_enabled' => 'boolean',
        'details_submitted' => 'boolean',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function isFullyActivated(): bool
    {
        return $this->onboarding_completed 
            && $this->charges_enabled 
            && $this->payouts_enabled 
            && $this->details_submitted;
    }
}
```

#### ServiceType.php, Booking.php, etc.
(Modèles standards avec relations)

### 4. Services

#### StripeConnectService.php
```php
<?php

namespace App\Services;

use App\Models\Coach;
use App\Models\StripeAccount;
use Stripe\StripeClient;

class StripeConnectService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.secret'));
    }

    /**
     * Créer un lien d'onboarding Stripe Connect
     */
    public function createAccountLink(Coach $coach): string
    {
        $stripeAccount = $coach->stripeAccount;
        
        if (!$stripeAccount) {
            $account = $this->createConnectedAccount($coach);
            $stripeAccount = StripeAccount::create([
                'coach_id' => $coach->id,
                'stripe_account_id' => $account->id,
            ]);
        }

        $accountLink = $this->stripe->accountLinks->create([
            'account' => $stripeAccount->stripe_account_id,
            'refresh_url' => route('dashboard.payments.stripe.refresh'),
            'return_url' => route('dashboard.payments.stripe.return'),
            'type' => 'account_onboarding',
        ]);

        return $accountLink->url;
    }

    /**
     * Créer un Payment Intent pour une réservation
     */
    public function createPaymentIntent(Booking $booking): array
    {
        $coach = $booking->coach;
        $stripeAccount = $coach->stripeAccount;

        if (!$stripeAccount || !$stripeAccount->isFullyActivated()) {
            throw new \Exception('Coach Stripe account not activated');
        }

        $amount = (int) ($booking->amount * 100); // Convertir en centimes

        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => $booking->currency,
            'application_fee_amount' => $this->calculatePlatformFee($amount),
            'transfer_data' => [
                'destination' => $stripeAccount->stripe_account_id,
            ],
            'metadata' => [
                'booking_id' => $booking->id,
                'coach_id' => $coach->id,
                'client_email' => $booking->client_email,
            ],
        ]);

        return $paymentIntent->toArray();
    }

    /**
     * Calculer la commission plateforme
     */
    protected function calculatePlatformFee(int $amount): int
    {
        $commissionRate = config('stripe.platform_commission_rate', 0); // 0% phase 1, 2% phase 2
        return (int) ($amount * $commissionRate);
    }

    // ... autres méthodes
}
```

#### BookingService.php
```php
<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Coach;
use App\Models\ServiceType;
use Carbon\Carbon;

class BookingService
{
    /**
     * Vérifier disponibilité d'un créneau
     */
    public function isSlotAvailable(
        Coach $coach, 
        Carbon $date, 
        string $startTime, 
        int $durationMinutes
    ): bool {
        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end = $start->copy()->addMinutes($durationMinutes);

        // Vérifier qu'il n'y a pas de réservation existante
        $existingBooking = Booking::where('coach_id', $coach->id)
            ->where('booking_date', $date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                    ->orWhereBetween('end_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_time', '<=', $start->format('H:i:s'))
                          ->where('end_time', '>=', $end->format('H:i:s'));
                    });
            })
            ->exists();

        return !$existingBooking;
    }

    /**
     * Créer une réservation
     */
    public function createBooking(array $data): Booking
    {
        // Validation, création, etc.
    }

    // ... autres méthodes
}
```

### 5. Controllers

#### Dashboard/PaymentsController.php
Gestion du compte Stripe du coach

#### Dashboard/ServicesController.php
CRUD des types de services proposés

#### Dashboard/AvailabilityController.php
Configuration des disponibilités

#### Dashboard/BookingsController.php
Gestion des réservations côté coach

#### Public/BookingController.php
Interface de réservation publique

#### StripeWebhookController.php
Gestion des webhooks Stripe

---

## 🎨 Interface utilisateur

### Dashboard Coach - Nouvel onglet "Paiements"

#### 1. Page d'activation
```
┌─────────────────────────────────────────────┐
│  💳 Paiements & Réservations                │
│                                             │
│  Module premium - 5€/mois                   │
│  ❌ Non activé                              │
│                                             │
│  ┌────────────────────────────────────┐    │
│  │ ✨ Activez le module premium       │    │
│  │                                     │    │
│  │ ✓ Encaissez vos séances en ligne   │    │
│  │ ✓ Système de réservation intégré   │    │
│  │ ✓ Calendrier de disponibilités     │    │
│  │ ✓ Paiements sécurisés par Stripe   │    │
│  │                                     │    │
│  │ [Activer pour 5€/mois] ────────→   │    │
│  └────────────────────────────────────┘    │
└─────────────────────────────────────────────┘
```

#### 2. Page de connexion Stripe (module activé)
```
┌─────────────────────────────────────────────┐
│  💳 Paiements & Réservations                │
│                                             │
│  Module premium activé ✅                   │
│                                             │
│  ┌────────────────────────────────────┐    │
│  │ 🔗 Connecter votre compte Stripe   │    │
│  │                                     │    │
│  │ Pour recevoir les paiements de vos │    │
│  │ clients, vous devez connecter un    │    │
│  │ compte Stripe.                      │    │
│  │                                     │    │
│  │ ⏱️ Temps estimé : 10 minutes        │    │
│  │ 📋 Documents nécessaires :          │    │
│  │   • Pièce d'identité                │    │
│  │   • SIRET / n° TVA                  │    │
│  │   • Coordonnées bancaires (IBAN)    │    │
│  │                                     │    │
│  │ [Connecter mon compte Stripe] ───→  │    │
│  └────────────────────────────────────┘    │
└─────────────────────────────────────────────┘
```

#### 3. Dashboard paiements (connecté)
```
┌─────────────────────────────────────────────────────────┐
│  💳 Paiements & Réservations                            │
│                                                         │
│  Compte Stripe : ✅ Connecté et activé                  │
│  [Voir mon dashboard Stripe →]  [Déconnecter]          │
│                                                         │
│  ─────────────────────────────────────────────────     │
│                                                         │
│  📊 Ce mois-ci                                          │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐              │
│  │  1 240€  │ │    24    │ │   96%    │              │
│  │  Revenus │ │ Séances  │ │  Payé    │              │
│  └──────────┘ └──────────┘ └──────────┘              │
│                                                         │
│  ─────────────────────────────────────────────────     │
│                                                         │
│  [📅 Disponibilités] [🎯 Mes services] [📋 Réservations]│
│                                                         │
└─────────────────────────────────────────────────────────┘
```

#### 4. Configuration des services
```
┌─────────────────────────────────────────────────────────┐
│  🎯 Mes services                                        │
│                                                         │
│  [+ Ajouter un service]                                 │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 🏋️ Séance découverte                 [Modifier] │   │
│  │ Première séance d'évaluation                     │   │
│  │ ⏱️ 60 min  •  💰 50€  •  ✅ Actif                │   │
│  │ Réservable jusqu'à 24h à l'avance               │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 💪 Séance de suivi                  [Modifier]  │   │
│  │ Séance de coaching personnalisé                  │   │
│  │ ⏱️ 45 min  •  💰 70€  •  ✅ Actif                │   │
│  │ Réservable jusqu'à 12h à l'avance               │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

#### 5. Gestion des disponibilités
```
┌─────────────────────────────────────────────────────────┐
│  📅 Mes disponibilités                                  │
│                                                         │
│  Définissez vos créneaux hebdomadaires                  │
│                                                         │
│  Lundi      ☑ Actif                                     │
│  ├─ 09:00 - 12:00  [Modifier] [Supprimer]              │
│  ├─ 14:00 - 18:00  [Modifier] [Supprimer]              │
│  └─ [+ Ajouter un créneau]                              │
│                                                         │
│  Mardi      ☑ Actif                                     │
│  ├─ 09:00 - 17:00  [Modifier] [Supprimer]              │
│  └─ [+ Ajouter un créneau]                              │
│                                                         │
│  Mercredi   ☐ Jour de repos                             │
│                                                         │
│  [+ Ajouter des exceptions] (vacances, jours fériés)    │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

#### 6. Liste des réservations
```
┌─────────────────────────────────────────────────────────┐
│  📋 Réservations                                        │
│                                                         │
│  [À venir] [Passées] [Annulées]                         │
│                                                         │
│  Jeudi 3 janvier 2026                                   │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 09:00 - 10:00  •  Séance découverte             │   │
│  │ 👤 Marie Dupont  •  marie@email.com             │   │
│  │ 📞 06 12 34 56 78  •  💰 50€ payé ✅            │   │
│  │ 💬 "Première fois en coaching"                   │   │
│  │ [Voir détails] [Annuler] [Reprogrammer]         │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  Jeudi 3 janvier 2026                                   │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 14:00 - 14:45  •  Séance de suivi               │   │
│  │ 👤 Jean Martin (client existant)                │   │
│  │ 💰 70€ payé ✅                                   │   │
│  │ [Voir détails] [Ajouter notes]                   │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Site public coach - Page de réservation

#### 1. Sélection du service
```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│            Réserver une séance avec                     │
│              [Prénom Nom Coach]                         │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 🏋️ Séance découverte           50€              │   │
│  │ Première séance d'évaluation                     │   │
│  │ ⏱️ 60 minutes                                     │   │
│  │                                                  │   │
│  │ [Réserver ce créneau] ─────────────────→        │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 💪 Séance de suivi              70€              │   │
│  │ Coaching personnalisé                            │   │
│  │ ⏱️ 45 minutes                                     │   │
│  │                                                  │   │
│  │ [Réserver ce créneau] ─────────────────→        │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

#### 2. Sélection date/heure
```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│  ← Retour     Séance découverte - 50€                   │
│                                                         │
│  📅 Choisissez une date                                 │
│                                                         │
│  [< Janvier 2026 >]                                     │
│                                                         │
│   L   M   M   J   V   S   D                            │
│        1   2  [3]  4   5   6                           │
│   7   8   9  10  11  12  13                            │
│  14  15  16  17  18  19  20                            │
│                                                         │
│  ⏰ Créneaux disponibles le jeudi 3 janvier            │
│                                                         │
│  [ 09:00 ]  [ 10:30 ]  [ 14:00 ]  [ 15:30 ]           │
│                                                         │
│  [Continuer] ──────────────────→                       │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

#### 3. Informations client
```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│  ← Retour     Séance découverte                         │
│               Jeudi 3 janvier 2026 à 09:00              │
│                                                         │
│  👤 Vos informations                                    │
│                                                         │
│  ┌────────────────────────────────────────────────┐    │
│  │ Prénom *                                       │    │
│  │ [                                              ]│    │
│  └────────────────────────────────────────────────┘    │
│                                                         │
│  ┌────────────────────────────────────────────────┐    │
│  │ Nom *                                          │    │
│  │ [                                              ]│    │
│  └────────────────────────────────────────────────┘    │
│                                                         │
│  ┌────────────────────────────────────────────────┐    │
│  │ Email *                                        │    │
│  │ [                                              ]│    │
│  └────────────────────────────────────────────────┘    │
│                                                         │
│  ┌────────────────────────────────────────────────┐    │
│  │ Téléphone *                                    │    │
│  │ [                                              ]│    │
│  └────────────────────────────────────────────────┘    │
│                                                         │
│  ┌────────────────────────────────────────────────┐    │
│  │ Message (optionnel)                            │    │
│  │ [                                              ]│    │
│  │ [                                              ]│    │
│  └────────────────────────────────────────────────┘    │
│                                                         │
│  [Continuer vers le paiement] ──────────→              │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

#### 4. Paiement (Stripe Checkout embedded)
```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│  💳 Paiement sécurisé                                   │
│                                                         │
│  Récapitulatif                                          │
│  ├─ Séance découverte                           50,00€  │
│  ├─ Durée : 60 minutes                                  │
│  ├─ Date : Jeudi 3 janvier 2026 à 09:00                │
│  └─ Coach : [Prénom Nom]                                │
│                                                         │
│  ─────────────────────────────────────────────────     │
│                                                         │
│  [Stripe Payment Element integré ici]                   │
│                                                         │
│  ☑ J'accepte les conditions d'annulation               │
│                                                         │
│  [Payer 50€] ────────────────────→                     │
│                                                         │
│  🔒 Paiement sécurisé par Stripe                        │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

#### 5. Confirmation
```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│              ✅ Réservation confirmée !                 │
│                                                         │
│  Votre séance a été réservée avec succès.               │
│                                                         │
│  📧 Un email de confirmation a été envoyé à             │
│     votre adresse : marie@email.com                     │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 📅 Jeudi 3 janvier 2026                          │   │
│  │ ⏰ 09:00 - 10:00                                 │   │
│  │ 🎯 Séance découverte                             │   │
│  │ 👤 Avec [Prénom Nom Coach]                       │   │
│  │ 💰 50€ payé                                      │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  [📥 Ajouter à mon calendrier]                          │
│                                                         │
│  ℹ️ Vous recevrez un rappel 24h avant la séance.        │
│                                                         │
│  [Retour au site du coach]                              │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Espace client - Mes réservations

```
┌─────────────────────────────────────────────────────────┐
│  📋 Mes réservations                                    │
│                                                         │
│  Prochaine séance                                       │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 📅 Jeudi 3 janvier 2026                          │   │
│  │ ⏰ 09:00 - 10:00                                 │   │
│  │ 🎯 Séance découverte                             │   │
│  │ 💰 50€ payé ✅                                   │   │
│  │                                                  │   │
│  │ [📥 Calendrier] [❌ Annuler]                     │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  Historique                                             │
│  ┌─────────────────────────────────────────────────┐   │
│  │ 📅 Lundi 15 décembre 2025                        │   │
│  │ 🎯 Séance de suivi  •  ✅ Complétée             │   │
│  │ [Voir détails] [📄 Facture]                      │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 📋 Fonctionnalités détaillées

### Phase 1 - MVP (2-3 semaines)

#### Fonctionnalités MVP
- ✅ Activation du module premium (5€/mois)
- ✅ Connexion Stripe Connect (Standard Accounts)
- ✅ CRUD types de services (nom, durée, prix)
- ✅ Configuration disponibilités hebdomadaires basique
- ✅ Page publique de réservation
- ✅ Paiement Stripe Checkout
- ✅ Confirmation par email (coach + client)
- ✅ Liste des réservations dashboard coach
- ✅ Webhooks Stripe basiques (payment_intent.succeeded)

#### Exclusions MVP
- ❌ Pas de calendrier complexe (coach recontacte pour confirmer)
- ❌ Pas d'annulation client
- ❌ Pas de remboursements auto
- ❌ Pas d'intégration Google/Outlook Calendar
- ❌ Pas de rappels SMS

### Phase 2 - Calendrier & Disponibilités (2 semaines)

- ✅ Calendrier temps réel des créneaux disponibles
- ✅ Gestion des exceptions (congés, jours fériés)
- ✅ Bloquage automatique des créneaux réservés
- ✅ Vue calendrier dans dashboard coach
- ✅ Filtres et recherche avancée réservations

### Phase 3 - Annulations & Remboursements (1-2 semaines)

- ✅ Politique d'annulation configurable par coach
- ✅ Annulation client depuis espace perso
- ✅ Remboursements automatiques/partiels selon délai
- ✅ Gestion des no-show
- ✅ Historique des annulations

### Phase 4 - Fonctionnalités avancées (2-3 semaines)

- ✅ Rappels automatiques (email 24h avant, 2h avant)
- ✅ Intégration Google Calendar / Outlook
- ✅ Visioconférence intégrée (Zoom/Meet)
- ✅ Packs/forfaits (ex: 10 séances)
- ✅ Abonnements mensuels (X séances/mois)
- ✅ Codes promo / réductions
- ✅ Notes et compte-rendu post-séance
- ✅ Évaluation client après séance

---

## 🔒 Sécurité & Conformité

### Sécurité des paiements
- ✅ Aucune donnée bancaire stockée côté UNICOACH
- ✅ Stripe Checkout/Elements uniquement (PCI-DSS compliant)
- ✅ Validation signature webhooks obligatoire
- ✅ HTTPS obligatoire sur toutes les pages de paiement
- ✅ 3D Secure activé par défaut

### Conformité légale
- ✅ CGV adaptées pour inclure paiements/réservations
- ✅ Politique d'annulation clairement affichée
- ✅ Facturation automatique (conformité fiscale)
- ✅ Mention "paiement sécurisé par Stripe"
- ✅ Droit de rétractation 14 jours (sauf renonciation)

### RGPD
- ✅ Données minimales collectées
- ✅ Consentement explicite stockage données
- ✅ Droit d'accès/rectification/suppression
- ✅ Durée de conservation définie (3 ans comptables)

### KYC (Know Your Customer)
- ✅ Géré intégralement par Stripe
- ✅ Vérification identité automatique
- ✅ Conformité anti-blanchiment (AML)
- ✅ Blocage automatique si compte non vérifié

---

## 📊 Tracking & Analytics

### Métriques coach (dashboard)
- Revenus du mois/trimestre/année
- Nombre de réservations
- Taux d'occupation (créneaux remplis vs disponibles)
- Taux d'annulation
- Revenu moyen par séance
- Services les plus réservés

### Métriques plateforme (admin)
- Nombre de coachs avec module activé
- Volume de transactions total
- Commissions générées (phase 2)
- Taux d'abandon panier
- Taux de conversion site → réservation
- Problèmes de paiement (échecs, litiges)

---

## 🚀 Plan de déploiement

### Timeline globale

**Janvier 2026** : Spécifications & Design (2 semaines)
- Finalisation spec technique
- Maquettes UI/UX
- Validation partenaires FEA

**Février 2026** : Développement MVP (3 semaines)
- Backend : migrations, modèles, services
- Frontend : dashboard coach + page réservation
- Tests unitaires

**Mars 2026** : Beta privée (2 semaines)
- 5-10 coachs FEA volontaires
- Tests en conditions réelles
- Corrections bugs
- Documentation

**Avril 2026** : Release publique
- Déploiement production
- Communication à tous les coachs
- Webinar de présentation
- Support dédié

**Mai-Juin 2026** : Itération & Phases 2-3
- Développement fonctionnalités avancées
- Feedback utilisateurs

### Critères de lancement

**Bloquants** (doit être parfait)
- ✅ Sécurité paiements 100% validée
- ✅ Webhooks testés en profondeur
- ✅ Gestion d'erreurs exhaustive
- ✅ Emails transactionnels fonctionnels
- ✅ Documentation complète

**Nice-to-have** (peut attendre)
- Calendrier avancé
- Intégrations externes
- Rappels SMS

---

## 💡 Points d'attention & Risques

### Risques techniques (faibles)
| Risque | Impact | Probabilité | Mitigation |
|--------|--------|-------------|------------|
| Bug webhook critique | Élevé | Faible | Tests exhaustifs, logs détaillés, alertes |
| Surcharge serveur (pics) | Moyen | Faible | Architecture scalable, monitoring |
| Échec paiement | Moyen | Moyen | Gestion d'erreur claire, retry automatique |

### Risques business (moyens)
| Risque | Impact | Probabilité | Mitigation |
|--------|--------|-------------|------------|
| Adoption faible | Élevé | Moyen | Pricing attractif, communication forte, testimonials |
| Complexité onboarding Stripe | Moyen | Moyen | Tutoriels vidéo, support dédié, FAQ complète |
| Litiges client-coach | Moyen | Moyen | CGV claires, process de médiation, support réactif |

### Risques légaux (faibles si bien géré)
| Risque | Impact | Probabilité | Mitigation |
|--------|--------|-------------|------------|
| Non-conformité KYC | Élevé | Très faible | Stripe gère intégralement |
| Litiges fiscaux coachs | Moyen | Faible | Disclaimer clair, coach responsable de sa fiscalité |
| RGPD | Moyen | Faible | Conformité dès conception, DPO si nécessaire |

---

## 📝 Documentation nécessaire

### Pour les coachs
1. **Guide de démarrage**
   - Comment activer le module
   - Créer son compte Stripe
   - Configurer ses services
   - Gérer ses disponibilités

2. **Guide d'utilisation**
   - Gérer les réservations
   - Annuler/reprogrammer
   - Suivre ses revenus
   - Résoudre les problèmes

3. **FAQ**
   - Combien ça coûte ?
   - Quand je reçois mes paiements ?
   - Comment gérer les remboursements ?
   - Fiscalité et déclarations

4. **Tutoriels vidéo**
   - Activation module (5 min)
   - Connexion Stripe (10 min)
   - Première réservation (8 min)

### Support technique
1. **Runbook opérationnel**
   - Gestion des alertes
   - Process de résolution incidents
   - Escalade problèmes Stripe

2. **Base de connaissance support**
   - Problèmes courants + solutions
   - Scripts de réponse
   - Contact Stripe support

---

## 🎯 KPIs de succès

### Objectifs à 3 mois
- **Adoption** : 30% des coachs actifs (environ 50+ coachs si 150 actifs)
- **Utilisation** : 5+ réservations/coach/mois
- **Revenus** : 250€/mois revenus additionnels (50 coachs × 5€)
- **Satisfaction** : NPS > 8/10
- **Support** : < 2 tickets/jour liés au module

### Objectifs à 6 mois
- **Adoption** : 50% des coachs actifs
- **Volume transactions** : 500+ réservations/mois total plateforme
- **GMV** (Gross Merchandise Value) : 30 000€/mois transités
- **Churn module** : < 5%
- **Revenus** : 500€/mois abonnements + commissions (phase 2)

### Objectifs à 1 an
- **Adoption** : 70% des coachs actifs
- **GMV** : 100 000€/mois
- **Feature complete** : Phases 1-4 déployées
- **Intégrations tierces** : Google Calendar, Zoom, etc.
- **Revenus** : 1000€+/mois module paiements

---

## 💰 Business case UNICOACH

### Investissement estimé
- **Développement** : 6-8 semaines dev (1 dev fullstack)
- **Design/UX** : 1 semaine
- **Tests/QA** : 1 semaine
- **Documentation** : 1 semaine
- **Total** : ~300-400h de travail

### ROI prévisionnel

**Scénario conservateur** (30% adoption à 6 mois)
- 150 coachs actifs × 30% = 45 coachs
- 45 × 5€/mois = **225€/mois revenus additionnels**
- ROI : 6-8 mois pour amortir développement

**Scénario optimiste** (50% adoption à 6 mois + commission 2%)
- 150 coachs actifs × 50% = 75 coachs
- Abonnements : 75 × 5€ = 375€/mois
- GMV : 75 coachs × 20 séances × 60€ = 90 000€/mois
- Commissions (2%) : 90 000 × 2% = 1 800€/mois
- **Total : 2 175€/mois revenus additionnels**
- ROI : 3-4 mois

**Scénario très optimiste** (70% adoption à 1 an)
- 300 coachs actifs × 70% = 210 coachs
- Abonnements : 210 × 5€ = 1 050€/mois
- GMV : 210 × 25 séances × 65€ = 341 250€/mois
- Commissions (2%) : ~6 825€/mois
- **Total : ~7 875€/mois revenus additionnels**

### Bénéfices indirects
- **Différenciation concurrentielle** : rare sur marché
- **Rétention améliorée** : lock-in via historique financier
- **Justification pricing** : peut augmenter abonnement de base
- **Attractivité FEA** : argument massif pour partenariat

---

## 📞 Support & Contact

### Ressources développement
- **Lead dev** : À définir
- **Reviewer** : À définir
- **QA** : À définir

### Contacts externes
- **Stripe Support** : support@stripe.com
- **Stripe docs** : https://stripe.com/docs/connect
- **FEA Contact** : À définir

---

**Document évolutif - Version 1.0**  
**Dernière mise à jour** : 2 janvier 2026  
**Prochain review** : Après beta test mars 2026
