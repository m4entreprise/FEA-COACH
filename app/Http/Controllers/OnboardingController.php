<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        return Inertia::render('Onboarding/Step3', [
            'user' => $user,
            'stripePublicKey' => config('services.stripe.key'),
        ]);
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
            $user->save();

            return redirect()->route('dashboard')->with('success', 'Bienvenue ! Vous bénéficiez de 1 mois offert grâce à votre code FEA.');
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

            return redirect()->route('dashboard')->with('success', 'Bienvenue ! Votre abonnement est actif.');
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Erreur lors du paiement : ' . $e->getMessage()]);
        }
    }
}
