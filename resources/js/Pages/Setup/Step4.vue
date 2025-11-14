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
    cta_text: props.coach.cta_text || '',
    method_title: props.coach.method_title || '',
    method_subtitle: props.coach.method_subtitle || '',
    method_step1_title: props.coach.method_step1_title || '',
    method_step1_description: props.coach.method_step1_description || '',
    method_step2_title: props.coach.method_step2_title || '',
    method_step2_description: props.coach.method_step2_description || '',
    method_step3_title: props.coach.method_step3_title || '',
    method_step3_description: props.coach.method_step3_description || '',
    pricing_title: props.coach.pricing_title || '',
    pricing_subtitle: props.coach.pricing_subtitle || '',
    transformations_title: props.coach.transformations_title || '',
    transformations_subtitle: props.coach.transformations_subtitle || '',
    final_cta_title: props.coach.final_cta_title || '',
    final_cta_subtitle: props.coach.final_cta_subtitle || '',
});

const submit = (action) => {
    form.action = action;
    form.post(route('setup.step4.save'));
};

const skip = () => {
    form.post(route('setup.skip', { step: props.currentStep }));
};
</script>

<template>
    <Head title="Étape 4 : Sections Avancées" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps">
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-8 md:p-12 max-h-[80vh] overflow-y-auto">
            <!-- Header -->
            <div class="text-center mb-12 sticky top-0 bg-slate-900/90 backdrop-blur-xl pb-6 z-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-500/20 rounded-full mb-6">
                    <span class="text-5xl">⚡</span>
                </div>
                <h2 class="text-4xl font-bold text-white mb-4">
                    Peaufinez les détails
                </h2>
                <p class="text-lg text-gray-300">
                    Personnalisez les sections qui feront la différence
                </p>
            </div>

            <!-- CTA Button -->
            <div class="mb-6 bg-gradient-to-r from-purple-500/10 to-pink-500/10 border border-purple-400/30 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-3 flex items-center">
                    <span class="text-xl mr-2">🚀</span>
                    Texte du bouton principal
                </h3>
                <input
                    type="text"
                    v-model="form.cta_text"
                    class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="Réserver ma séance découverte"
                />
            </div>

            <!-- Method Steps -->
            <div class="mb-6 bg-gradient-to-r from-blue-500/10 to-cyan-500/10 border border-blue-400/30 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">📋 Les 3 étapes de votre méthode</h3>
                
                <div class="space-y-4">
                    <input
                        type="text"
                        v-model="form.method_title"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm"
                        placeholder="Titre de la section"
                    />
                    
                    <div v-for="i in [1, 2, 3]" :key="i" class="bg-white/5 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-300 mb-2">{{ i }}️⃣ Étape {{ i }}</p>
                        <input
                            type="text"
                            v-model="form[`method_step${i}_title`]"
                            class="w-full px-3 py-2 mb-2 bg-white/5 border border-white/10 rounded text-white text-sm"
                            :placeholder="`Titre étape ${i}`"
                        />
                        <textarea
                            v-model="form[`method_step${i}_description`]"
                            rows="2"
                            class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded text-white text-sm resize-none"
                            :placeholder="`Description étape ${i}`"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="mb-6 bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-400/30 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-3 flex items-center">
                    <span class="text-xl mr-2">💰</span>
                    Section Tarifs
                </h3>
                <div class="space-y-3">
                    <input
                        type="text"
                        v-model="form.pricing_title"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm"
                        placeholder="Mes formules de coaching"
                    />
                    <input
                        type="text"
                        v-model="form.pricing_subtitle"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm"
                        placeholder="Choisissez la formule qui vous correspond"
                    />
                </div>
            </div>

            <!-- Transformations Section -->
            <div class="mb-6 bg-gradient-to-r from-teal-500/10 to-blue-500/10 border border-teal-400/30 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-3 flex items-center">
                    <span class="text-xl mr-2">📈</span>
                    Section Transformations
                </h3>
                <div class="space-y-3">
                    <input
                        type="text"
                        v-model="form.transformations_title"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm"
                        placeholder="Leurs transformations"
                    />
                    <input
                        type="text"
                        v-model="form.transformations_subtitle"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm"
                        placeholder="Des résultats réels de personnes comme vous"
                    />
                </div>
            </div>

            <!-- Final CTA -->
            <div class="mb-8 bg-gradient-to-r from-orange-500/10 to-red-500/10 border border-orange-400/30 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-3 flex items-center">
                    <span class="text-xl mr-2">🎯</span>
                    Appel à l'action final
                </h3>
                <div class="space-y-3">
                    <input
                        type="text"
                        v-model="form.final_cta_title"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm"
                        placeholder="Prêt à commencer votre transformation ?"
                    />
                    <textarea
                        v-model="form.final_cta_subtitle"
                        rows="2"
                        class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white text-sm resize-none"
                        placeholder="Ne laissez pas vos objectifs être de simples rêves..."
                    ></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 sticky bottom-0 bg-slate-900/90 backdrop-blur-xl pt-6">
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
                    ✨ Remplir avec la démo
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
