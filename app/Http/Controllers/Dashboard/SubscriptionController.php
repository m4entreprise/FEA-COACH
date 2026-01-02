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

        // Get custom domain if exists (confirmation après paiement / traitement admin)
        $coach = $user->coach;
        $customDomain = null;
        
        if ($coach) {
            $domain = $coach->customDomain;
            if ($domain) {
                $customDomain = [
                    'domain' => $domain->domain,
                    'status' => $domain->status,
                    'purchased_at' => $domain->purchased_at,
                    'expires_at' => $domain->expires_at,
                ];
            }
        }

        return Inertia::render('Coach/SubscriptionBeta', [
            'subscription' => $subscriptionInfo,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'planInfo' => $planInfo,
            'customDomain' => $customDomain,
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
        try {
            $user = $request->user();
            
            // Check if user has a subscription
            if (!$user->lemonsqueezy_subscription_id) {
                return back()->with('error', 'Aucun abonnement trouvé.');
            }

            // Get customer portal URL from Lemon Squeezy API
            $portalUrl = $this->lemonSqueezyService->getCustomerPortalUrl(
                (string) $user->lemonsqueezy_subscription_id
            );

            // If API call fails, use fallback URL
            if (!$portalUrl) {
                $portalUrl = $this->lemonSqueezyService->getFallbackPortalUrl();
                Log::warning('Using fallback portal URL', [
                    'user_id' => $user->id,
                ]);
            }

            // Return URL in JSON for frontend to handle redirect (avoid CORS with Inertia)
            return response()->json([
                'redirect_url' => $portalUrl,
            ]);

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
        return back()->with('info', 'Annulation : gérée via Lemon Squeezy (MVP).');
    }

    /**
     * Create a Lemon Squeezy checkout session for custom domain.
     */
    public function checkoutCustomDomain(Request $request)
    {
        try {
            $user = $request->user();
            $coach = $user->coach;

            if (!$coach) {
                return back()->with('error', 'Aucun profil coach associé.');
            }

            $validated = $request->validate([
                'desired_domain' => ['nullable', 'string', 'max:255'],
            ]);

            $desiredDomain = $validated['desired_domain'] ?? null;

            if ($desiredDomain) {
                $coach->desired_custom_domain = $desiredDomain;
                $coach->save();
            }

            // Get the custom domain variant ID from config
            $variantId = (int) config('lemonsqueezy.variant_custom_domain');
            
            if (!$variantId) {
                Log::error('Custom domain variant ID not configured');
                return back()->with('error', 'Le produit nom de domaine n\'est pas configuré.');
            }

            $checkoutSession = $this->lemonSqueezyService->createCheckoutSession([
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name,
                'vat_number' => $user->vat_number,
            ], [
                'coach_id' => $coach->id,
                'coach_slug' => $coach->slug,
                'product_type' => 'custom_domain',
                'source' => 'dashboard_premium',
                'desired_domain' => $desiredDomain ?? '',
            ], $variantId);

            $checkoutUrl = $checkoutSession['data']['attributes']['url'] ?? null;
            if ($checkoutUrl) {
                return redirect($checkoutUrl);
            }

            Log::error('Lemon Squeezy checkout session missing URL (custom domain)', [
                'user_id' => $user->id,
                'response' => $checkoutSession,
            ]);

            return back()->with('error', 'Erreur lors de la création de la session de paiement.');

        } catch (\Exception $e) {
            Log::error('Failed to create Lemon Squeezy checkout session (custom domain)', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }
}
