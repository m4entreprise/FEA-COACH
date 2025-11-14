<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
    stripePublicKey: String,
});

const showRequestForm = ref(false);

const promoForm = useForm({
    promo_code: '',
});

const requestForm = useForm({
    message: '',
});

const paymentForm = useForm({
    payment_method_id: '',
});

const submitPromoCode = () => {
    promoForm.post(route('onboarding.validate-promo'));
};

const submitPromoRequest = () => {
    requestForm.post(route('onboarding.request-promo'), {
        onSuccess: () => {
            showRequestForm.value = false;
            requestForm.reset();
        }
    });
};

const submitPayment = () => {
    // TODO: Intégration Stripe ici
    alert('Intégration Stripe à venir');
    // paymentForm.post(route('onboarding.process-payment'));
};
</script>

<template>
    <Head title="Finalisation - Étape 3/3" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center px-4 py-12">
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-purple-600 rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-pink-600 rounded-full opacity-20 blur-3xl"></div>
        </div>

        <div class="w-full max-w-2xl relative">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="text-4xl font-bold text-white mb-2">
                    Ignite <span class="text-purple-400">Coach</span>
                </div>
                <p class="text-gray-400">Créons ensemble votre site professionnel</p>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between text-sm text-gray-400 mb-2">
                    <span class="text-purple-400 font-medium">Étape 3/3</span>
                    <span>Finalisation</span>
                </div>
                <div class="w-full bg-white/10 rounded-full h-2">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- Diplômé FEA : Code Promo -->
            <div v-if="user.is_fea_graduate" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl p-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-500/20 rounded-full mb-4">
                        <span class="text-3xl">🎁</span>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Bienvenue diplômé FEA !
                    </h1>
                    <p class="text-gray-300">
                        Entrez votre code promotionnel pour bénéficier de <span class="text-purple-400 font-semibold">1 mois offert</span>
                    </p>
                </div>

                <form @submit.prevent="submitPromoCode" class="space-y-6">
                    <!-- Code Promo Input -->
                    <div>
                        <label for="promo_code" class="block text-sm font-medium text-gray-200 mb-2">
                            Code promotionnel
                        </label>
                        <input
                            id="promo_code"
                            type="text"
                            v-model="promoForm.promo_code"
                            required
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition text-center text-lg font-mono tracking-wider"
                            placeholder="FEA-XXXXX"
                        />
                        <p v-if="promoForm.errors.promo_code" class="mt-2 text-sm text-red-400 text-center">
                            {{ promoForm.errors.promo_code }}
                        </p>
                    </div>

                    <!-- Success Message -->
                    <div v-if="$page.props.flash.success" class="p-4 bg-green-500/10 border border-green-400/30 rounded-lg">
                        <p class="text-sm text-green-300">{{ $page.props.flash.success }}</p>
                    </div>

                    <!-- Request Form Toggle -->
                    <div v-if="!showRequestForm" class="p-4 bg-purple-500/10 border border-purple-400/30 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-purple-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm text-purple-300">
                                <p class="font-medium mb-2">Vous n'avez pas reçu votre code ?</p>
                                <button 
                                    @click="showRequestForm = true"
                                    type="button"
                                    class="text-purple-400 hover:text-purple-300 underline font-medium"
                                >
                                    Demander un code promo FEA →
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Request Form -->
                    <div v-else class="p-6 bg-purple-500/10 border border-purple-400/30 rounded-lg space-y-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-white">Demander un code promo</h3>
                            <button 
                                @click="showRequestForm = false"
                                type="button"
                                class="text-gray-400 hover:text-white"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <textarea
                            v-model="requestForm.message"
                            rows="4"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition resize-none"
                            placeholder="Message optionnel : Présentez-vous et expliquez pourquoi vous demandez un code promo FEA..."
                        ></textarea>
                        
                        <button 
                            @click="submitPromoRequest"
                            type="button"
                            :disabled="requestForm.processing"
                            class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition disabled:opacity-50"
                        >
                            <span v-if="!requestForm.processing">Envoyer ma demande</span>
                            <span v-else>Envoi...</span>
                        </button>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex gap-4 pt-4">
                        <a
                            :href="route('onboarding.step2')"
                            class="flex-1 px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-semibold rounded-xl transition text-center"
                        >
                            ← Retour
                        </a>
                        <button
                            type="submit"
                            :disabled="promoForm.processing"
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-xl shadow-lg transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <span v-if="!promoForm.processing">Valider mon code</span>
                            <span v-else class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Validation...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Non diplômé : Paiement Stripe -->
            <div v-else class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl p-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500/20 rounded-full mb-4">
                        <span class="text-3xl">💳</span>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Finalisons votre inscription
                    </h1>
                    <p class="text-gray-300">
                        Choisissez votre formule d'abonnement
                    </p>
                </div>

                <!-- Pricing Card -->
                <div class="mb-6 p-6 bg-gradient-to-br from-blue-600/20 to-purple-600/20 border border-blue-400/30 rounded-xl">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Abonnement Mensuel</h3>
                            <p class="text-gray-300 text-sm">Annulable à tout moment</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-white">29€</div>
                            <div class="text-sm text-gray-300">par mois</div>
                        </div>
                    </div>
                    
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Site vitrine personnalisable</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Sous-domaine personnalisé</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Gestion complète de votre contenu</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Support prioritaire</span>
                        </li>
                    </ul>
                </div>

                <!-- Stripe Payment Form Placeholder -->
                <div class="mb-6 p-8 bg-white/5 border-2 border-dashed border-white/20 rounded-xl text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <p class="text-gray-300 mb-2">Formulaire de paiement Stripe</p>
                    <p class="text-sm text-gray-400">L'intégration Stripe sera ajoutée ici</p>
                </div>

                <!-- Info Box -->
                <div class="p-4 bg-green-500/10 border border-green-400/30 rounded-lg mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div class="text-sm text-green-300">
                            <p class="font-medium">Paiement 100% sécurisé</p>
                            <p>Vos données bancaires sont protégées par Stripe, leader mondial du paiement en ligne</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex gap-4">
                    <a
                        :href="route('onboarding.step2')"
                        class="flex-1 px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-semibold rounded-xl transition text-center"
                    >
                        ← Retour
                    </a>
                    <button
                        @click="submitPayment"
                        type="button"
                        :disabled="paymentForm.processing"
                        class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <span v-if="!paymentForm.processing">Payer 29€/mois</span>
                        <span v-else class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Traitement...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
