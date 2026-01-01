<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BrandingController extends Controller
{
    /**
     * Show the branding edit form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        
        // Si l'utilisateur n'a pas de coach, on le crée automatiquement
        if (!$user->coach) {
            $coach = \App\Models\Coach::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'slug' => \Illuminate\Support\Str::slug($user->name),
                'color_primary' => '#3B82F6',
                'color_secondary' => '#10B981',
                'site_layout' => config('coach_site.default_layout'),
            ]);
            
            // Lier le coach à l'utilisateur
            $user->coach_id = $coach->id;
            $user->save();
        }
        
        $coach = $user->coach;

        $view = $request->boolean('beta')
            ? 'Coach/BrandingBeta'
            : 'Dashboard/Branding';

        return Inertia::render($view, [
            'coach' => $coach->load('media'),
            'availableLayouts' => config('coach_site.layouts'),
            'defaultLayout' => config('coach_site.default_layout'),
        ]);
    }

    /**
     * Update the coach's branding.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        // Si l'utilisateur n'a pas de coach, on le crée automatiquement
        if (!$user->coach) {
            $coach = \App\Models\Coach::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'slug' => \Illuminate\Support\Str::slug($user->name),
                'color_primary' => '#3B82F6',
                'color_secondary' => '#10B981',
                'site_layout' => config('coach_site.default_layout'),
            ]);
            
            // Lier le coach à l'utilisateur
            $user->coach_id = $coach->id;
            $user->save();
        }
        
        $coach = $user->coach;

        $validated = $request->validate([
            'color_primary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_secondary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'site_layout' => [
                'required',
                Rule::in(array_keys(config('coach_site.layouts'))),
            ],
        ]);

        $coach->update($validated);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $coach->clearMediaCollection('logo');
            $coach->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        }

        // Handle hero image upload
        if ($request->hasFile('hero')) {
            $coach->clearMediaCollection('hero');
            $coach->addMediaFromRequest('hero')
                ->toMediaCollection('hero');
        }

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.branding', $redirectParams)
            ->with('success', 'Branding mis à jour avec succès.');
    }

    /**
     * Render a live preview of the coach public site using branding updates.
     */
    public function preview(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            abort(404, 'Coach introuvable.');
        }

        $data = $request->validate([
            'color_primary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_secondary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'site_layout' => [
                'nullable',
                'string',
                Rule::in(array_keys(config('coach_site.layouts', []))),
            ],
        ]);

        $coach->fill($data);

        $coach->loadMissing([
            'user',
            'media',
            'transformations' => fn ($query) => $query->orderBy('order'),
            'plans' => fn ($query) => $query->where('is_active', true)->orderBy('price'),
            'faqs' => fn ($query) => $query->where('is_active', true)->orderBy('order')->orderBy('created_at'),
        ]);

        $layouts = config('coach_site.layouts', []);
        $defaultLayout = config('coach_site.default_layout', 'classic');
        $layoutKey = $data['site_layout'] ?? ($coach->site_layout ?: $defaultLayout);
        $layoutKey = array_key_exists($layoutKey, $layouts) ? $layoutKey : $defaultLayout;
        $viewName = $layouts[$layoutKey]['view'] ?? 'coach-site.layouts.classic';

        $html = view($viewName, [
            'coach' => $coach,
            'plans' => $coach->plans,
            'transformations' => $coach->transformations,
            'faqs' => $coach->faqs,
        ])->render();

        return response()->json([
            'html' => $html,
        ]);
    }
}
