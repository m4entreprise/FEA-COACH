<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
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
            'coach' => $coach ? $coach->only(['slug', 'subdomain']) : null,
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

        $coach->faqs()->create([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'order' => $validated['order'] ?? 0,
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
            'order' => $validated['order'] ?? $faq->order,
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
}
