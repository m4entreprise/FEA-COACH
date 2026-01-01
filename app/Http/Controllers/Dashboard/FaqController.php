<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class FaqController extends Controller
{
    /**
     * Display a listing of the coach's FAQs.
     */
    public function index(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun profil coach associé.');
        }

        $faqs = $coach->faqs()
            ->orderBy('order')
            ->orderBy('created_at')
            ->get()
            ->map(fn($faq) => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'order' => $faq->order,
                'is_active' => $faq->is_active,
            ]);

        $view = $request->boolean('beta')
            ? 'Coach/FaqBeta'
            : 'Dashboard/Faq';

        return Inertia::render($view, [
            'faqs' => $faqs,
        ]);
    }

    /**
     * Store a newly created FAQ.
     */
    public function store(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Aucun profil coach associé.');
        }

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:2000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $nextOrder = ($coach->faqs()->max('order') ?? -1) + 1;
        $order = array_key_exists('order', $validated)
            ? $validated['order']
            : $nextOrder;

        $coach->faqs()->create([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'order' => $order,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.faq', $redirectParams)
            ->with('success', 'Question ajoutée avec succès.');
    }

    /**
     * Update the specified FAQ.
     */
    public function update(Request $request, Faq $faq)
    {
        $coach = $request->user()->coach;

        // Verify the FAQ belongs to the coach
        if (!$coach || $faq->coach_id !== $coach->id) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:2000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $faq->update([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'order' => array_key_exists('order', $validated) ? $validated['order'] : $faq->order,
            'is_active' => $validated['is_active'] ?? $faq->is_active,
        ]);

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.faq', $redirectParams)
            ->with('success', 'Question mise à jour avec succès.');
    }

    /**
     * Remove the specified FAQ.
     */
    public function destroy(Request $request, Faq $faq)
    {
        $coach = $request->user()->coach;

        // Verify the FAQ belongs to the coach
        if (!$coach || $faq->coach_id !== $coach->id) {
            abort(403, 'Accès non autorisé.');
        }

        $faq->delete();

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.faq', $redirectParams)
            ->with('success', 'Question supprimée avec succès.');
    }

    /**
     * Reorder FAQs using drag & drop payload.
     */
    public function reorder(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            throw ValidationException::withMessages([
                'order' => 'Aucun profil coach associé.',
            ]);
        }

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'integer'],
            'order.*.order' => ['required', 'integer', 'min:0'],
        ]);

        $coachFaqIds = $coach->faqs()->pluck('id')->toArray();
        $orderPayload = collect($validated['order'])
            ->filter(fn ($item) => in_array($item['id'], $coachFaqIds))
            ->sortBy('order')
            ->values();

        foreach ($orderPayload as $index => $item) {
            Faq::where('id', $item['id'])
                ->where('coach_id', $coach->id)
                ->update(['order' => $index]);
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
