<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlansController extends Controller
{
    /**
     * Display a listing of the coach's plans.
     */
    public function index(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun profil coach associé.');
        }

        $plans = $coach->plans()
            ->orderBy('price')
            ->get()
            ->map(fn($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => $plan->price,
                'cta_url' => $plan->cta_url,
                'is_active' => $plan->is_active,
            ]);

        return Inertia::render('Dashboard/Plans', [
            'plans' => $plans,
        ]);
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun profil coach associé.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'cta_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $coach->plans()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('dashboard.plans')
            ->with('success', 'Plan créé avec succès.');
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Plan $plan)
    {
        $coach = $request->user()->coach;

        // Verify the plan belongs to the coach
        if (!$coach || $plan->coach_id !== $coach->id) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'cta_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'is_active' => $validated['is_active'] ?? $plan->is_active,
        ]);

        return redirect()->route('dashboard.plans')
            ->with('success', 'Plan mis à jour avec succès.');
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(Request $request, Plan $plan)
    {
        $coach = $request->user()->coach;

        // Verify the plan belongs to the coach
        if (!$coach || $plan->coach_id !== $coach->id) {
            abort(403, 'Accès non autorisé.');
        }

        $plan->delete();

        return redirect()->route('dashboard.plans')
            ->with('success', 'Plan supprimé avec succès.');
    }
}
