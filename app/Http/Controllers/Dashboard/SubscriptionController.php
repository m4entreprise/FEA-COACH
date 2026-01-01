<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\LemonSqueezyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    protected LemonSqueezyService $lemonSqueezyService;

    public function __construct(LemonSqueezyService $lemonSqueezyService)
    {
        $this->lemonSqueezyService = $lemonSqueezyService;
    }

    /**
     * Display the subscription management page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Calculate subscription info
        // Détecte la période d'essai : si trial_ends_at existe et est dans le futur
        // Note: Lemon Squeezy envoie status="active" même pendant le trial
        $isOnTrial = $user->trial_ends_at 
                     && now()->isBefore($user->trial_ends_at);
        
        $trialDaysLeft = null;
        if ($user->trial_ends_at) {
            $secondsLeft = now()->diffInSeconds($user->trial_ends_at, false);
            $trialDaysLeft = $secondsLeft > 0
                ? (int) ceil($secondsLeft / 86400)
                : 0;
        }

        $subscriptionInfo = [
            'status' => $user->subscription_status ?? 'trial',
            'trial_ends_at' => $user->trial_ends_at,
            'is_on_trial' => $isOnTrial,
            'trial_days_left' => $trialDaysLeft,
            'lemonsqueezy_customer_id' => $user->lemonsqueezy_customer_id,
            'lemonsqueezy_subscription_id' => $user->lemonsqueezy_subscription_id,
            'current_period_end' => $user->subscription_current_period_end,
            'cancel_at_period_end' => $user->cancel_at_period_end ?? false,
        ];

        // Determine plan info based on FEA graduate status
        $isFea = (bool) $user->is_fea_graduate;
        $planInfo = [
            'name' => 'UNICOACH Pro',
            'price' => $isFea ? '20' : '30',
            'original_price' => $isFea ? '30' : null,
            'interval' => 'HTVA / mois',
            'is_fea_price' => $isFea,
            'description' => $isFea 
                ? 'Bénéficiez d\'une réduction permanente grâce au partenariat avec Fitness Education Academy.'
                : 'Accédez à toutes les fonctionnalités pour développer votre activité de coaching.',
        ];

        return Inertia::render('Coach/SubscriptionBeta', [
            'subscription' => $subscriptionInfo,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'planInfo' => $planInfo,
        ]);
    }

    /**
     * Create a Lemon Squeezy checkout session for subscription.
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            $user = $request->user();

            // Check if user already has an active subscription
            if ($user->subscription_status === 'active' && $user->lemonsqueezy_subscription_id) {
                return back()->with('info', 'Vous avez déjà un abonnement actif.');
            }

            $checkoutSession = $this->lemonSqueezyService->createCheckoutSession([
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name,
                'vat_number' => $user->vat_number,
            ], [
                'is_fea_graduate' => (bool) $user->is_fea_graduate,
                'source' => 'dashboard_subscription',
            ]);

            $checkoutUrl = $checkoutSession['data']['attributes']['url'] ?? null;
            if ($checkoutUrl) {
                return redirect($checkoutUrl);
            }

            Log::error('Lemon Squeezy checkout session missing URL (dashboard)', [
                'user_id' => $user->id,
                'response' => $checkoutSession,
            ]);

            return back()->with('error', 'Erreur lors de la création de la session de paiement.');

        } catch (\Exception $e) {
            Log::error('Failed to create Lemon Squeezy checkout session (dashboard)', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Access the customer portal.
     */
    public function customerPortal(Request $request)
    {
        return back()->with('info', 'Portail client : à connecter à Lemon Squeezy (MVP).');
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request)
    {
        return back()->with('info', 'Annulation : gérée via Lemon Squeezy (MVP).');
    }
}
