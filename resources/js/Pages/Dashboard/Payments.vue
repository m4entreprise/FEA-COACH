<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CheckCircleIcon, XCircleIcon, CreditCardIcon, BanknotesIcon, CalendarIcon, ClockIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    hasPaymentsModule: Boolean,
    paymentsModulePrice: Number,
    stripeAccount: Object,
    stats: Object,
});

const activateForm = useForm({});
const connectForm = useForm({});

const activateModule = () => {
    activateForm.post(route('dashboard.payments.activate'));
};

const connectStripe = () => {
    connectForm.post(route('dashboard.payments.connect'));
};

const openStripeDashboard = () => {
    window.location.href = route('dashboard.payments.dashboard');
};
</script>

<template>
    <Head title="Paiements & Réservations" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Paiements & Réservations
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Module non activé -->
                <div v-if="!hasPaymentsModule" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-center py-8">
                            <CreditCardIcon class="mx-auto h-16 w-16 text-gray-400 mb-4" />
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                Module Paiements & Réservations
                            </h3>
                            <p class="text-lg text-gray-600 mb-6">
                                Module premium - {{ paymentsModulePrice }}€ HTVA/mois
                            </p>

                            <div class="max-w-2xl mx-auto mb-8">
                                <div class="bg-blue-50 rounded-lg p-6 space-y-3 text-left">
                                    <div class="flex items-start">
                                        <CheckCircleIcon class="h-6 w-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" />
                                        <p class="text-gray-700">
                                            <strong>Encaissez vos séances en ligne</strong> avec paiement sécurisé par Stripe
                                        </p>
                                    </div>
                                    <div class="flex items-start">
                                        <CheckCircleIcon class="h-6 w-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" />
                                        <p class="text-gray-700">
                                            <strong>Système de réservation intégré</strong> sur votre site coach
                                        </p>
                                    </div>
                                    <div class="flex items-start">
                                        <CheckCircleIcon class="h-6 w-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" />
                                        <p class="text-gray-700">
                                            <strong>Calendrier de disponibilités</strong> automatique
                                        </p>
                                    </div>
                                    <div class="flex items-start">
                                        <CheckCircleIcon class="h-6 w-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" />
                                        <p class="text-gray-700">
                                            <strong>0% de commission</strong> sur vos transactions (frais Stripe standard : ~1,4%)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <button
                                @click="activateModule"
                                :disabled="activateForm.processing"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                            >
                                Activer pour {{ paymentsModulePrice }}€/mois
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Module activé mais Stripe non connecté -->
                <div v-else-if="hasPaymentsModule && !stripeAccount.connected" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <CheckCircleIcon class="h-6 w-6 text-green-500 mr-2" />
                            <span class="text-sm text-gray-600">Module premium activé</span>
                        </div>

                        <div class="text-center py-8">
                            <div class="mx-auto h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                <CreditCardIcon class="h-8 w-8 text-blue-600" />
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                Connecter votre compte Stripe
                            </h3>
                            <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
                                Pour recevoir les paiements de vos clients, vous devez connecter un compte Stripe.
                            </p>

                            <div class="max-w-md mx-auto bg-gray-50 rounded-lg p-6 mb-8 text-left">
                                <div class="flex items-center mb-3">
                                    <ClockIcon class="h-5 w-5 text-gray-500 mr-2" />
                                    <span class="font-semibold text-gray-700">Temps estimé : 10 minutes</span>
                                </div>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <p><strong>Documents nécessaires :</strong></p>
                                    <ul class="list-disc list-inside space-y-1 ml-4">
                                        <li>Pièce d'identité</li>
                                        <li>SIRET / n° TVA</li>
                                        <li>Coordonnées bancaires (IBAN)</li>
                                    </ul>
                                </div>
                            </div>

                            <button
                                @click="connectStripe"
                                :disabled="connectForm.processing"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                            >
                                Connecter mon compte Stripe
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stripe connecté -->
                <div v-else class="space-y-6">
                    <!-- Statut -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <CheckCircleIcon v-if="stripeAccount.is_fully_activated" class="h-8 w-8 text-green-500 mr-3" />
                                    <XCircleIcon v-else class="h-8 w-8 text-yellow-500 mr-3" />
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Compte Stripe</h3>
                                        <p v-if="stripeAccount.is_fully_activated" class="text-sm text-green-600">
                                            Connecté et activé
                                        </p>
                                        <p v-else class="text-sm text-yellow-600">
                                            Vérification en cours
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <button
                                        @click="openStripeDashboard"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Voir mon dashboard Stripe
                                    </button>
                                </div>
                            </div>

                            <div v-if="!stripeAccount.is_fully_activated" class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                <p class="text-sm text-yellow-800">
                                    Votre compte Stripe est en cours de vérification. Vous pourrez accepter des paiements une fois la vérification terminée.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div v-if="stats && stripeAccount.is_fully_activated" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <BanknotesIcon class="h-8 w-8 text-green-500 mr-3" />
                                    <div>
                                        <p class="text-sm text-gray-600">Revenus ce mois</p>
                                        <p class="text-2xl font-bold text-gray-900">
                                            {{ stats.this_month_revenue.toFixed(2) }}€
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <CalendarIcon class="h-8 w-8 text-blue-500 mr-3" />
                                    <div>
                                        <p class="text-sm text-gray-600">Séances ce mois</p>
                                        <p class="text-2xl font-bold text-gray-900">
                                            {{ stats.this_month_bookings }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <ClockIcon class="h-8 w-8 text-purple-500 mr-3" />
                                    <div>
                                        <p class="text-sm text-gray-600">À venir</p>
                                        <p class="text-2xl font-bold text-gray-900">
                                            {{ stats.upcoming_bookings }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <CheckCircleIcon class="h-8 w-8 text-green-500 mr-3" />
                                    <div>
                                        <p class="text-sm text-gray-600">Taux réalisation</p>
                                        <p class="text-2xl font-bold text-gray-900">
                                            {{ stats.completion_rate }}%
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation rapide -->
                    <div v-if="stripeAccount.is_fully_activated" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <a :href="route('dashboard.services.index')" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                            <div class="p-6 text-center">
                                <CreditCardIcon class="h-12 w-12 text-blue-600 mx-auto mb-3" />
                                <h4 class="font-semibold text-gray-900">Mes services</h4>
                                <p class="text-sm text-gray-600 mt-2">
                                    Gérer les types de séances et tarifs
                                </p>
                            </div>
                        </a>

                        <a :href="route('dashboard.availability.index')" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                            <div class="p-6 text-center">
                                <CalendarIcon class="h-12 w-12 text-green-600 mx-auto mb-3" />
                                <h4 class="font-semibold text-gray-900">Disponibilités</h4>
                                <p class="text-sm text-gray-600 mt-2">
                                    Définir mes créneaux horaires
                                </p>
                            </div>
                        </a>

                        <a :href="route('dashboard.bookings.index')" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                            <div class="p-6 text-center">
                                <ClockIcon class="h-12 w-12 text-purple-600 mx-auto mb-3" />
                                <h4 class="font-semibold text-gray-900">Réservations</h4>
                                <p class="text-sm text-gray-600 mt-2">
                                    Voir toutes mes réservations
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
