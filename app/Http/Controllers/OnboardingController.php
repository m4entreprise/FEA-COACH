<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\PromoCodeRequest;
use App\Models\User;
use App\Services\LemonSqueezyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OnboardingController extends Controller
{
    /**
     * Afficher la première étape : choix du type de compte
     */
    public function step1()
    {
        $user = Auth::user();
        
        // Rediriger si l'onboarding est déjà complété
        if ($user->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Onboarding/Step1');
    }

    /**
     * Enregistrer le choix du type de compte
     */
    public function storeStep1(Request $request)
    {
        $request->validate([
            'is_fea_graduate' => 'required|boolean',
        ]);

        $user = Auth::user();
        $user->is_fea_graduate = $request->is_fea_graduate;
        $user->save();

        return redirect()->route('onboarding.step2');
    }

    /**
     * Afficher la deuxième étape : informations personnelles
     */
    public function step2()
    {
        $user = Auth::user();
        
        if ($user->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Onboarding/Step2', [
            'user' => $user,
        ]);
    }

    /**
     * Enregistrer les informations personnelles
     */
    public function storeStep2(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'vat_number' => 'nullable|string|max:255',
            'legal_address' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->vat_number = $request->vat_number;
        $user->legal_address = $request->legal_address;
        $user->save();

        return redirect()->route('onboarding.step3');
    }

    /**
     * Afficher la troisième étape : Code promo FEA ou Paiement
     */
    public function step3()
    {
        $user = Auth::user();
        
        if ($user->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        // Charger la demande de code promo existante si présente
        $promoRequest = PromoCodeRequest::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('Onboarding/Step3', [
            'user' => $user,
            'promoRequest' => $promoRequest,
            'stripePublicKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * Soumettre une demande de code promo
     */
    public function requestPromoCode(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Vérifier s'il y a déjà une demande en attente
        $existingRequest = PromoCodeRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return back()->withErrors(['message' => 'Vous avez déjà une demande en cours de traitement.']);
        }

        // Créer la demande
        PromoCodeRequest::create([
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Votre demande a été envoyée ! Vous recevrez un email avec votre code promo une fois validée par notre équipe.');
    }

    /**
     * Valider le code promo FEA
     */
    public function validatePromoCode(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string',
        ]);

        $user = Auth::user();

        // Ici, vous pourrez ajouter la logique de validation réelle du code promo
        // Pour l'instant, on accepte tous les codes commençant par "FEA-"
        if (! str_starts_with($request->promo_code, 'FEA-')) {
            return back()->withErrors(['promo_code' => 'Code promo invalide. Veuillez vérifier votre code ou contacter FEA.']);
        }

        // Enregistrer le code saisi
        $user->fea_promo_code = $request->promo_code;
        $user->save();

        // Créer une session de checkout Lemon Squeezy pour le tarif diplômé FEA (20€/mois)
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
                'source' => 'onboarding_promo_code',
            ], (int) config('lemonsqueezy.variant_fea'));

            $checkoutUrl = $checkoutSession['data']['attributes']['url'] ?? null;

            if (! $checkoutUrl) {
                Log::error('Lemon Squeezy checkout session missing URL for FEA promo code', [
                    'user_id' => $user->id,
                    'response' => $checkoutSession,
                ]);

                return back()->withErrors(['promo_code' => 'Une erreur est survenue lors de la création du lien de paiement. Veuillez réessayer plus tard.']);
            }

            // Envoyer un email avec le lien de paiement
            Mail::raw(
                "Bonjour {$user->first_name},\n\n" .
                "Votre statut FEA a été reconnu. Pour finaliser votre inscription à UNICOACH au tarif diplômé (20€ HTVA / mois), veuillez effectuer votre paiement via le lien suivant :\n\n" .
                $checkoutUrl . "\n\n" .
                "Une fois le paiement effectué, votre compte sera automatiquement activé et vous pourrez configurer votre site de coach.\n\n" .
                "À très vite,\nL'équipe UNICOACH",
                function ($message) use ($user) {
                    $message->to($user->email, $user->first_name . ' ' . $user->last_name)
                        ->subject('Votre lien de paiement UNICOACH (tarif diplômé FEA)');
                }
            );

            return back()->with('success', 'Votre code a été accepté. Un email contenant votre lien de paiement au tarif diplômé FEA vient de vous être envoyé.');

        } catch (\Exception $e) {
            Log::error('Failed to create Lemon Squeezy checkout for FEA promo code', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['promo_code' => 'Une erreur est survenue lors de la création du lien de paiement. Veuillez réessayer plus tard.']);
        }
    }

    /**
     * Créer une session de checkout Lemon Squeezy pour le paiement
     */
    public function processPayment(Request $request)
    {
        $user = Auth::user();

        try {
            $service = new LemonSqueezyService();

            // Créer une session de checkout Lemon Squeezy pour les non diplômés FEA (30€/mois)
            $checkoutSession = $service->createCheckoutSession([
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name,
                'vat_number' => $user->vat_number,
            ], [
                'onboarding' => true,
                'source' => 'onboarding_standard',
            ], (int) config('lemonsqueezy.variant_non_fea'));

            $checkoutUrl = $checkoutSession['data']['attributes']['url'] ?? null;

            if ($checkoutUrl) {
                // Return URL for frontend redirect instead of server redirect (Inertia compatibility)
                return response()->json(['checkout_url' => $checkoutUrl]);
            }

            Log::error('Lemon Squeezy checkout session missing URL', ['response' => $checkoutSession]);
            return back()->withErrors(['payment' => 'Erreur lors de la création de la session de paiement.']);

        } catch (\Exception $e) {
            Log::error('Failed to create Lemon Squeezy checkout session in onboarding', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['payment' => 'Une erreur est survenue. Veuillez réessayer.']);
        }
    }

    /**
     * Créer le profil Coach pour l'utilisateur
     */
    private function createCoachProfile(User $user): void
    {
        // Vérifier si le profil Coach existe déjà
        if ($user->coach_id) {
            return;
        }

        // Générer un slug unique à partir du nom complet
        $fullName = trim($user->first_name . ' ' . $user->last_name);
        $baseSlug = Str::slug($fullName);
        $slug = $baseSlug;
        $counter = 1;

        // S'assurer que le slug est unique
        while (Coach::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Créer le profil Coach
        $coach = Coach::create([
            'name' => $fullName,
            'slug' => $slug,
            'primary_color' => '#9333ea', // Purple par défaut
            'secondary_color' => '#ec4899', // Pink par défaut
            'is_active' => true,
            'hero_title' => 'Transformez votre vie dès aujourd\'hui',
            'hero_subtitle' => 'Coaching personnalisé pour atteindre vos objectifs',
            'about_text' => 'Bienvenue ! Je suis ' . $fullName . ', votre coach sportif dédié.',
            'method_text' => 'Ma méthode repose sur un accompagnement personnalisé et adapté à vos besoins.',
            'cta_text' => 'Réserver ma séance découverte',
        ]);

        // Associer le coach à l'utilisateur
        $user->coach_id = $coach->id;
        $user->save();
    }
}
