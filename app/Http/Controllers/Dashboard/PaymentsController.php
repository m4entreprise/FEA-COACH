<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\StripeConnectService;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentsController extends Controller
{
    public function __construct(
        protected StripeConnectService $stripeService,
        protected BookingService $bookingService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $coach = $user->coach;

        if (!$coach) {
            return redirect()->route('dashboard');
        }

        // Rafraîchir les données depuis la base
        $user->refresh();
        
        $stripeAccount = $coach->stripeAccount;
        $stats = $stripeAccount ? $this->bookingService->getBookingStats($coach) : null;

        // Debug: logger les valeurs
        \Log::info('PaymentsController index', [
            'user_id' => $user->id,
            'has_payments_module' => $user->has_payments_module,
            'has_payments_module_type' => gettype($user->has_payments_module),
            'payments_module_activated_at' => $user->payments_module_activated_at,
        ]);

        return Inertia::render('Dashboard/Payments', [
            'hasPaymentsModule' => (bool) $user->has_payments_module,
            'paymentsModulePrice' => config('stripe.payments_module_price'),
            'stripeAccount' => $stripeAccount ? [
                'connected' => true,
                'onboarding_completed' => $stripeAccount->onboarding_completed,
                'charges_enabled' => $stripeAccount->charges_enabled,
                'payouts_enabled' => $stripeAccount->payouts_enabled,
                'details_submitted' => $stripeAccount->details_submitted,
                'is_fully_activated' => $stripeAccount->isFullyActivated(),
            ] : ['connected' => false],
            'stats' => $stats,
        ]);
    }

    public function activateModule(Request $request)
    {
        $user = $request->user();

        if ($user->has_payments_module) {
            return back()->with('error', 'Module déjà activé');
        }

        $user->update([
            'has_payments_module' => true,
            'payments_module_activated_at' => now(),
        ]);

        return back()->with('success', 'Module paiements activé avec succès');
    }

    public function connectStripe(Request $request)
    {
        $user = $request->user();
        $coach = $user->coach;

        if (!$user->has_payments_module) {
            \Log::warning('Tentative de connexion Stripe sans module activé', ['user_id' => $user->id]);
            return back()->with('error', 'Vous devez d\'abord activer le module paiements');
        }

        try {
            \Log::info('Début connexion Stripe', [
                'user_id' => $user->id,
                'coach_id' => $coach->id,
                'stripe_key_configured' => !empty(config('stripe.secret_key')),
            ]);

            $accountLink = $this->stripeService->getOrCreateAccountLink($coach);
            
            \Log::info('Lien Stripe généré avec succès', [
                'coach_id' => $coach->id,
                'url_length' => strlen($accountLink),
            ]);

            return redirect($accountLink);
        } catch (\Exception $e) {
            \Log::error('Erreur connexion Stripe', [
                'user_id' => $user->id,
                'coach_id' => $coach->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', 'Erreur lors de la connexion à Stripe: ' . $e->getMessage());
        }
    }

    public function stripeReturn(Request $request)
    {
        $coach = $request->user()->coach;
        $stripeAccount = $coach->stripeAccount;

        if ($stripeAccount) {
            try {
                $this->stripeService->updateStripeAccountStatus($stripeAccount);
            } catch (\Exception $e) {
                return redirect()->route('dashboard.payments.index')
                    ->with('error', 'Erreur lors de la vérification du compte Stripe');
            }
        }

        return redirect()->route('dashboard.payments.index')
            ->with('success', 'Compte Stripe connecté avec succès');
    }

    public function stripeRefresh(Request $request)
    {
        $coach = $request->user()->coach;

        try {
            $accountLink = $this->stripeService->getOrCreateAccountLink($coach);
            return redirect($accountLink);
        } catch (\Exception $e) {
            return redirect()->route('dashboard.payments.index')
                ->with('error', 'Erreur lors de la reconnexion à Stripe');
        }
    }

    public function disconnect(Request $request)
    {
        $coach = $request->user()->coach;
        $stripeAccount = $coach->stripeAccount;

        if ($stripeAccount) {
            $stripeAccount->delete();
        }

        return back()->with('success', 'Compte Stripe déconnecté');
    }

    public function dashboard(Request $request)
    {
        $coach = $request->user()->coach;
        $stripeAccount = $coach->stripeAccount;

        if (!$stripeAccount || !$stripeAccount->isFullyActivated()) {
            return back()->with('error', 'Compte Stripe non activé');
        }

        try {
            $dashboardUrl = $this->stripeService->getDashboardLink($stripeAccount->stripe_account_id);
            return redirect($dashboardUrl);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'accès au dashboard Stripe');
        }
    }
}
