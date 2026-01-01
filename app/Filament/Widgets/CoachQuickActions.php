<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CoachQuickActions extends Widget
{
    protected static string $view = 'filament.widgets.coach-quick-actions';

    protected static ?int $sort = 20;

    protected int|string|array $columnSpan = 'full';
}
