<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import WizardLayout from '@/Components/WizardLayout.vue';

const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    coach: Object,
    user: Object,
});

const form = useForm({});

const complete = () => {
    form.post(route('setup.step5.save'));
};
</script>

<template>
    <Head title="Étape 5 : Finalisation" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps">
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-8 md:p-12">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-full mb-6 animate-pulse">
                    <span class="text-6xl">🎉</span>
                </div>
                <h2 class="text-5xl font-bold text-white mb-4">
                    Vous y êtes presque !
                </h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Votre site de coaching est prêt à impressionner vos clients
                </p>
            </div>

            <!-- Summary Cards -->
            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <!-- Brand Card -->
                <div class="bg-gradient-to-br from-purple-500/20 to-pink-500/20 border-2 border-purple-400/40 rounded-2xl p-6 transform hover:scale-105 transition">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="text-3xl">🎨</span>
                        <h3 class="text-xl font-bold text-white">Identité visuelle</h3>
                    </div>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p>✅ Slug personnalisé</p>
                        <p class="font-mono text-purple-300">{{ coach.slug }}.unicoach.app</p>
                        <div class="flex items-center space-x-2 mt-3">
                            <div class="w-8 h-8 rounded" :style="{ backgroundColor: coach.primary_color }"></div>
                            <div class="w-8 h-8 rounded" :style="{ backgroundColor: coach.secondary_color }"></div>
                            <span class="text-xs text-gray-400">Vos couleurs</span>
                        </div>
                    </div>
                </div>

                <!-- Content Card -->
                <div class="bg-gradient-to-br from-blue-500/20 to-cyan-500/20 border-2 border-blue-400/40 rounded-2xl p-6 transform hover:scale-105 transition">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="text-3xl">✍️</span>
                        <h3 class="text-xl font-bold text-white">Contenu</h3>
                    </div>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p>✅ Textes personnalisés</p>
                        <p>✅ Statistiques configurées</p>
                        <p>✅ Sections optimisées</p>
                    </div>
                </div>

                <!-- Features Card -->
                <div class="bg-gradient-to-br from-green-500/20 to-emerald-500/20 border-2 border-green-400/40 rounded-2xl p-6 transform hover:scale-105 transition">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="text-3xl">⚡</span>
                        <h3 class="text-xl font-bold text-white">Fonctionnalités</h3>
                    </div>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p>✅ Dashboard complet</p>
                        <p>✅ Gestion des plans</p>
                        <p>✅ Galerie transformations</p>
                        <p>✅ FAQ personnalisable</p>
                    </div>
                </div>

                <!-- Next Steps Card -->
                <div class="bg-gradient-to-br from-orange-500/20 to-red-500/20 border-2 border-orange-400/40 rounded-2xl p-6 transform hover:scale-105 transition">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="text-3xl">🚀</span>
                        <h3 class="text-xl font-bold text-white">Prochaines étapes</h3>
                    </div>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p>📸 Ajouter des transformations</p>
                        <p>💰 Créer vos plans tarifaires</p>
                        <p>❓ Remplir votre FAQ</p>
                        <p>🎯 Partager votre site !</p>
                    </div>
                </div>
            </div>

            <!-- Important Info -->
            <div class="mb-12 p-8 bg-gradient-to-r from-yellow-500/10 to-orange-500/10 border-2 border-yellow-400/30 rounded-2xl">
                <div class="flex items-start space-x-4">
                    <div class="text-5xl">💡</div>
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-3">
                            Vous pourrez tout modifier plus tard !
                        </h3>
                        <p class="text-gray-300 mb-4">
                            Ce wizard vous a permis de créer une base solide. Depuis votre dashboard, vous pourrez à tout moment :
                        </p>
                        <ul class="space-y-2 text-gray-300">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Modifier vos couleurs et votre branding
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Changer vos images et votre logo
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Personnaliser tous vos textes
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Ajouter vos plans, transformations et FAQ
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Final CTA -->
            <div class="text-center">
                <h3 class="text-3xl font-bold text-white mb-6">
                    Prêt à lancer votre activité de coaching ?
                </h3>
                
                <button
                    @click="complete"
                    :disabled="form.processing"
                    class="inline-flex items-center px-12 py-6 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-xl font-bold rounded-2xl shadow-2xl transition transform hover:scale-110 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                    <span v-if="!form.processing">
                        <span class="mr-3">🚀</span>
                        Accéder à mon dashboard
                        <span class="ml-3">→</span>
                    </span>
                    <span v-else class="flex items-center">
                        <svg class="animate-spin h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Finalisation...
                    </span>
                </button>

                <p class="mt-6 text-gray-400">
                    Cliquez pour accéder à votre dashboard et commencer à gérer votre site
                </p>
            </div>
        </div>
    </WizardLayout>
</template>
