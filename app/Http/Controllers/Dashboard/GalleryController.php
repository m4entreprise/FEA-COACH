<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CoachTransformation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    /**
     * Display the transformations gallery.
     */
    public function index(Request $request): Response
    {
        $coach = $request->user()->coach;

        $transformations = $coach->transformations()->with('media')->get();

        return Inertia::render('Coach/GalleryBeta', [
            'transformations' => $transformations,
        ]);
    }

    /**
     * Store a new transformation.
     */
    public function store(Request $request)
    {
        $coach = $request->user()->coach;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'before' => ['required', 'image', 'max:5120'], // 5MB max
            'after' => ['required', 'image', 'max:5120'],
        ]);

        // Get the next order number
        $nextOrder = $coach->transformations()->max('order') + 1;

        $transformation = $coach->transformations()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $nextOrder,
        ]);

        // Handle before/after image uploads
        if ($request->hasFile('before')) {
            $transformation->addMediaFromRequest('before')
                ->toMediaCollection('before');
        }

        if ($request->hasFile('after')) {
            $transformation->addMediaFromRequest('after')
                ->toMediaCollection('after');
        }

        return redirect()->route('dashboard.gallery')
            ->with('success', 'Transformation ajoutée avec succès.');
    }

    /**
     * Remove a transformation.
     */
    public function destroy(Request $request, CoachTransformation $transformation)
    {
        // Ensure the transformation belongs to the authenticated coach
        if ($transformation->coach_id !== $request->user()->coach_id) {
            abort(403);
        }

        $transformation->delete();

        return redirect()->route('dashboard.gallery')
            ->with('success', 'Transformation supprimée avec succès.');
    }

    /**
     * Reorder transformations based on drag & drop payload.
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

        $coachTransformationIds = $coach->transformations()->pluck('id')->toArray();

        $orderPayload = collect($validated['order'])
            ->filter(fn ($item) => in_array($item['id'], $coachTransformationIds, true))
            ->sortBy('order')
            ->values();

        foreach ($orderPayload as $index => $item) {
            CoachTransformation::where('id', $item['id'])
                ->where('coach_id', $coach->id)
                ->update(['order' => $index]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    /**
     * Provide a fullscreen preview of the public site with current transformations.
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
            'plans' => fn ($query) => $query->where('is_active', true)->orderBy('price'),
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
