# Guide d'Implémentation : Stripe Connect pour les Coachs

Ce document détaille les étapes techniques pour permettre aux coachs de connecter leur propre compte Stripe et recevoir des paiements directement (Direct Charges).

## 1. Architecture & Concept

Nous utilisons **Stripe Connect** avec des comptes **Express**.
*   **Mode** : Direct Charges.
*   **Flux d'argent** : Client -> Stripe -> Compte du Coach.
*   **Rôle de la plateforme (FEA-COACH)** : Initiateur de la connexion et créateur de l'interface de paiement. La plateforme ne touche pas les fonds.

## 2. Pré-requis

### Installation de la librairie
```bash
composer require stripe/stripe-php
```

### Configuration (.env)
Ajoutez vos clés Stripe (récupérées sur le Dashboard Stripe > Developers > API keys & Connect > Settings).

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_CLIENT_ID=ca_...  # Client ID pour Connect (Mode Test)
STRIPE_WEBHOOK_SECRET=whsec_...
```

## 3. Base de Données

Il faut ajouter deux colonnes à la table `coaches` pour stocker l'identifiant du compte connecté.

**Migration :** `database/migrations/xxxx_xx_xx_add_stripe_to_coaches_table.php`

```php
Schema::table('coaches', function (Blueprint $table) {
    $table->string('stripe_account_id')->nullable()->after('user_id');
    $table->boolean('stripe_onboarding_completed')->default(false)->after('stripe_account_id');
});
```

**Modèle :** `app/Models/Coach.php`

```php
protected $fillable = [
    // ...
    'stripe_account_id',
    'stripe_onboarding_completed',
];
```

## 4. Backend (Laravel)

### Routeur (`routes/web.php`)

```php
use App\Http\Controllers\StripeConnectController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('stripe')->name('stripe.')->group(function () {
        Route::get('/connect', [StripeConnectController::class, 'connect'])->name('connect');
        Route::get('/callback', [StripeConnectController::class, 'callback'])->name('callback');
        Route::get('/dashboard', [StripeConnectController::class, 'dashboard'])->name('dashboard');
    });
});
```

### Contrôleur (`app/Http/Controllers/StripeConnectController.php`)

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;

class StripeConnectController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret')); // Assurez-vous que config/services.php est configuré
    }

    // 1. Initier la connexion (Création du compte Express)
    public function connect()
    {
        $user = Auth::user();
        $coach = $user->coach;

        if (!$coach->stripe_account_id) {
            // Créer un compte Express
            $account = Account::create([
                'type' => 'express',
                'country' => 'FR', // Ou dynamique selon le user
                'email' => $user->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);

            $coach->update(['stripe_account_id' => $account->id]);
        }

        // Créer le lien d'onboarding
        $accountLink = AccountLink::create([
            'account' => $coach->stripe_account_id,
            'refresh_url' => route('stripe.connect'), // En cas d'échec/expiration
            'return_url' => route('stripe.callback'), // En cas de succès
            'type' => 'account_onboarding',
        ]);

        return redirect($accountLink->url);
    }

    // 2. Retour de Stripe (Callback)
    public function callback()
    {
        $user = Auth::user();
        $coach = $user->coach;

        // Vérifier le statut du compte chez Stripe
        $account = Account::retrieve($coach->stripe_account_id);

        if ($account->details_submitted) {
            $coach->update(['stripe_onboarding_completed' => true]);
            return redirect()->route('dashboard.settings')->with('success', 'Compte Stripe connecté avec succès !');
        }

        return redirect()->route('dashboard.settings')->with('error', 'La configuration Stripe n\'a pas été terminée.');
    }

    // 3. Accès au Dashboard Express (pour voir les paiements/virements)
    public function dashboard()
    {
        $user = Auth::user();
        $coach = $user->coach;

        if (!$coach->stripe_account_id) {
            return back()->with('error', 'Aucun compte Stripe connecté.');
        }

        $loginLink = \Stripe\Account::createLoginLink($coach->stripe_account_id);

        return redirect($loginLink->url);
    }
}
```

## 5. Frontend (Vue.js)

Dans votre page de réglages (`Settings.vue`), ajoutez une section pour gérer la connexion.

```vue
<template>
  <div class="p-6 bg-white shadow rounded-lg">
    <h3 class="text-lg font-medium text-gray-900">Paiements</h3>
    
    <div v-if="coach.stripe_onboarding_completed" class="mt-4">
      <div class="flex items-center text-green-600 mb-4">
        <span class="mr-2">✅</span> Compte Stripe connecté
      </div>
      <a :href="route('stripe.dashboard')" class="btn-secondary">
        Voir mes paiements sur Stripe
      </a>
    </div>

    <div v-else class="mt-4">
      <p class="text-gray-600 mb-4">Connectez votre compte Stripe pour recevoir les paiements de vos clients.</p>
      <a :href="route('stripe.connect')" class="btn-primary">
        Connecter Stripe
      </a>
    </div>
  </div>
</template>
```

## 6. Gestion des Clients (Portail Client)

Pour permettre aux clients de gérer leur abonnement (changer de carte, annuler), utilisez le **Stripe Customer Portal**.

Lorsqu'un client s'abonne, vous stockez son `stripe_customer_id` (c'est déjà prévu dans votre table `users`).

Pour générer un lien vers le portail :
```php
$session = \Stripe\BillingPortal\Session::create([
  'customer' => $user->stripe_customer_id,
  'return_url' => route('dashboard'),
], ['stripe_account' => $coach->stripe_account_id]); // IMPORTANT: Préciser le compte connecté

return redirect($session->url);
```
*Note : Pour les Direct Charges, le client appartient au compte connecté du coach, pas à la plateforme.*

## 7. Configuration `config/services.php`

Assurez-vous que Laravel sait où chercher les clés.

```php
'stripe' => [
    'model' => App\Models\User::class,
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => 300,
    ],
    'client_id' => env('STRIPE_CLIENT_ID'),
],
```
