<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    is_fea_graduate: null,
});

const submit = () => {
    form.post(route('onboarding.step1.store'));
};
</script>

<template>
    <Head title="Bienvenue - Étape 1/3" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center px-4">
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
                    <span class="text-purple-400 font-medium">Étape 1/3</span>
                    <span>Type de compte</span>
                </div>
                <div class="w-full bg-white/10 rounded-full h-2">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-2 rounded-full" style="width: 33.33%"></div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl p-8">
                <h1 class="text-3xl font-bold text-white mb-4 text-center">
                    Êtes-vous diplômé de Fitness Education Academy ?
                </h1>
                <p class="text-gray-300 mb-8 text-center">
                    Cette information nous permettra de vous proposer l'offre la plus adaptée
                </p>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Option: Diplômé FEA -->
                    <div 
                        @click="form.is_fea_graduate = true"
                        class="relative cursor-pointer group"
                    >
                        <input 
                            type="radio" 
                            name="is_fea_graduate" 
                            :value="true" 
                            v-model="form.is_fea_graduate"
                            class="sr-only"
                        />
                        <div 
                            class="p-6 rounded-xl border-2 transition-all duration-200"
                            :class="form.is_fea_graduate === true 
                                ? 'bg-gradient-to-br from-purple-600/30 to-pink-600/30 border-purple-400' 
                                : 'bg-white/5 border-white/10 hover:border-white/30'"
                        >
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div 
                                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="form.is_fea_graduate === true 
                                            ? 'bg-purple-500 border-purple-500' 
                                            : 'bg-transparent border-white/30'"
                                    >
                                        <svg v-if="form.is_fea_graduate === true" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-white mb-2">
                                        ✅ Oui, je suis diplômé FEA
                                    </h3>
                                    <p class="text-gray-300 mb-3">
                                        Bénéficiez de <span class="text-purple-400 font-semibold">1 mois offert</span> en utilisant votre code promotionnel
                                    </p>
                                    <div class="inline-flex items-center space-x-2 px-3 py-1 bg-purple-500/20 border border-purple-400/30 rounded-full text-sm text-purple-300">
                                        <span>🎁</span>
                                        <span>Offre exclusive FEA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Option: Non diplômé -->
                    <div 
                        @click="form.is_fea_graduate = false"
                        class="relative cursor-pointer group"
                    >
                        <input 
                            type="radio" 
                            name="is_fea_graduate" 
                            :value="false" 
                            v-model="form.is_fea_graduate"
                            class="sr-only"
                        />
                        <div 
                            class="p-6 rounded-xl border-2 transition-all duration-200"
                            :class="form.is_fea_graduate === false 
                                ? 'bg-gradient-to-br from-blue-600/30 to-purple-600/30 border-blue-400' 
                                : 'bg-white/5 border-white/10 hover:border-white/30'"
                        >
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div 
                                        class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                        :class="form.is_fea_graduate === false 
                                            ? 'bg-blue-500 border-blue-500' 
                                            : 'bg-transparent border-white/30'"
                                    >
                                        <svg v-if="form.is_fea_graduate === false" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-white mb-2">
                                        Non, je débute
                                    </h3>
                                    <p class="text-gray-300">
                                        Accédez à UNICOACH et créez votre site professionnel de coaching
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <p v-if="form.errors.is_fea_graduate" class="text-sm text-red-400 text-center">
                        {{ form.errors.is_fea_graduate }}
                    </p>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.is_fea_graduate === null || form.processing"
                        class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-xl shadow-lg transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none mt-6"
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
                </form>
            </div>
        </div>
    </div>
</template>
