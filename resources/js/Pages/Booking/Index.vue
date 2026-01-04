<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ClockIcon, CreditCardIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    coach: Object,
    services: Array,
    canAcceptBookings: Boolean,
});

const bookService = (service) => {
    if (!props.canAcceptBookings) {
        alert('Les réservations ne sont pas encore disponibles pour ce coach.');
        return;
    }
    router.visit(route('coach.booking.create', { 
        coach_slug: props.coach.subdomain,
        service: service.id
    }));
};
</script>

<template>
    <Head :title="`Réserver avec ${coach.name}`" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Réserver une séance
                </h1>
                <p class="text-xl text-gray-600">
                    avec {{ coach.name }}
                </p>
            </div>

            <!-- Message si réservations non disponibles -->
            <div v-if="!canAcceptBookings" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800">
                            Les réservations en ligne ne sont pas encore activées pour ce coach. Contactez-le directement pour prendre rendez-vous.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Liste des services -->
            <div v-if="services.length === 0" class="bg-white rounded-xl shadow-lg p-12 text-center">
                <p class="text-gray-500 text-lg">
                    Aucun service disponible pour le moment.
                </p>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2">
                <div
                    v-for="service in services"
                    :key="service.id"
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1"
                >
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ service.name }}
                            </h3>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-blue-600">
                                    {{ service.price }}€
                                </div>
                            </div>
                        </div>

                        <p v-if="service.description" class="text-gray-600 mb-6">
                            {{ service.description }}
                        </p>

                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-6">
                            <div class="flex items-center">
                                <ClockIcon class="h-5 w-5 mr-1" />
                                <span>{{ service.duration_minutes }} minutes</span>
                            </div>
                            <div class="flex items-center">
                                <CreditCardIcon class="h-5 w-5 mr-1" />
                                <span>Paiement sécurisé</span>
                            </div>
                        </div>

                        <button
                            @click="bookService(service)"
                            :disabled="!canAcceptBookings"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Réserver ce créneau
                        </button>

                        <p class="text-xs text-gray-500 mt-3 text-center">
                            Réservation possible jusqu'à {{ service.min_advance_booking_hours }}h à l'avance
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer info -->
            <div class="mt-12 bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Comment ça marche ?</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li>✓ Choisissez le service qui vous convient</li>
                            <li>✓ Sélectionnez une date et un créneau horaire</li>
                            <li>✓ Remplissez vos informations</li>
                            <li>✓ Payez en ligne de manière sécurisée</li>
                            <li>✓ Recevez une confirmation immédiate par email</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">
                    🔒 Paiement sécurisé par Stripe • Confirmation immédiate • Annulation possible selon conditions
                </p>
            </div>
        </div>
    </div>
</template>
