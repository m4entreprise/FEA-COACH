<?php

namespace App\Filament\Widgets;

use App\Models\Coach;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CoachDashboardStats extends BaseWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        if ($user->role === 'admin') {
            return [
                Stat::make('Role', 'Admin')
                    ->description('Utilisez le panel admin dedie pour la gestion.')
                    ->color('info'),
            ];
        }

        $coach = $user->coach()
            ->with(['plans', 'transformations', 'faqs', 'user'])
            ->first();

        if (! $coach) {
            return [
                Stat::make('Profil coach', 'Non configure')
                    ->description('Aucun profil coach assigne a votre compte.')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        $profileData = $this->calculateProfileCompletion($coach);
        $subscriptionInfo = $this->getSubscriptionInfo($user);

        $totalPlans = $coach->plans->count();
        $activePlans = $coach->plans->where('is_active', true)->count();
        $totalTransformations = $coach->transformations->count();

        $missingCount = count($profileData['missing_fields']);

        $subscriptionLabel = $subscriptionInfo['status'] === 'trial'
            ? 'Essai'
            : ucfirst((string) $subscriptionInfo['status']);

        $trialDescription = null;

        if ($subscriptionInfo['is_on_trial'] && $subscriptionInfo['trial_days_left'] !== null) {
            $days = $subscriptionInfo['trial_days_left'];
            $trialDescription = $days > 0
                ? 'Essai restant : ' . $days . ' jour' . ($days > 1 ? 's' : '')
                : 'Essai termine recemment';
        }

        $subscriptionColor = match ($subscriptionInfo['status']) {
            'active', 'active_promo' => 'success',
            'canceled' => 'danger',
            default => $subscriptionInfo['is_on_trial'] ? 'warning' : 'gray',
        };

        return [
            Stat::make('Profil', $profileData['percentage'] . '%')
                ->description(
                    $missingCount === 0
                        ? 'Profil complet'
                        : $missingCount . ' element' . ($missingCount > 1 ? 's' : '') . ' manquant' . ($missingCount > 1 ? 's' : '')
                )
                ->descriptionIcon($missingCount === 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-circle')
                ->color($profileData['percentage'] >= 80 ? 'success' : 'warning'),

            Stat::make('Plans', $activePlans . '/' . $totalPlans)
                ->description('Plans actifs / total')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color($activePlans > 0 ? 'success' : 'warning'),

            Stat::make('Transformations', (string) $totalTransformations)
                ->description('Avant / apres publies')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($totalTransformations > 0 ? 'success' : 'warning'),

            Stat::make('Abonnement', $subscriptionLabel)
                ->description($trialDescription)
                ->descriptionIcon('heroicon-m-bolt')
                ->color($subscriptionColor),

            Stat::make('Site', $coach->is_active ? 'Actif' : 'Inactif')
                ->description($coach->is_active ? 'Visible publiquement' : 'En construction')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color($coach->is_active ? 'success' : 'danger')
                ->url(route('coach.site', ['coach_slug' => $coach->slug ?: $coach->subdomain])),
        ];
    }

    private function calculateProfileCompletion(Coach $coach): array
    {
        $fieldLabels = [
            'name' => 'Nom du coach',
            'slug' => 'Sous-domaine',
            'color_primary' => 'Couleur principale',
            'color_secondary' => 'Couleur secondaire',
            'hero_title' => 'Titre principal',
            'hero_subtitle' => 'Sous-titre principal',
            'about_text' => 'Texte A propos',
            'method_text' => 'Texte Ma methode',
            'logo' => 'Logo',
            'hero' => 'Image hero',
            'plans' => 'Au moins 1 plan tarifaire',
            'transformations' => 'Au moins 1 transformation',
            'faqs' => 'Au moins 1 FAQ',
            'vat_number' => 'Numero de TVA',
            'legal_terms' => 'Mentions legales',
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
            'name' => ! empty($coach->name),
            'slug' => ! empty($coach->slug),
            'color_primary' => ! empty($coach->color_primary),
            'color_secondary' => ! empty($coach->color_secondary),
            'hero_title' => ! empty($coach->hero_title),
            'hero_subtitle' => ! empty($coach->hero_subtitle),
            'about_text' => ! empty($coach->about_text),
            'method_text' => ! empty($coach->method_text),
            'logo' => $coach->hasMedia('logo'),
            'hero' => $coach->hasMedia('hero'),
            'plans' => $coach->plans()->count() > 0,
            'transformations' => $coach->transformations()->count() > 0,
            'faqs' => $coach->faqs()->count() > 0,
            'vat_number' => ! empty(optional($coach->user)->vat_number),
            'legal_terms' => ! empty($coach->legal_terms),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);
        $percentage = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        $missingFields = [];

        foreach ($fields as $field => $isCompleted) {
            if (! $isCompleted) {
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

    private function getSubscriptionInfo($user): array
    {
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

        return [
            'status' => $user->subscription_status ?? 'trial',
            'trial_ends_at' => $user->trial_ends_at,
            'is_on_trial' => $isOnTrial,
            'trial_days_left' => $trialDaysLeft,
        ];
    }
}
