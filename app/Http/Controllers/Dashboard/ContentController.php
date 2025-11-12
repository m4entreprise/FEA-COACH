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
            ->orderBy('order')
            ->orderBy('created_at')
            ->get()
            ->map(fn($faq) => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'order' => $faq->order,
                'is_active' => $faq->is_active,
            ])
            : collect();
        
        $faqsCount = $coach ? $coach->faqs()->count() : 0;
        $faqsActiveCount = $coach ? $coach->faqs()->where('is_active', true)->count() : 0;

        // Get media URLs
        $profilePhotoUrl = $coach ? $coach->getFirstMediaUrl('profile') : null;

        return Inertia::render('Dashboard/Content', [
            'coach' => $coach,
            'faqs' => $faqs,
            'faqsCount' => $faqsCount,
            'faqsActiveCount' => $faqsActiveCount,
            'profilePhotoUrl' => $profilePhotoUrl,
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
            'method_title' => ['nullable', 'string', 'max:255'],
            'method_subtitle' => ['nullable', 'string', 'max:255'],
            'method_step1_title' => ['nullable', 'string', 'max:255'],
            'method_step1_description' => ['nullable', 'string', 'max:1000'],
            'method_step2_title' => ['nullable', 'string', 'max:255'],
            'method_step2_description' => ['nullable', 'string', 'max:1000'],
            'method_step3_title' => ['nullable', 'string', 'max:255'],
            'method_step3_description' => ['nullable', 'string', 'max:1000'],
            'pricing_title' => ['nullable', 'string', 'max:255'],
            'pricing_subtitle' => ['nullable', 'string', 'max:255'],
            'cta_text' => ['required', 'string', 'max:100'],
            'satisfaction_rate' => ['required', 'integer', 'min:0', 'max:100'],
            'average_rating' => ['required', 'numeric', 'min:0', 'max:5'],
        ]);

        $coach->update($validated);

        return redirect()->route('dashboard.content')
            ->with('success', 'Contenu mis à jour avec succès.');
    }

    /**
     * Upload profile photo.
     */
    public function uploadProfilePhoto(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard.content')
                ->with('error', 'Aucun profil coach associé.');
        }

        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Add the photo to the profile collection
        $coach->addMediaFromRequest('profile_photo')
            ->toMediaCollection('profile');

        return redirect()->route('dashboard.content')
            ->with('success', 'Photo de profil mise à jour avec succès.');
    }

    /**
     * Delete profile photo.
     */
    public function deleteProfilePhoto(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard.content')
                ->with('error', 'Aucun profil coach associé.');
        }

        $coach->clearMediaCollection('profile');

        return redirect()->route('dashboard.content')
            ->with('success', 'Photo de profil supprimée avec succès.');
    }
}
