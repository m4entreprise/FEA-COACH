<script setup>
import { Head } from '@inertiajs/vue3';
import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    booking: Object,
});
</script>

<template>
    <Head title="Paiement annulé" />

    <div class="min-h-screen bg-gradient-to-br from-rose-50 via-white to-orange-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-rose-100">
                <div class="bg-gradient-to-r from-rose-500 to-orange-500 p-8 text-center">
                    <ExclamationTriangleIcon class="h-20 w-20 text-white mx-auto mb-4" />
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Paiement annulé
                    </h1>
                    <p class="text-rose-100">
                        Votre séance n'a pas été confirmée
                    </p>
                </div>

                <div class="p-8 space-y-6">
                    <p class="text-gray-600 text-center">
                        Aucun montant n'a été prélevé. Si vous souhaitez toujours réserver cette prestation,
                        cliquez sur le bouton ci-dessous pour recommencer la procédure de paiement.
                    </p>

                    <div class="bg-gray-50 rounded-xl p-5 text-sm text-gray-600 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900">ID de réservation</span>
                            <span class="text-gray-700">#{{ booking?.id }}</span>
                        </div>
                        <div>
                            <p class="text-gray-500">
                                Besoin d'aide ? Répondez simplement à l'email reçu lors de la tentative de réservation
                                ou contactez directement le coach.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a
                            :href="route('booking.success', { booking: booking?.id })"
                            class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Voir la page de confirmation
                        </a>
                        <a
                            href="/"
                            class="inline-flex justify-center items-center px-6 py-3 bg-rose-500 border border-rose-600 text-base font-semibold text-white rounded-md hover:bg-rose-400"
                        >
                            Retour au site
                        </a>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 text-center border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Cette page vous a été fournie par la plateforme UNICOACH.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
