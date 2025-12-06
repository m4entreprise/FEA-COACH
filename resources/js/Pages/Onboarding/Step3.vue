<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    user: Object,
    promoRequest: Object,
    stripePublicKey: String,
});

// Rediriger automatiquement si le compte est déjà activé
onMounted(() => {
    // La redirection est désormais gérée après le paiement Lemon Squeezy via webhook
});

const showRequestForm = ref(false);

const promoForm = useForm({
    promo_code: '',
});

const requestForm = useForm({
    message: '',
});

const paymentForm = useForm({});

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
    // Rediriger vers le checkout Fungies
    paymentForm.post(route('onboarding.process-payment'));
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
                        Entrez votre code promotionnel pour bénéficier de <span class="text-purple-400 font-semibold">votre tarif diplômé FEA avec 1 mois offert</span>
                    </p>
                </div>

                <!-- Demande en cours -->
                <div v-if="promoRequest && promoRequest.status === 'pending'" class="space-y-4">
                    <div class="p-6 bg-yellow-500/10 border-2 border-yellow-400/30 rounded-xl text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-500/20 rounded-full mb-4">
                            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Demande en cours de vérification</h3>
                        <p class="text-gray-300 mb-4">
                            Votre demande de code promotionnel FEA a bien été reçue et est actuellement en cours d'examen par notre équipe.
                        </p>
                        <div class="p-4 bg-white/5 rounded-lg text-left">
                            <p class="text-sm text-gray-400 mb-1">Demandé le {{ new Date(promoRequest.created_at).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</p>
                            <p v-if="promoRequest.message" class="text-sm text-gray-300 mt-2">
                                <span class="font-medium">Votre message :</span> {{ promoRequest.message }}
                            </p>
                        </div>
                        <p class="text-sm text-purple-300 mt-4">
                            💌 Vous recevrez un email contenant votre lien de paiement au tarif diplômé FEA dès que votre demande sera approuvée
                        </p>
                    </div>
                </div>

                <!-- Demande approuvée -->
                <div v-else-if="promoRequest && promoRequest.status === 'approved'" class="space-y-4">
                    <div class="p-6 bg-green-500/10 border-2 border-green-400/30 rounded-xl text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-500/20 rounded-full mb-4">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Demande approuvée</h3>
                        <p class="text-gray-300 mb-2">
                            Votre statut FEA a été validé.
                        </p>
                        <p class="text-gray-300">
                            💌 Un email contenant votre lien de paiement au tarif diplômé FEA vient de vous être envoyé. Une fois le paiement effectué, votre compte sera automatiquement activé.
                        </p>
                    </div>
                </div>

                <!-- Demande rejetée -->
                <div v-else-if="promoRequest && promoRequest.status === 'rejected'" class="space-y-4">
                    <div class="p-6 bg-red-500/10 border-2 border-red-400/30 rounded-xl">
                        <h3 class="text-xl font-bold text-white mb-2">Demande non approuvée</h3>
                        <p class="text-gray-300 mb-4">
                            Malheureusement, votre demande n'a pas pu être validée.
                        </p>
                        <div v-if="promoRequest.admin_notes" class="p-4 bg-white/5 rounded-lg mb-4">
                            <p class="text-sm text-gray-300">
                                <span class="font-medium">Raison :</span> {{ promoRequest.admin_notes }}
                            </p>
                        </div>
                        <button 
                            @click="showRequestForm = true; promoRequest = null"
                            type="button"
                            class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition"
                        >
                            Faire une nouvelle demande
                        </button>
                    </div>
                </div>

                <!-- Formulaire code promo (si pas de demande) -->
                <form v-else @submit.prevent="submitPromoCode" class="space-y-6">
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

                    <!-- Request Form Toggle (seulement si pas de demande en cours) -->
                    <div v-if="!showRequestForm && !promoRequest" class="p-4 bg-purple-500/10 border border-purple-400/30 rounded-lg">
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
                            v-if="!promoRequest || promoRequest.status === 'rejected'"
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

                <!-- Navigation pour demande en attente -->
                <div v-if="promoRequest && promoRequest.status === 'pending'" class="flex gap-4 pt-4">
                    <a
                        :href="route('onboarding.step2')"
                        class="flex-1 px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-semibold rounded-xl transition text-center"
                    >
                        ← Retour
                    </a>
                </div>
            </div>

            <!-- Non diplômé : Paiement par abonnement mensuel -->
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
                            <div class="text-3xl font-bold text-white">30€</div>
                            <div class="text-sm text-gray-300">HTVA/mois</div>
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

                <!-- Info Message -->
                <div v-if="paymentForm.errors.payment" class="mb-6 p-4 bg-red-500/10 border border-red-400/30 rounded-lg">
                    <p class="text-sm text-red-300">{{ paymentForm.errors.payment }}</p>
                </div>

                <!-- Info Box -->
                <div class="p-4 bg-green-500/10 border border-green-400/30 rounded-lg mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div class="text-sm text-green-300">
                            <p class="font-medium">Paiement 100% sécurisé</p>
                            <p>Vos données bancaires sont protégées par notre partenaire de paiement sécurisé Lemon Squeezy.</p>
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
                        <span v-if="!paymentForm.processing">Continuer vers le paiement (30€ HTVA/mois)</span>
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
