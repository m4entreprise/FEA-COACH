<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import WizardLayout from '@/Components/WizardLayout.vue';

const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    coach: Object,
});

const form = useForm({
    action: 'save',
    hero_title: props.coach.hero_title || '',
    hero_subtitle: props.coach.hero_subtitle || '',
    about_text: props.coach.about_text || '',
    method_text: props.coach.method_text || '',
    satisfaction_rate: props.coach.satisfaction_rate || 100,
    average_rating: props.coach.average_rating || 5.0,
});

const submit = (action) => {
    form.action = action;
    form.post(route('setup.step3.save'), {
        preserveScroll: true,
    });
};

const skip = () => {
    form.post(route('setup.skip', { step: props.currentStep }));
};
</script>

<template>
    <Head title="Étape 3 : Contenu Principal" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps">
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-8 md:p-12">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500/20 rounded-full mb-6">
                    <span class="text-5xl">✍️</span>
                </div>
                <h2 class="text-4xl font-bold text-white mb-4">
                    Racontez votre histoire
                </h2>
                <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                    Créez une connexion authentique avec vos futurs clients
                </p>
            </div>

            <!--Hero Section -->
            <div class="mb-8 bg-gradient-to-r from-purple-500/10 to-pink-500/10 border border-purple-400/30 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">🎯</span>
                    Section Hero (Première impression)
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-2">
                            Titre principal
                        </label>
                        <input
                            type="text"
                            v-model="form.hero_title"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                            placeholder="Transformez votre vie dès aujourd'hui"
                        />
                        <p class="mt-1 text-xs text-gray-400">{{ form.hero_title.length }}/255</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-2">
                            Sous-titre
                        </label>
                        <textarea
                            v-model="form.hero_subtitle"
                            rows="2"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none"
                            placeholder="Coaching personnalisé pour atteindre vos objectifs"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-400">{{ form.hero_subtitle.length }}/500</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="mb-8 bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border border-blue-400/30 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">📊</span>
                    Statistiques
                </h3>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-2">
                            Taux de satisfaction (%)
                        </label>
                        <div class="flex items-center space-x-3">
                            <input
                                type="number"
                                v-model.number="form.satisfaction_rate"
                                min="0"
                                max="100"
                                class="flex-1 px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <span class="text-2xl font-bold text-blue-300">{{ form.satisfaction_rate }}%</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-2">
                            Note moyenne (étoiles)
                        </label>
                        <div class="flex items-center space-x-3">
                            <input
                                type="number"
                                v-model.number="form.average_rating"
                                min="0"
                                max="5"
                                step="0.1"
                                class="flex-1 px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <span class="text-2xl font-bold text-yellow-300">{{ form.average_rating }}★</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- About -->
            <div class="mb-8 bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-400/30 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">👤</span>
                    À propos de vous
                </h3>
                
                <textarea
                    v-model="form.about_text"
                    rows="4"
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"
                    placeholder="Partagez votre parcours, vos certifications, votre passion pour le coaching..."
                ></textarea>
                <p class="mt-1 text-xs text-gray-400">{{ form.about_text.length }}/5000</p>
            </div>

            <!-- Method -->
            <div class="mb-8 bg-gradient-to-r from-orange-500/10 to-red-500/10 border border-orange-400/30 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">⚡</span>
                    Votre méthode de coaching
                </h3>
                
                <textarea
                    v-model="form.method_text"
                    rows="4"
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 resize-none"
                    placeholder="Décrivez votre approche unique du coaching..."
                ></textarea>
                <p class="mt-1 text-xs text-gray-400">{{ form.method_text.length }}/5000</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button
                    @click="skip"
                    type="button"
                    :disabled="form.processing"
                    class="px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 font-semibold rounded-xl transition disabled:opacity-50"
                >
                    Passer →
                </button>
                
                <button
                    @click="submit('demo')"
                    type="button"
                    :disabled="form.processing"
                    class="px-8 py-4 bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/50 text-blue-300 font-semibold rounded-xl transition disabled:opacity-50"
                >
                    ✨ Utiliser le contenu de démo
                </button>
                
                <button
                    @click="submit('save')"
                    type="button"
                    :disabled="form.processing"
                    class="flex-1 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-xl shadow-lg transition transform hover:scale-[1.02] disabled:opacity-50"
                >
                    <span v-if="!form.processing">Continuer →</span>
                    <span v-else>Enregistrement...</span>
                </button>
            </div>
        </div>
    </WizardLayout>
</template>
