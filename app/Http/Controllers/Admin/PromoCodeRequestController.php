<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\PromoCodeBatch;
use App\Models\PromoCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PromoCodeRequestController extends Controller
{
    /**
     * Afficher toutes les demandes de code promo
     */
    public function index()
    {
        $requests = PromoCodeRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $batches = PromoCodeBatch::with(['creator', 'codes'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'label' => $batch->label,
                    'notes' => $batch->notes,
                    'quantity' => $batch->quantity,
                    'codes_count' => $batch->codes->count(),
                    'created_by' => $batch->creator?->name,
                    'created_at' => $batch->created_at->format('d/m/Y H:i'),
                    'codes' => $batch->codes->pluck('code'),
                ];
            });

        return Inertia::render('Admin/PromoCodeRequests/Index', [
            'requests' => $requests,
            'batches' => $batches,
        ]);
    }

    /**
     * Générer un batch de codes promo
     */
    public function generateBatch(Request $request)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:500',
            'label' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();

        $batch = PromoCodeBatch::create([
            'created_by' => $user->id,
            'quantity' => $data['quantity'],
            'label' => $data['label'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Générer les codes
        for ($i = 0; $i < $batch->quantity; $i++) {
            $code = 'FEA-' . strtoupper(Str::random(8));

            $batch->codes()->create([
                'code' => $code,
                'status' => 'unused',
            ]);
        }

        return redirect()->back()->with('success', $batch->quantity.' codes promo FEA ont été générés.');
    }

    /**
     * Approuver une demande et générer un code promo
     */
    public function approve(Request $request, PromoCodeRequest $promoCodeRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        // Générer un code promo unique
        $promoCode = 'FEA-' . strtoupper(Str::random(8));

        $promoCodeRequest->status = 'approved';
        $promoCodeRequest->promo_code = $promoCode;
        $promoCodeRequest->admin_notes = $request->admin_notes;
        $promoCodeRequest->save();

        // Activer directement le compte de l'utilisateur
        $user = $promoCodeRequest->user;
        if ($user && !$user->onboarding_completed) {
            $user->fea_promo_code = $promoCode;
            $user->subscription_status = 'active_promo';
            $user->onboarding_completed = true;
            $user->trial_ends_at = now()->addMonth();
            $user->save();

            // Créer le profil Coach si nécessaire
            if (!$user->coach_id) {
                $this->createCoachProfile($user);
            }
        }

        // L'utilisateur sera redirigé vers le setup wizard à sa prochaine connexion
        // TODO: Envoyer un email à l'utilisateur pour lui dire que son compte est activé

        return redirect()->back()->with('success', 'Demande approuvée et compte activé ! Code : ' . $promoCode);
    }

    /**
     * Créer le profil Coach pour l'utilisateur
     */
    private function createCoachProfile($user): void
    {
        $fullName = trim($user->first_name . ' ' . $user->last_name);
        $baseSlug = Str::slug($fullName);
        $slug = $baseSlug;
        $counter = 1;

        while (Coach::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $coach = Coach::create([
            'name' => $fullName,
            'slug' => $slug,
            'primary_color' => '#9333ea',
            'secondary_color' => '#ec4899',
            'is_active' => true,
            'hero_title' => 'Transformez votre vie dès aujourd\'hui',
            'hero_subtitle' => 'Coaching personnalisé pour atteindre vos objectifs',
            'about_text' => 'Bienvenue ! Je suis ' . $fullName . ', votre coach sportif dédié.',
            'method_text' => 'Ma méthode repose sur un accompagnement personnalisé et adapté à vos besoins.',
            'cta_text' => 'Réserver ma séance découverte',
        ]);

        $user->coach_id = $coach->id;
        $user->save();
    }

    /**
     * Rejeter une demande
     */
    public function reject(Request $request, PromoCodeRequest $promoCodeRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $promoCodeRequest->status = 'rejected';
        $promoCodeRequest->admin_notes = $request->admin_notes;
        $promoCodeRequest->save();

        // TODO: Envoyer un email à l'utilisateur

        return redirect()->back()->with('success', 'Demande rejetée.');
    }
}
