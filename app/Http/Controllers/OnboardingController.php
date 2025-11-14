<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\PromoCodeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        // Ici, vous pourrez ajouter la logique de validation du code promo
        // Pour l'instant, on accepte tous les codes commençant par "FEA-"
        if (str_starts_with($request->promo_code, 'FEA-')) {
            $user->fea_promo_code = $request->promo_code;
            $user->subscription_status = 'active_promo';
            $user->onboarding_completed = true;
            $user->trial_ends_at = now()->addMonth();
            $user->save();

            // Créer le profil Coach
            $this->createCoachProfile($user);

            return redirect()->route('setup.index')->with('success', 'Bienvenue ! Vous bénéficiez de 1 mois offert. Configurons maintenant votre site !');
        }

        return back()->withErrors(['promo_code' => 'Code promo invalide. Veuillez vérifier votre code ou contacter FEA.']);
    }

    /**
     * Traiter le paiement Stripe
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            // Configuration Stripe (à adapter selon votre intégration)
            // Stripe::setApiKey(config('services.stripe.secret'));
            
            // Créer ou récupérer le client Stripe
            // $customer = \Stripe\Customer::create([
            //     'email' => $user->email,
            //     'name' => $user->first_name . ' ' . $user->last_name,
            //     'payment_method' => $request->payment_method_id,
            //     'invoice_settings' => [
            //         'default_payment_method' => $request->payment_method_id,
            //     ],
            // ]);

            // Créer l'abonnement
            // $subscription = \Stripe\Subscription::create([
            //     'customer' => $customer->id,
            //     'items' => [['price' => config('services.stripe.price_id')]],
            //     'expand' => ['latest_invoice.payment_intent'],
            // ]);

            // Mise à jour de l'utilisateur
            // $user->stripe_customer_id = $customer->id;
            $user->subscription_status = 'active';
            $user->onboarding_completed = true;
            $user->save();

            // Créer le profil Coach
            $this->createCoachProfile($user);

            return redirect()->route('setup.index')->with('success', 'Bienvenue ! Votre abonnement est actif. Configurons votre site !');
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Erreur lors du paiement : ' . $e->getMessage()]);
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
