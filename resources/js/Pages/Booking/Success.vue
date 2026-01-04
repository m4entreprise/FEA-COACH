<script setup>
import { Head } from '@inertiajs/vue3';
import { CheckCircleIcon, CalendarIcon, ClockIcon, UserIcon, CreditCardIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const props = defineProps({
    booking: Object,
});

const isScheduled = computed(() => !!(props.booking?.booking_date && props.booking?.start_time));
const hasClientPortal = computed(() => Boolean(props.booking?.client_share_link));
const isFirstBooking = computed(() => Boolean(props.booking?.is_first_booking));
</script>

<template>
    <Head title="Réservation confirmée" />

    <div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-blue-500 p-8 text-center">
                    <CheckCircleIcon class="h-20 w-20 text-white mx-auto mb-4" />
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Paiement confirmé !
                    </h1>
                    <p class="text-green-100">
                        Votre coach vous recontactera très rapidement pour fixer votre séance
                    </p>
                </div>

                <div class="p-8">
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Détails de votre réservation</h2>
                        
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <CalendarIcon class="h-6 w-6 text-gray-400 mr-3 flex-shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="text-base font-medium text-gray-900">
                                        {{ booking.booking_date || 'À planifier' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <ClockIcon class="h-6 w-6 text-gray-400 mr-3 flex-shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">Horaire</p>
                                    <p class="text-base font-medium text-gray-900">
                                        <span v-if="isScheduled">
                                            {{ booking.start_time }} - {{ booking.end_time }}
                                        </span>
                                        <span v-else>À planifier</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <UserIcon class="h-6 w-6 text-gray-400 mr-3 flex-shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">Service</p>
                                    <p class="text-base font-medium text-gray-900">{{ booking.service_name }}</p>
                                    <p class="text-sm text-gray-600">avec {{ booking.coach_name }}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <CreditCardIcon class="h-6 w-6 text-gray-400 mr-3 flex-shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-sm text-gray-600">Montant payé</p>
                                    <p class="text-base font-medium text-gray-900">{{ booking.amount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    Un email de confirmation a été envoyé à <strong>{{ booking.client_email }}</strong> avec tous les détails de votre réservation.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 text-center">
                            📧 Vous recevrez un rappel 24h avant votre séance une fois le créneau défini.
                        </p>

                        <div
                            v-if="hasClientPortal"
                            class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 space-y-3"
                        >
                            <div>
                                <p class="font-semibold">Espace client disponible</p>
                                <p v-if="isFirstBooking">
                                    Conservez votre code d'accès : <strong>{{ booking.client_share_code }}</strong>
                                </p>
                                <p v-else>
                                    Connectez-vous depuis votre espace habituel pour retrouver vos documents et programmes.
                                </p>
                            </div>
                            <div>
                                <a
                                    :href="booking.client_share_link"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex items-center justify-center gap-2 rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-lg hover:bg-blue-700 transition"
                                >
                                    Voir mon dashboard
                                </a>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button
                                onclick="window.print()"
                                class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                📥 Télécharger la confirmation
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 text-center border-t border-gray-200">
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>
                            Des questions ? Contactez directement <strong>{{ booking.coach_name }}</strong>.
                        </p>
                        <p v-if="booking.coach_contact_email">
                            <span class="font-medium">Email :</span>
                            <a :href="`mailto:${booking.coach_contact_email}`" class="text-blue-600 underline">
                                {{ booking.coach_contact_email }}
                            </a>
                        </p>
                        <p v-if="booking.coach_contact_phone">
                            <span class="font-medium">Téléphone :</span>
                            <a :href="`tel:${booking.coach_contact_phone}`" class="text-blue-600 underline">
                                {{ booking.coach_contact_phone }}
                            </a>
                        </p>
                        <p v-if="!booking.coach_contact_email && !booking.coach_contact_phone">
                            Vous trouverez ses coordonnées dans l'email de confirmation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    .no-print {
        display: none;
    }
}
</style>
