<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\FungiesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    protected FungiesService $fungiesService;

    public function __construct(FungiesService $fungiesService)
    {
        $this->fungiesService = $fungiesService;
    }

    /**
     * Display the subscription management page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Calculate subscription info
        // Détecte la période d'essai : statut trial, null, ou active_promo (pour les comptes FEA)
        $isOnTrial = ($user->subscription_status === 'trial' 
                      || $user->subscription_status === null 
                      || $user->subscription_status === 'active_promo') 
                     && $user->trial_ends_at 
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
            'fungies_customer_id' => $user->fungies_customer_id,
            'fungies_subscription_id' => $user->fungies_subscription_id,
            'current_period_end' => $user->subscription_current_period_end,
            'cancel_at_period_end' => $user->cancel_at_period_end,
        ];

        return Inertia::render('Dashboard/Subscription', [
            'subscription' => $subscriptionInfo,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Create a Fungies checkout session for subscription.
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            $user = $request->user();

            // Check if user already has an active subscription
            if ($user->subscription_status === 'active' && $user->fungies_subscription_id) {
                return back()->with('info', 'Vous avez déjà un abonnement actif.');
            }

            // Create checkout session via Fungies
            $checkoutSession = $this->fungiesService->createCheckoutSession([
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name ?? $user->first_name . ' ' . $user->last_name,
            ], [
                'coach_id' => $user->coach_id,
                'is_fea_graduate' => $user->is_fea_graduate,
            ]);

            // Redirect to Fungies checkout page
            if (isset($checkoutSession['checkoutUrl'])) {
                return redirect($checkoutSession['checkoutUrl']);
            }

            // If no checkout URL, redirect to a payment page
            if (isset($checkoutSession['url'])) {
                return redirect($checkoutSession['url']);
            }

            Log::error('Fungies checkout session missing URL', ['response' => $checkoutSession]);
            return back()->with('error', 'Erreur lors de la création de la session de paiement.');

        } catch (\Exception $e) {
            Log::error('Failed to create Fungies checkout session', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Access the Fungies customer portal.
     */
    public function customerPortal(Request $request)
    {
        try {
            $user = $request->user();

            // Check if user has a customer ID
            if (!$user->fungies_customer_id) {
                return back()->with('error', 'Aucun compte client trouvé.');
            }

            // Get customer portal URL
            $portalUrl = $this->fungiesService->getCustomerPortalUrl($user->fungies_customer_id);

            if ($portalUrl) {
                return redirect($portalUrl);
            }

            return back()->with('error', 'Impossible d\'accéder au portail client.');

        } catch (\Exception $e) {
            Log::error('Failed to access customer portal', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request)
    {
        try {
            $user = $request->user();

            // Check if user has an active subscription
            if (!$user->fungies_subscription_id) {
                return back()->with('error', 'Aucun abonnement actif trouvé.');
            }

            // Cancel subscription at end of period
            $cancelled = $this->fungiesService->cancelSubscription(
                $user->fungies_subscription_id,
                'endPeriod', // Cancel at end of billing period
                'noRefund'   // No refund
            );

            if ($cancelled) {
                $user->update([
                    'cancel_at_period_end' => true,
                ]);

                return back()->with('success', 'Votre abonnement sera annulé à la fin de la période de facturation.');
            }

            return back()->with('error', 'Erreur lors de l\'annulation de l\'abonnement.');

        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }
}
