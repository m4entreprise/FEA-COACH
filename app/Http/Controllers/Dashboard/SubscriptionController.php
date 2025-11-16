<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    /**
     * Display the subscription management page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Calculate subscription info
        $subscriptionInfo = [
            'status' => $user->subscription_status ?? 'trial',
            'trial_ends_at' => $user->trial_ends_at,
            'is_on_trial' => $user->subscription_status === 'trial' || $user->subscription_status === null,
            'trial_days_left' => $user->trial_ends_at ? max(0, now()->diffInDays($user->trial_ends_at, false)) : null,
            'stripe_customer_id' => $user->stripe_customer_id,
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
     * Create a Stripe checkout session for subscription.
     */
    public function createCheckoutSession(Request $request)
    {
        // TODO: Implement Stripe checkout session creation
        // This will be implemented when you integrate Stripe
        
        return back()->with('info', 'Fonctionnalité de paiement en cours de développement.');
    }

    /**
     * Access the Stripe customer portal.
     */
    public function customerPortal(Request $request)
    {
        // TODO: Implement Stripe customer portal redirect
        // This will be implemented when you integrate Stripe
        
        return back()->with('info', 'Fonctionnalité de gestion en cours de développement.');
    }
}
