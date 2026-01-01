<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CoachDashboardHero extends Widget
{
    protected string $view = 'filament.widgets.coach-dashboard-hero';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';
}
