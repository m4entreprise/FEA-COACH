<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the coach dashboard with stats and data.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Admins see a different view or basic stats
        if ($user->role === 'admin') {
            return Inertia::render('Dashboard', [
                'isAdmin' => true,
                'hasCompletedOnboarding' => (bool) $user->has_completed_onboarding,
            ]);
        }

        // Load coach data with relationships
        $coach = $user->coach()->with([
            'plans',
            'transformations',
            'faqs',
            'user',
        ])->first();

        if (!$coach) {
            return Inertia::render('Dashboard', [
                'error' => 'Aucun profil coach associé à votre compte.',
                'hasCompletedOnboarding' => (bool) $user->has_completed_onboarding,
            ]);
        }

        // Calculate stats
        $profileData = $this->calculateProfileCompletion($coach);
        
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
        ];
        
        $stats = [
            'total_plans' => $coach->plans()->count(),
            'active_plans' => $coach->plans()->where('is_active', true)->count(),
            'total_transformations' => $coach->transformations()->count(),
            'is_active' => $coach->is_active,
            'profile_completion' => $profileData['percentage'],
            'profile_missing_fields' => $profileData['missing_fields'],
            'subscription' => $subscriptionInfo,
        ];

        // Get recent transformations (for quick view)
        $recentTransformations = $coach->transformations()
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'description' => $t->description,
                'before_url' => $t->hasMedia('before') ? $t->getFirstMediaUrl('before') : null,
                'after_url' => $t->hasMedia('after') ? $t->getFirstMediaUrl('after') : null,
            ]);

        return Inertia::render('Dashboard', [
            'coach' => [
                'id' => $coach->id,
                'name' => $coach->name,
                'slug' => $coach->slug,
                'subdomain' => $coach->subdomain,
                'is_active' => $coach->is_active,
                'color_primary' => $coach->color_primary,
                'color_secondary' => $coach->color_secondary,
                'has_logo' => $coach->hasMedia('logo'),
                'has_hero' => $coach->hasMedia('hero'),
            ],
            'stats' => $stats,
            'recentTransformations' => $recentTransformations,
            'hasCompletedOnboarding' => (bool) $user->has_completed_onboarding,
        ]);
    }

    /**
     * Mark the onboarding as completed for the current user.
     */
    public function completeOnboarding(Request $request)
    {
        $user = $request->user();
        $user->has_completed_onboarding = true;
        $user->save();

        return redirect()->back();
    }

    /**
     * Calculate profile completion percentage and missing fields.
     */
    private function calculateProfileCompletion($coach): array
    {
        $fieldLabels = [
            'name' => 'Nom du coach',
            'slug' => 'Sous-domaine',
            'color_primary' => 'Couleur principale',
            'color_secondary' => 'Couleur secondaire',
            'hero_title' => 'Titre principal',
            'hero_subtitle' => 'Sous-titre principal',
            'about_text' => 'Texte "À propos"',
            'method_text' => 'Texte "Ma méthode"',
            'logo' => 'Logo',
            'hero' => 'Image hero',
            'plans' => 'Au moins 1 plan tarifaire',
            'transformations' => 'Au moins 1 transformation',
            'faqs' => 'Au moins 1 FAQ',
            'vat_number' => 'Numéro de TVA',
            'legal_terms' => 'Mentions légales',
        ];

        $fieldRoutes = [
            'name' => 'dashboard.branding',
            'slug' => 'dashboard.branding',
            'color_primary' => 'dashboard.branding',
            'color_secondary' => 'dashboard.branding',
            'hero_title' => 'dashboard.content',
            'hero_subtitle' => 'dashboard.content',
            'about_text' => 'dashboard.content',
            'method_text' => 'dashboard.content',
            'logo' => 'dashboard.branding',
            'hero' => 'dashboard.branding',
            'plans' => 'dashboard.plans',
            'transformations' => 'dashboard.gallery',
            'faqs' => 'dashboard.content',
            'vat_number' => 'dashboard.legal',
            'legal_terms' => 'dashboard.legal',
        ];

        $fields = [
            'name' => !empty($coach->name),
            'slug' => !empty($coach->slug),
            'color_primary' => !empty($coach->color_primary),
            'color_secondary' => !empty($coach->color_secondary),
            'hero_title' => !empty($coach->hero_title),
            'hero_subtitle' => !empty($coach->hero_subtitle),
            'about_text' => !empty($coach->about_text),
            'method_text' => !empty($coach->method_text),
            'logo' => $coach->hasMedia('logo'),
            'hero' => $coach->hasMedia('hero'),
            'plans' => $coach->plans()->count() > 0,
            'transformations' => $coach->transformations()->count() > 0,
            'faqs' => $coach->faqs()->count() > 0,
            'vat_number' => !empty($coach->user->vat_number),
            'legal_terms' => !empty($coach->legal_terms),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);
        $percentage = round(($completed / $total) * 100);

        $missingFields = [];
        foreach ($fields as $field => $isCompleted) {
            if (!$isCompleted) {
                $missingFields[] = [
                    'field' => $field,
                    'label' => $fieldLabels[$field],
                    'route' => $fieldRoutes[$field],
                ];
            }
        }

        return [
            'percentage' => $percentage,
            'missing_fields' => $missingFields,
        ];
    }
}
