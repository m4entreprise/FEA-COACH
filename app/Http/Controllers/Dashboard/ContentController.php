<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContentController extends Controller
{
    /**
     * Show the content edit form.
     */
    public function edit(Request $request): Response
    {
        $coach = $request->user()->coach;

        $faqs = $coach ? $coach->faqs()
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('created_at')
            ->limit(5) // Limiter à 5 FAQs max pour l'aperçu
            ->get()
            ->map(fn($faq) => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
            ])
            : collect();
        
        $faqsCount = $coach ? $coach->faqs()->count() : 0;
        $faqsActiveCount = $coach ? $coach->faqs()->where('is_active', true)->count() : 0;

        return Inertia::render('Dashboard/Content', [
            'coach' => $coach,
            'faqs' => $faqs,
            'faqsCount' => $faqsCount,
            'faqsActiveCount' => $faqsActiveCount,
        ]);
    }

    /**
     * Update the coach's content.
     */
    public function update(Request $request)
    {
        $coach = $request->user()->coach;

        $validated = $request->validate([
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'about_text' => ['nullable', 'string', 'max:5000'],
            'method_text' => ['nullable', 'string', 'max:5000'],
            'cta_text' => ['required', 'string', 'max:100'],
        ]);

        $coach->update($validated);

        return redirect()->route('dashboard.content')
            ->with('success', 'Contenu mis à jour avec succès.');
    }
}
