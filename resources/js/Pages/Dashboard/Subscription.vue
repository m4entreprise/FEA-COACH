<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    subscription: Object,
    user: Object,
});

const subscriptionEndDate = computed(() => {
    if (!props.subscription.trial_ends_at) return null;
    return new Date(props.subscription.trial_ends_at).toLocaleDateString('fr-FR', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
});

const handleSubscribe = () => {
    router.post(route('dashboard.subscription.checkout'));
};

const handleManageSubscription = () => {
    router.post(route('dashboard.subscription.portal'));
};
</script>

<template>
    <Head title="Gestion de l'abonnement" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Gestion de l'abonnement
            </h2>
        </template>

        <div class="py-12 bg-gradient-to-br from-slate-50 via-purple-50 to-slate-50 dark:from-slate-900 dark:via-purple-900/20 dark:to-slate-900 min-h-screen">
            <div class="mx-auto max-w-4xl space-y-6 sm:px-6 lg:px-8">
                <!-- Success/Info Message -->
                <div v-if="$page.props.flash?.success" class="rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-xl p-6 text-white transform hover:scale-[1.01] transition-all duration-300">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="font-semibold">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <div v-if="$page.props.flash?.info" class="rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 shadow-xl p-6 text-white transform hover:scale-[1.01] transition-all duration-300">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-semibold">{{ $page.props.flash.info }}</p>
                    </div>
                </div>

                <!-- Current Subscription Status -->
                <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-white to-emerald-50 shadow-xl dark:from-gray-800 dark:to-emerald-900/20 border border-emerald-200/50 dark:border-emerald-500/30 backdrop-blur-xl">
                    <div class="p-8">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl p-4 shadow-lg">
                                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    Votre abonnement
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">
                                    Gérez votre abonnement et vos paiements
                                </p>
                            </div>
                        </div>

                        <!-- Trial Status -->
                        <div v-if="subscription.is_on_trial" class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 mb-6 border-2 border-blue-200 dark:border-blue-700">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 bg-blue-500 rounded-full p-2">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        Période d'essai active
                                    </h4>
                                    <p class="text-gray-700 dark:text-gray-300 mb-3">
                                        Vous profitez actuellement de votre période d'essai gratuite. 
                                        Vous avez encore <span class="font-bold text-blue-600 dark:text-blue-400">{{ subscription.trial_days_left }} jours</span> pour explorer toutes les fonctionnalités.
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <strong>Expire le :</strong> {{ subscriptionEndDate }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Active Subscription Status -->
                        <div v-else class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 mb-6 border-2 border-green-200 dark:border-green-700">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 bg-green-500 rounded-full p-2">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        Abonnement {{ subscription.status === 'active' ? 'actif' : 'inactif' }}
                                    </h4>
                                    <p class="text-gray-700 dark:text-gray-300">
                                        {{ subscription.status === 'active' ? 'Votre abonnement est actif et vous donne accès à toutes les fonctionnalités.' : 'Votre abonnement n\'est pas actif. Souscrivez pour continuer à utiliser la plateforme.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Nom</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ user.name }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Email</p>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ user.email }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button 
                                v-if="subscription.is_on_trial"
                                @click="handleSubscribe"
                                class="flex-1 px-6 py-4 bg-gradient-to-r from-emerald-500 to-green-600 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 shadow-lg flex items-center justify-center gap-2"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                S'abonner maintenant
                            </button>
                            <button 
                                v-else
                                @click="handleManageSubscription"
                                class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg flex items-center justify-center gap-2"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Gérer mon abonnement
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-white to-indigo-50 shadow-xl dark:from-gray-800 dark:to-indigo-900/20 border border-indigo-200/50 dark:border-indigo-500/30 backdrop-blur-xl">
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                            Formule FEA Coach Pro
                        </h3>
                        
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-8 text-white mb-6">
                            <div class="flex items-baseline gap-2 mb-4">
                                <span class="text-5xl font-bold">39€</span>
                                <span class="text-xl text-indigo-100">/mois</span>
                            </div>
                            <p class="text-indigo-100 mb-6">
                                Tout ce dont vous avez besoin pour développer votre activité de coaching en ligne
                            </p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Site web personnalisé avec votre sous-domaine</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Gestion illimitée de plans et transformations</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Formulaire de contact et gestion des prospects</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Gestion de votre base clients avec notes</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Support prioritaire par email</span>
                            </div>
                            <div class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Mises à jour et nouvelles fonctionnalités incluses</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl dark:from-gray-800 border border-gray-200/50 dark:border-gray-500/30 backdrop-blur-xl">
                    <div class="p-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                            Questions fréquentes
                        </h3>
                        
                        <div class="space-y-6">
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Puis-je annuler mon abonnement à tout moment ?
                                </h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Oui, vous pouvez annuler votre abonnement à tout moment. Vous continuerez à avoir accès jusqu'à la fin de votre période de facturation.
                                </p>
                            </div>
                            
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Comment se passe le paiement ?
                                </h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Le paiement est sécurisé par Stripe et débité automatiquement chaque mois. Vous recevrez une facture par email.
                                </p>
                            </div>
                            
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    Que se passe-t-il à la fin de ma période d'essai ?
                                </h4>
                                <p class="text-gray-600 dark:text-gray-400">
                                    À la fin de votre période d'essai, vous devrez souscrire à l'abonnement pour continuer à utiliser la plateforme. Vos données seront conservées pendant 30 jours.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
