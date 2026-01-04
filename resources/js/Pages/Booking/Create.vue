<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { UserIcon, PhoneIcon, EnvelopeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    coach: Object,
    service: Object,
    stripePublicKey: String,
});

const form = useForm({
    client_first_name: '',
    client_last_name: '',
    client_email: '',
    client_phone: '',
    client_notes: '',
});

const submitBooking = async () => {
    if (form.processing) return;

    form.post(route('coach.booking.store', { coach_slug: props.coach.subdomain, service: props.service.id }), {
        onSuccess: (response) => {
            if (response.props.checkout_url) {
                window.location.href = response.props.checkout_url;
            }
        },
        onError: (errors) => {
            console.error('Erreur:', errors);
            alert('Une erreur est survenue. Veuillez réessayer.');
        },
    });
};
</script>

<template>
    <Head :title="`Réserver ${service.name}`" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="/reserver" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    ← Retour aux services
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4 mb-2">
                    {{ service.name }}
                </h1>
                <p class="text-gray-600">avec {{ coach.name }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        Vos informations
                    </h2>

                    <!-- Service Info -->
                    <div class="mb-6 bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Service</p>
                                <p class="font-semibold text-gray-900">{{ service.name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Montant</p>
                                <p class="text-2xl font-bold text-blue-600">{{ service.price }}€</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitBooking" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Prénom *
                                </label>
                                <div class="relative">
                                    <UserIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                    <input
                                        v-model="form.client_first_name"
                                        type="text"
                                        required
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Jean"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom *
                                </label>
                                <div class="relative">
                                    <UserIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                    <input
                                        v-model="form.client_last_name"
                                        type="text"
                                        required
                                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Dupont"
                                    />
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email *
                            </label>
                            <div class="relative">
                                <EnvelopeIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="form.client_email"
                                    type="email"
                                    required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="jean.dupont@example.com"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone *
                            </label>
                            <div class="relative">
                                <PhoneIcon class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                <input
                                    v-model="form.client_phone"
                                    type="tel"
                                    required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="06 12 34 56 78"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Message (optionnel)
                            </label>
                            <textarea
                                v-model="form.client_notes"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Indiquez toute information utile pour votre coach..."
                            ></textarea>
                        </div>

                        <div class="border-t pt-6">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold text-gray-900">Total à payer</span>
                                <span class="text-3xl font-bold text-blue-600">{{ service.price }}€</span>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Traitement...' : 'Payer et confirmer' }}
                            </button>

                            <p class="text-xs text-gray-500 text-center mt-4">
                                🔒 Paiement sécurisé par Stripe
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
