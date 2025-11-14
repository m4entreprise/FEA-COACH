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
            'faqs' => function ($query) {
                $query->where('is_active', true)->orderBy('order')->orderBy('created_at');
            },
        ]);

        // Get the data as arrays for better debugging
        $activePlans = $coach->plans;
        $transformations = $coach->transformations;
        $faqs = $coach->faqs;

        return view('coach-site.index', [
            'coach' => $coach,
            'plans' => $activePlans,
            'transformations' => $transformations,
            'faqs' => $faqs,
        ]);
    }

    /**
     * Handle contact form submissions from the public site.
     */
    public function contact(Request $request)
    {
        $coach = app(Coach::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $coach->contactMessages()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
    }
}
