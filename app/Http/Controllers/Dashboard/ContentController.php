<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        $view = $request->boolean('beta')
            ? 'Coach/ContentBeta'
            : 'Dashboard/Content';

        return Inertia::render($view, [
            'coach' => $coach,
            'faqs' => $faqs,
            'faqsCount' => $faqsCount,
            'faqsActiveCount' => $faqsActiveCount,
            'profilePhotoUrl' => $profilePhotoUrl,
            'siteLayouts' => collect(config('coach_site.layouts', []))
                ->map(fn ($layout, $key) => [
                    'key' => $key,
                    'label' => $layout['label'] ?? ucfirst($key),
                    'description' => $layout['description'] ?? '',
                    'preview_image' => $layout['preview_image'] ?? null,
                ])
                ->values(),
            'defaultLayout' => config('coach_site.default_layout', 'classic'),
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
            'transformations_title' => ['nullable', 'string', 'max:255'],
            'transformations_subtitle' => ['nullable', 'string', 'max:255'],
            'final_cta_title' => ['nullable', 'string', 'max:255'],
            'final_cta_subtitle' => ['nullable', 'string', 'max:500'],
            'cta_text' => ['required', 'string', 'max:100'],
            'intermediate_cta_title' => ['nullable', 'string', 'max:255'],
            'intermediate_cta_subtitle' => ['nullable', 'string', 'max:500'],
            'satisfaction_rate' => ['required', 'integer', 'min:0', 'max:100'],
            'average_rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
        ]);

        $coach->update($validated);

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.content', $redirectParams)
            ->with('success', 'Contenu mis à jour avec succès.');
    }

    /**
     * Upload profile photo.
     */
    public function uploadProfilePhoto(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

            return redirect()->route('dashboard.content', $redirectParams)
                ->with('error', 'Aucun profil coach associé.');
        }

        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Add the photo to the profile collection
        $coach->addMediaFromRequest('profile_photo')
            ->toMediaCollection('profile');

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.content', $redirectParams)
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

        $redirectParams = $request->boolean('beta') ? ['beta' => 1] : [];

        return redirect()->route('dashboard.content', $redirectParams)
            ->with('success', 'Photo de profil supprimée avec succès.');
    }

    /**
     * Render a live preview of the coach public site using unsaved data.
     */
    public function preview(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            abort(404, 'Coach introuvable.');
        }

        $data = $request->validate([
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
            'transformations_title' => ['nullable', 'string', 'max:255'],
            'transformations_subtitle' => ['nullable', 'string', 'max:255'],
            'final_cta_title' => ['nullable', 'string', 'max:255'],
            'final_cta_subtitle' => ['nullable', 'string', 'max:500'],
            'cta_text' => ['required', 'string', 'max:100'],
            'intermediate_cta_title' => ['nullable', 'string', 'max:255'],
            'intermediate_cta_subtitle' => ['nullable', 'string', 'max:500'],
            'satisfaction_rate' => ['required', 'integer', 'min:0', 'max:100'],
            'average_rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'tiktok_url' => ['nullable', 'url', 'max:255'],
            'site_layout' => ['nullable', 'string', Rule::in(array_keys(config('coach_site.layouts', [])))],
        ]);

        $coach->fill($data);

        $coach->loadMissing([
            'user',
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
