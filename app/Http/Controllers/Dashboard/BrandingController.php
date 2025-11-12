<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BrandingController extends Controller
{
    /**
     * Show the branding edit form.
     */
    public function edit(Request $request): Response
    {
        $coach = $request->user()->coach;

        return Inertia::render('Dashboard/Branding', [
            'coach' => $coach->load('media'),
        ]);
    }

    /**
     * Update the coach's branding.
     */
    public function update(Request $request)
    {
        $coach = $request->user()->coach;

        $validated = $request->validate([
            'color_primary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_secondary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
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

        return redirect()->route('dashboard.branding')
            ->with('success', 'Branding mis à jour avec succès.');
    }
}
