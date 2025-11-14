<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const form = useForm({
    first_name: props.user?.first_name || '',
    last_name: props.user?.last_name || '',
    email: props.user?.email || '',
    vat_number: props.user?.vat_number || '',
    legal_address: props.user?.legal_address || '',
});

const submit = () => {
    form.post(route('onboarding.step2.store'));
};
</script>

<template>
    <Head title="Informations personnelles - Étape 2/3" />
    
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
                    <span class="text-purple-400 font-medium">Étape 2/3</span>
                    <span>Vos informations</span>
                </div>
                <div class="w-full bg-white/10 rounded-full h-2">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-2 rounded-full" style="width: 66.66%"></div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl p-8">
                <h1 class="text-3xl font-bold text-white mb-2 text-center">
                    Vos informations
                </h1>
                <p class="text-gray-300 mb-8 text-center">
                    Ces informations nous permettront de créer votre compte
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Prénom & Nom -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-200 mb-2">
                                Prénom <span class="text-red-400">*</span>
                            </label>
                            <input
                                id="first_name"
                                type="text"
                                v-model="form.first_name"
                                required
                                class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="Jean"
                            />
                            <p v-if="form.errors.first_name" class="mt-2 text-sm text-red-400">
                                {{ form.errors.first_name }}
                            </p>
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-200 mb-2">
                                Nom <span class="text-red-400">*</span>
                            </label>
                            <input
                                id="last_name"
                                type="text"
                                v-model="form.last_name"
                                required
                                class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="Dupont"
                            />
                            <p v-if="form.errors.last_name" class="mt-2 text-sm text-red-400">
                                {{ form.errors.last_name }}
                            </p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-200 mb-2">
                            Adresse email <span class="text-red-400">*</span>
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="jean.dupont@example.com"
                        />
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-400">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Numéro TVA (optionnel) -->
                    <div>
                        <label for="vat_number" class="block text-sm font-medium text-gray-200 mb-2">
                            Numéro de TVA <span class="text-gray-400 text-xs">(optionnel)</span>
                        </label>
                        <input
                            id="vat_number"
                            type="text"
                            v-model="form.vat_number"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="FR12345678901"
                        />
                        <p class="mt-1 text-xs text-gray-400">
                            Si vous êtes assujetti à la TVA
                        </p>
                        <p v-if="form.errors.vat_number" class="mt-2 text-sm text-red-400">
                            {{ form.errors.vat_number }}
                        </p>
                    </div>

                    <!-- Adresse légale -->
                    <div>
                        <label for="legal_address" class="block text-sm font-medium text-gray-200 mb-2">
                            Adresse légale <span class="text-red-400">*</span>
                        </label>
                        <textarea
                            id="legal_address"
                            v-model="form.legal_address"
                            required
                            rows="3"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition resize-none"
                            placeholder="123 Rue de la Paix&#10;75001 Paris&#10;France"
                        ></textarea>
                        <p v-if="form.errors.legal_address" class="mt-2 text-sm text-red-400">
                            {{ form.errors.legal_address }}
                        </p>
                    </div>

                    <!-- Info Box -->
                    <div class="p-4 bg-blue-500/10 border border-blue-400/30 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-blue-300">
                                Ces informations seront utilisées pour votre facturation et la gestion de votre compte.
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex gap-4 pt-4">
                        <a
                            :href="route('onboarding.step1')"
                            class="flex-1 px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-semibold rounded-xl transition text-center"
                        >
                            ← Retour
                        </a>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-xl shadow-lg transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <span v-if="!form.processing">Continuer</span>
                            <span v-else class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Chargement...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
