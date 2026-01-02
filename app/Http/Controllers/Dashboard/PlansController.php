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
            ->orderBy('order')
            ->orderBy('created_at')
            ->get()
            ->map(fn($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description,
                'price' => $plan->price,
                'order' => $plan->order,
                'cta_url' => $plan->cta_url,
                'is_active' => $plan->is_active,
            ]);

        return Inertia::render('Coach/PlansBeta', [
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
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $nextOrder = ($coach->plans()->max('order') ?? -1) + 1;

        $coach->plans()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'order' => array_key_exists('order', $validated) ? $validated['order'] : $nextOrder,
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
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'cta_url' => $validated['cta_url'] ?? null,
            'order' => array_key_exists('order', $validated) ? $validated['order'] : $plan->order,
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

    /**
     * Reorder plans using drag & drop payload.
     */
    public function reorder(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            abort(404, 'Aucun coach associé.');
        }

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'integer'],
            'order.*.order' => ['required', 'integer', 'min:0'],
        ]);

        $coachPlanIds = $coach->plans()->pluck('id')->toArray();
        $orderPayload = collect($validated['order'])
            ->filter(fn ($item) => in_array($item['id'], $coachPlanIds))
            ->sortBy('order')
            ->values();

        foreach ($orderPayload as $index => $item) {
            Plan::where('id', $item['id'])
                ->where('coach_id', $coach->id)
                ->update(['order' => $index]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    /**
     * Render live preview of the public site reflecting plan changes.
     */
    public function preview(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            abort(404, 'Coach introuvable.');
        }

        $coach->loadMissing([
            'user',
            'media',
            'transformations' => fn ($query) => $query->with('media')->orderBy('order'),
            'plans' => fn ($query) => $query->where('is_active', true)->orderBy('order')->orderBy('price'),
            'faqs' => fn ($query) => $query->where('is_active', true)->orderBy('order')->orderBy('created_at'),
        ]);

        $layouts = config('coach_site.layouts', []);
        $defaultLayout = config('coach_site.default_layout', 'classic');
        $layoutKey = $coach->site_layout ?: $defaultLayout;
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
