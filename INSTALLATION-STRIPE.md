# Installation et Configuration - Module Stripe Paiements & Réservations

## 📋 Prérequis

- PHP 8.2+
- Composer
- Node.js 20+
- Base de données (MySQL/SQLite)
- Compte Stripe (test et production)

## 🚀 Installation

### 1. Installer les dépendances

Si pas déjà fait lors de l'installation initiale:

```bash
composer install
npm install
```

### 2. Configuration environnement

Ajouter les variables Stripe dans `.env`:

```env
# Stripe Configuration (for payments module)
STRIPE_PUBLIC_KEY=pk_test_xxxxx
STRIPE_SECRET_KEY=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
STRIPE_PLATFORM_COMMISSION_RATE=0.00
STRIPE_CURRENCY=EUR
```

**Obtenir les clés Stripe:**
1. Créer un compte sur https://dashboard.stripe.com
2. Mode Test: Dashboard → Developers → API keys
3. Copier "Publishable key" → `STRIPE_PUBLIC_KEY`
4. Copier "Secret key" → `STRIPE_SECRET_KEY`

### 3. Exécuter les migrations

```bash
php artisan migrate
```

Migrations créées:
- `2026_01_02_210000_create_stripe_accounts_table.php`
- `2026_01_02_210100_create_service_types_table.php`
- `2026_01_02_210200_create_availability_slots_table.php`
- `2026_01_02_210300_create_bookings_table.php`
- `2026_01_02_210400_create_booking_cancellation_policies_table.php`
- `2026_01_02_210500_add_payments_module_to_users_table.php`

### 4. Compiler les assets frontend

```bash
npm run build
# Ou en développement
npm run dev
```

## 🔧 Configuration Stripe

### Webhooks Stripe

1. **Créer un webhook endpoint:**
   - Dashboard Stripe → Developers → Webhooks
   - Click "Add endpoint"
   - URL: `https://votre-domaine.com/webhooks/stripe`
   - Events à sélectionner:
     - `checkout.session.completed`
     - `payment_intent.succeeded`
     - `payment_intent.payment_failed`
     - `account.updated`
     - `charge.refunded`

2. **Récupérer le signing secret:**
   - Copier le "Signing secret" (commence par `whsec_`)
   - Ajouter dans `.env`: `STRIPE_WEBHOOK_SECRET=whsec_xxxxx`

### Mode Test vs Production

**Mode Test (développement):**
```env
STRIPE_PUBLIC_KEY=pk_test_xxxxx
STRIPE_SECRET_KEY=sk_test_xxxxx
```

**Mode Production:**
```env
STRIPE_PUBLIC_KEY=pk_live_xxxxx
STRIPE_SECRET_KEY=sk_live_xxxxx
```

⚠️ **Important:** Ne JAMAIS commiter les clés de production dans git!

## 🎨 Configuration Frontend

Les pages Vue créées:
- `resources/js/Pages/Dashboard/Payments.vue`
- `resources/js/Pages/Dashboard/Services.vue`
- `resources/js/Pages/Dashboard/Bookings.vue`
- `resources/js/Pages/Booking/Index.vue` (à venir)
- `resources/js/Pages/Booking/Create.vue` (à venir)
- `resources/js/Pages/Booking/Success.vue` (à venir)

## 🧪 Tests en mode développement

### 1. Activer le module pour un coach

```bash
# Via Tinker
php artisan tinker

$user = User::where('email', 'coach@example.com')->first();
$user->update([
    'has_payments_module' => true,
    'payments_module_activated_at' => now()
]);
```

### 2. Cartes de test Stripe

**Carte valide:**
- Numéro: `4242 4242 4242 4242`
- Date: n'importe quelle date future
- CVC: n'importe quel 3 chiffres

**Paiement échoué:**
- Numéro: `4000 0000 0000 0002`

**3D Secure requis:**
- Numéro: `4000 0027 6000 3184`

Plus de cartes: https://stripe.com/docs/testing

### 3. Tester le flow complet

1. Activer le module paiements (5€/mois)
2. Connecter un compte Stripe (mode test)
3. Créer un type de service
4. Définir des disponibilités
5. Visiter le site public du coach `/reserver`
6. Réserver une séance avec carte test
7. Vérifier la réservation dans le dashboard

## 📦 Structure des fichiers

### Backend
```
app/
├── Http/Controllers/
│   ├── Dashboard/
│   │   ├── PaymentsController.php
│   │   ├── ServicesController.php
│   │   ├── AvailabilityController.php
│   │   └── BookingsController.php
│   ├── BookingController.php (public)
│   └── StripeWebhookController.php
├── Models/
│   ├── StripeAccount.php
│   ├── ServiceType.php
│   ├── AvailabilitySlot.php
│   ├── Booking.php
│   └── BookingCancellationPolicy.php
├── Services/
│   ├── StripeConnectService.php
│   └── BookingService.php
└── Policies/
    ├── ServiceTypePolicy.php
    ├── AvailabilitySlotPolicy.php
    └── BookingPolicy.php
```

### Routes
- Dashboard coach: `/dashboard/payments`, `/dashboard/services`, `/dashboard/bookings`
- Public: `{coach}.unicoach.app/reserver`
- Webhooks: `/webhooks/stripe`

## 🔒 Sécurité

### Validation des webhooks

Le `StripeWebhookController` vérifie automatiquement la signature des webhooks.

**Ne jamais désactiver cette vérification en production!**

### Protection CSRF

Les routes webhook sont exclues de la vérification CSRF (normal pour les webhooks).

### Policies

Les policies vérifient que:
- Le coach ne peut gérer que ses propres services
- Le coach ne peut voir que ses propres réservations
- Seuls les coachs avec module activé peuvent accéder aux fonctionnalités

## 📊 Monitoring

### Logs Stripe

Tous les événements Stripe sont loggés dans `storage/logs/laravel.log`:
- Création de comptes connectés
- Paiements réussis/échoués
- Mises à jour de comptes
- Erreurs webhook

### Vérifier les logs

```bash
tail -f storage/logs/laravel.log | grep Stripe
```

## 🐛 Dépannage

### Webhook ne fonctionne pas

1. Vérifier que `STRIPE_WEBHOOK_SECRET` est défini
2. Vérifier les logs: `storage/logs/laravel.log`
3. Tester la signature dans Dashboard Stripe → Webhooks → Événements

### Compte Stripe non activé

1. Dashboard coach → Paiements
2. Vérifier le statut du compte
3. Si "Vérification en cours": attendre email Stripe
4. Si erreur: recréer la connexion

### Créneaux non disponibles

1. Vérifier que des disponibilités sont définies
2. Vérifier `min_advance_booking_hours` du service
3. Vérifier qu'il n'y a pas de réservation existante

## 🚀 Déploiement en production

### Checklist avant déploiement

- [ ] Remplacer clés Stripe test par clés production
- [ ] Configurer webhook production (URL HTTPS)
- [ ] Tester le flow complet en prod avec carte test
- [ ] Activer les logs d'erreur
- [ ] Configurer les backups BDD
- [ ] Documenter procédure remboursement

### Variables d'environnement production

```env
APP_ENV=production
APP_DEBUG=false

STRIPE_PUBLIC_KEY=pk_live_xxxxx
STRIPE_SECRET_KEY=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
STRIPE_PLATFORM_COMMISSION_RATE=0.00
```

### Commandes de déploiement

```bash
# Migrer la base de données
php artisan migrate --force

# Compiler les assets
npm run build

# Cacher les routes et config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrer queue workers
php artisan queue:restart
```

## 📚 Ressources

- Documentation Stripe Connect: https://stripe.com/docs/connect
- Dashboard Stripe: https://dashboard.stripe.com
- Stripe Testing: https://stripe.com/docs/testing
- Support: support@stripe.com

## 🎯 Prochaines étapes (Phase 2+)

- [ ] Calendrier avancé avec vue mensuelle
- [ ] Rappels automatiques par email
- [ ] Politique d'annulation configurable
- [ ] Intégration Google Calendar
- [ ] Packs et forfaits
- [ ] Codes promo

---

**Documentation créée le:** 2 janvier 2026  
**Version:** Phase 1 MVP  
**Maintenance:** Mettre à jour lors de chaque déploiement majeur
