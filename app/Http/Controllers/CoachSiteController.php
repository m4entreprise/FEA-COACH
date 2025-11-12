<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoachSiteController extends Controller
{
    /**
     * Display the coach's public site.
     */
    public function show(Request $request): View
    {
        // Get the coach from the container (set by ResolveCoachFromHost middleware)
        $coach = app(Coach::class);

        // Load relationships with explicit eager loading
        $coach->loadMissing([
            'transformations' => function ($query) {
                $query->orderBy('order');
            },
            'plans' => function ($query) {
                $query->where('is_active', true)->orderBy('price');
            },
        ]);

        // Get the data as arrays for better debugging
        $activePlans = $coach->plans;
        $transformations = $coach->transformations;

        return view('coach-site.index', [
            'coach' => $coach,
            'plans' => $activePlans,
            'transformations' => $transformations,
        ]);
    }
}
