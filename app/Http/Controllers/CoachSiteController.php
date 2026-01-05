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
            'user',
            'transformations' => function ($query) {
                $query->orderBy('order');
            },
            'serviceTypes' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->orderBy('price');
            },
            'faqs' => function ($query) {
                $query->where('is_active', true)->orderBy('order')->orderBy('created_at');
            },
        ]);

        // Get the data as arrays for better debugging
        $services = $coach->serviceTypes;
        $transformations = $coach->transformations;
        $faqs = $coach->faqs;

        // Determine which layout to use based on coach's site_layout setting
        $config = config('coach_site');
        $layouts = $config['layouts'] ?? [];
        $defaultKey = $config['default_layout'] ?? 'classic';

        // Get layout key from coach with fallback to default
        $layoutKey = method_exists($coach, 'getSiteLayoutOrDefaultAttribute')
            ? $coach->site_layout_or_default
            : ($coach->site_layout ?: $defaultKey);

        // Fallback if layout key is not found in config
        if (! isset($layouts[$layoutKey])) {
            $layoutKey = $defaultKey;
        }

        // Get the view name from config, with final fallback
        $viewName = $layouts[$layoutKey]['view'] ?? 'coach-site.layouts.classic';

        return view($viewName, [
            'coach' => $coach,
            'services' => $services,
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
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $coach->contactMessages()->create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'message' => $validated['message'],
        ]);

        // Réponse JSON pour les requêtes AJAX (Alpine.js)
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Votre message a bien été envoyé. Nous vous répondrons rapidement.',
            ]);
        }

        // Fallback classique en POST (sans JS)
        return back()->with('success', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
    }

    /**
     * Display the coach's legal terms and CGV.
     */
    public function legal(Request $request): View
    {
        $coach = app(Coach::class);
        $coach->load('user');

        // Generate HTML if not cached or if in AUTO mode
        $legalHtml = $coach->legal_terms;
        
        if (empty($legalHtml) || $coach->legal_generation_mode === 'AUTO') {
            $generator = app(\App\Services\LegalContentGenerator::class);
            $legalHtml = $generator->generate($coach);
        }

        return view('coach-site.legal', [
            'coach' => $coach,
            'legalHtml' => $legalHtml,
        ]);
    }
}
