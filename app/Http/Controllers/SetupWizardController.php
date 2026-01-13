<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SetupWizardController extends Controller
{
    const TOTAL_STEPS = 5;

    /**
     * Afficher le wizard - redirige vers l'étape en cours
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->setup_completed) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('setup.step', ['step' => $user->setup_step ?? 1]);
    }

    /**
     * Afficher une étape spécifique
     */
    public function showStep($step)
    {
        $user = Auth::user();
        $coach = $user->coach;

        if (!$coach) {
            return redirect()->route('dashboard')->with('error', 'Profil coach introuvable');
        }

        if ($user->setup_completed) {
            return redirect()->route('dashboard');
        }

        $step = (int) $step;
        if ($step < 1 || $step > self::TOTAL_STEPS) {
            return redirect()->route('setup.step', ['step' => 1]);
        }

        return Inertia::render("Setup/Step{$step}", [
            'currentStep' => $step,
            'totalSteps' => self::TOTAL_STEPS,
            'coach' => $coach,
            'user' => $user,
            'availableLayouts' => config('coach_site.layouts', []),
            'defaultLayout' => config('coach_site.default_layout', 'classic'),
        ]);
    }

    /**
     * Étape 1 : Branding (Couleurs + Logo)
     */
    public function saveStep1(Request $request)
    {
        $user = Auth::user();
        $coach = $user->coach;

        if ($request->action === 'demo') {
            $coach->update([
                'color_primary' => '#9333ea',
                'color_secondary' => '#ec4899',
                'site_layout' => config('coach_site.default_layout', 'classic'),
            ]);
        } elseif ($request->action === 'save') {
            $request->validate([
                'slug' => 'required|string|max:255|unique:coaches,slug,' . $coach->id,
                'color_primary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'color_secondary' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'site_layout' => [
                    'required',
                    'string',
                    Rule::in(array_keys(config('coach_site.layouts', []))),
                ],
            ]);

            $coach->update([
                'slug' => $request->slug,
                'color_primary' => $request->color_primary,
                'color_secondary' => $request->color_secondary,
                'site_layout' => $request->site_layout,
            ]);

            if ($request->hasFile('logo')) {
                $coach->clearMediaCollection('logo');
                $coach->addMediaFromRequest('logo')->toMediaCollection('logo');
            }
        }

        $user->update(['setup_step' => 2]);
        return redirect()->route('setup.step', ['step' => 2]);
    }

    /**
     * Étape 2 : Images (Hero + Photo profil)
     */
    public function saveStep2(Request $request)
    {
        $user = Auth::user();
        $coach = $user->coach;

        if ($request->action === 'save' && ($request->hasFile('hero_image') || $request->hasFile('profile_photo'))) {
            if ($request->hasFile('hero_image')) {
                $coach->clearMediaCollection('hero');
                $coach->addMediaFromRequest('hero_image')->toMediaCollection('hero');
            }

            if ($request->hasFile('profile_photo')) {
                $coach->clearMediaCollection('profile');
                $coach->addMediaFromRequest('profile_photo')->toMediaCollection('profile');
            }
        }

        $user->update(['setup_step' => 3]);
        return redirect()->route('setup.step', ['step' => 3]);
    }

    /**
     * Étape 3 : Contenu Principal
     */
    public function saveStep3(Request $request)
    {
        $user = Auth::user();
        $coach = $user->coach;

        if ($request->action === 'demo') {
            $coach->update([
                'hero_title' => 'Transformez votre vie dès aujourd\'hui',
                'hero_subtitle' => 'Coaching personnalisé pour atteindre vos objectifs rapidement et durablement',
                'about_text' => 'Avec plus de 10 ans d\'expérience dans le coaching sportif, je vous accompagne vers une transformation complète de votre corps et de votre état d\'esprit.',
                'method_text' => 'Ma méthode combine entraînement personnalisé, nutrition adaptée et suivi psychologique pour des résultats durables.',
                'satisfaction_rate' => 100,
                'average_rating' => 5.0,
                'show_stats' => true,
            ]);
        } elseif ($request->action === 'save') {
            $request->validate([
                'hero_title' => 'nullable|string|max:255',
                'hero_subtitle' => 'nullable|string|max:500',
                'about_text' => 'nullable|string|max:5000',
                'method_text' => 'nullable|string|max:5000',
                'satisfaction_rate' => 'nullable|integer|min:0|max:100|required_with:average_rating',
                'average_rating' => 'nullable|numeric|min:0|max:5|required_with:satisfaction_rate',
            ]);

            $showStats = !is_null($request->input('satisfaction_rate')) && !is_null($request->input('average_rating'));

            $coach->update(array_merge(
                $request->only([
                    'hero_title',
                    'hero_subtitle',
                    'about_text',
                    'method_text',
                ]),
                [
                    'show_stats' => $showStats,
                ],
                $showStats
                    ? $request->only(['satisfaction_rate', 'average_rating'])
                    : []
            ));
        }

        $user->update(['setup_step' => 4]);
        return redirect()->route('setup.step', ['step' => 4]);
    }

    /**
     * Étape 4 : Sections Avancées
     */
    public function saveStep4(Request $request)
    {
        $user = Auth::user();
        $coach = $user->coach;

        if ($request->action === 'demo') {
            $coach->update([
                'cta_text' => 'Réserver ma séance découverte',
                'method_title' => 'Ma méthode de coaching',
                'method_subtitle' => 'Une approche complète et personnalisée',
                'method_step1_title' => 'Évaluation initiale',
                'method_step1_description' => 'Analyse complète de votre situation actuelle',
                'method_step2_title' => 'Plan personnalisé',
                'method_step2_description' => 'Création d\'un programme adapté à vos objectifs',
                'method_step3_title' => 'Suivi et ajustements',
                'method_step3_description' => 'Accompagnement continu pour garantir vos résultats',
                'intermediate_cta_title' => 'Prêt à transformer votre corps et votre vie ?',
                'intermediate_cta_subtitle' => 'Ne restez pas seul face à vos objectifs. Bénéficiez d\'un accompagnement personnalisé qui vous mènera au succès.',
                'pricing_title' => 'Mes formules de coaching',
                'pricing_subtitle' => 'Choisissez la formule qui vous correspond',
                'transformations_title' => 'Leurs transformations',
                'transformations_subtitle' => 'Des résultats réels de personnes comme vous',
                'final_cta_title' => 'Prêt à commencer votre transformation ?',
                'final_cta_subtitle' => 'Ne laissez pas vos objectifs être de simples rêves. Agissez maintenant !',
            ]);
        } elseif ($request->action === 'save') {
            $coach->update($request->only([
                'cta_text', 'method_title', 'method_subtitle',
                'method_step1_title', 'method_step1_description',
                'method_step2_title', 'method_step2_description',
                'method_step3_title', 'method_step3_description',
                'intermediate_cta_title', 'intermediate_cta_subtitle',
                'pricing_title', 'pricing_subtitle',
                'transformations_title', 'transformations_subtitle',
                'final_cta_title', 'final_cta_subtitle',
            ]));
        }

        $user->update(['setup_step' => 5]);
        return redirect()->route('setup.step', ['step' => 5]);
    }

    /**
     * Étape 5 : Finalisation
     */
    public function saveStep5(Request $request)
    {
        $user = Auth::user();

        // Marquer le setup comme complété
        $user->update([
            'setup_completed' => true,
            'setup_step' => 5,
        ]);

        return redirect()->route('dashboard')->with('success', 'Félicitations ! Votre site est configuré et prêt à accueillir vos clients !');
    }

    /**
     * Vérifier la disponibilité d'un slug
     */
    public function checkSlugAvailability(Request $request)
    {
        $slug = $request->input('slug');
        $currentCoachId = Auth::user()->coach_id;
        
        $exists = Coach::where('slug', $slug)
            ->where('id', '!=', $currentCoachId)
            ->exists();
        
        return response()->json(['available' => !$exists]);
    }

    /**
     * Passer une étape
     */
    public function skipStep($step)
    {
        $user = Auth::user();
        $nextStep = ((int) $step) + 1;

        if ($nextStep > self::TOTAL_STEPS) {
            $user->update(['setup_completed' => true]);
            return redirect()->route('dashboard');
        }

        $user->update(['setup_step' => $nextStep]);
        return redirect()->route('setup.step', ['step' => $nextStep]);
    }

    /**
     * Live preview of the public coach site (using unsaved setup wizard data).
     */
    public function preview(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            abort(404, 'Coach introuvable.');
        }

        $data = $request->validate([
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'color_primary' => ['sometimes', 'nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'color_secondary' => ['sometimes', 'nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'site_layout' => ['sometimes', 'nullable', 'string', Rule::in(array_keys(config('coach_site.layouts', [])))],
            'hero_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'hero_subtitle' => ['sometimes', 'nullable', 'string', 'max:500'],
            'about_text' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'method_text' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'cta_text' => ['sometimes', 'nullable', 'string', 'max:100'],
            'intermediate_cta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'intermediate_cta_subtitle' => ['sometimes', 'nullable', 'string', 'max:500'],
            'method_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'method_subtitle' => ['sometimes', 'nullable', 'string', 'max:255'],
            'method_step1_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'method_step1_description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'method_step2_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'method_step2_description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'method_step3_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'method_step3_description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'pricing_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pricing_subtitle' => ['sometimes', 'nullable', 'string', 'max:255'],
            'transformations_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'transformations_subtitle' => ['sometimes', 'nullable', 'string', 'max:255'],
            'final_cta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'final_cta_subtitle' => ['sometimes', 'nullable', 'string', 'max:500'],
            'satisfaction_rate' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100', 'required_with:average_rating'],
            'average_rating' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:5', 'required_with:satisfaction_rate'],
            'show_stats' => ['sometimes', 'nullable', 'boolean'],
        ]);

        if (
            array_key_exists('satisfaction_rate', $data)
            || array_key_exists('average_rating', $data)
        ) {
            $data['show_stats'] = !is_null($data['satisfaction_rate'] ?? null) && !is_null($data['average_rating'] ?? null);
        }

        $coach->fill($data);

        $coach->loadMissing([
            'user',
            'media',
            'transformations' => fn ($query) => $query->orderBy('order'),
            'serviceTypes' => fn ($query) => $query->where('is_active', true)->orderBy('order')->orderBy('price'),
            'faqs' => fn ($query) => $query->where('is_active', true)->orderBy('order')->orderBy('created_at'),
        ]);

        $layouts = config('coach_site.layouts', []);
        $defaultLayout = config('coach_site.default_layout', 'classic');
        $layoutKey = $data['site_layout'] ?? ($coach->site_layout ?: $defaultLayout);
        $layoutKey = array_key_exists($layoutKey, $layouts) ? $layoutKey : $defaultLayout;
        $viewName = $layouts[$layoutKey]['view'] ?? 'coach-site.layouts.classic';

        $html = view($viewName, [
            'coach' => $coach,
            'services' => $coach->serviceTypes,
            'transformations' => $coach->transformations,
            'faqs' => $coach->faqs,
        ])->render();

        return response()->json([
            'html' => $html,
        ]);
    }
}
