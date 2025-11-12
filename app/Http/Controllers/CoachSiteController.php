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

        // Load relationships
        $coach->load([
            'transformations' => function ($query) {
                $query->orderBy('order');
            },
            'plans' => function ($query) {
                $query->where('is_active', true);
            },
        ]);

        return view('coach-site.index', [
            'coach' => $coach,
        ]);
    }
}
