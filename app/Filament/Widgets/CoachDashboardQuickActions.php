<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CoachDashboardQuickActions extends Widget
{
    protected static string $view = 'filament.widgets.coach-dashboard-quick-actions';

    protected static ?int $sort = 20;

    protected int|string|array $columnSpan = 'full';
}
