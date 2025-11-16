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
            ]);
        }

        // Load coach data with relationships
        $coach = $user->coach()->with([
            'plans',
            'transformations',
        ])->first();

        if (!$coach) {
            return Inertia::render('Dashboard', [
                'error' => 'Aucun profil coach associé à votre compte.',
            ]);
        }

        // Calculate stats
        $profileData = $this->calculateProfileCompletion($coach);
        $stats = [
            'total_plans' => $coach->plans()->count(),
            'active_plans' => $coach->plans()->where('is_active', true)->count(),
            'total_transformations' => $coach->transformations()->count(),
            'is_active' => $coach->is_active,
            'profile_completion' => $profileData['percentage'],
            'profile_missing_fields' => $profileData['missing_fields'],
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
        ]);
    }

    /**
     * Calculate profile completion percentage and missing fields.
     */
    private function calculateProfileCompletion($coach): array
    {
        $fieldLabels = [
            'name' => 'Nom du coach',
            'subdomain' => 'Sous-domaine',
            'color_primary' => 'Couleur principale',
            'color_secondary' => 'Couleur secondaire',
            'hero_title' => 'Titre principal',
            'hero_subtitle' => 'Sous-titre principal',
            'about_text' => 'Texte "À propos"',
            'method_text' => 'Texte "Ma méthode"',
            'logo' => 'Logo',
            'hero' => 'Image hero',
        ];

        $fieldRoutes = [
            'name' => 'dashboard.branding',
            'subdomain' => 'dashboard.branding',
            'color_primary' => 'dashboard.branding',
            'color_secondary' => 'dashboard.branding',
            'hero_title' => 'dashboard.content',
            'hero_subtitle' => 'dashboard.content',
            'about_text' => 'dashboard.content',
            'method_text' => 'dashboard.content',
            'logo' => 'dashboard.branding',
            'hero' => 'dashboard.branding',
        ];

        $fields = [
            'name' => !empty($coach->name),
            'subdomain' => !empty($coach->subdomain),
            'color_primary' => !empty($coach->color_primary),
            'color_secondary' => !empty($coach->color_secondary),
            'hero_title' => !empty($coach->hero_title),
            'hero_subtitle' => !empty($coach->hero_subtitle),
            'about_text' => !empty($coach->about_text),
            'method_text' => !empty($coach->method_text),
            'logo' => $coach->hasMedia('logo'),
            'hero' => $coach->hasMedia('hero'),
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
