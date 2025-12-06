<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\PromoCodeBatch;
use App\Models\PromoCodeRequest;
use App\Services\LemonSqueezyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $user = $promoCodeRequest->user;

        if ($user && ! $user->onboarding_completed) {
            $user->fea_promo_code = $promoCode;
            $user->save();

            try {
                $service = new LemonSqueezyService();

                $checkoutSession = $service->createCheckoutSession([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name,
                    'vat_number' => $user->vat_number,
                ], [
                    'is_fea_graduate' => true,
                    'onboarding' => true,
                    'source' => 'onboarding_promo_request_approved',
                    'promo_request_id' => $promoCodeRequest->id,
                ], (int) config('lemonsqueezy.variant_fea'));

                $checkoutUrl = $checkoutSession['data']['attributes']['url'] ?? null;

                if (! $checkoutUrl) {
                    Log::error('Lemon Squeezy checkout session missing URL for approved promo request', [
                        'user_id' => $user->id,
                        'promo_request_id' => $promoCodeRequest->id,
                        'response' => $checkoutSession,
                    ]);
                } else {
                    // Envoyer un email avec le lien de paiement
                    Mail::raw(
                        "Bonjour {$user->first_name},\n\n" .
                        "Votre demande de validation FEA vient d'être approuvée. Pour finaliser votre inscription à Ignite Coach au tarif diplômé (20€ HTVA / mois), veuillez effectuer votre paiement via le lien suivant :\n\n" .
                        $checkoutUrl . "\n\n" .
                        "Une fois le paiement effectué, votre compte sera automatiquement activé et vous pourrez configurer votre site de coach.\n\n" .
                        "À très vite,\nL'équipe Ignite Coach",
                        function ($message) use ($user) {
                            $message->to($user->email, $user->first_name . ' ' . $user->last_name)
                                ->subject('Votre lien de paiement Ignite Coach (validation FEA approuvée)');
                        }
                    );
                }

            } catch (\Exception $e) {
                Log::error('Failed to create Lemon Squeezy checkout for approved promo request', [
                    'user_id' => $user->id,
                    'promo_request_id' => $promoCodeRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Demande approuvée. Un email avec le lien de paiement FEA a été envoyé au coach. Code : ' . $promoCode);
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
