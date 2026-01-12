<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import WizardLayout from '@/Components/WizardLayout.vue';
import {
    Zap,
    Rocket,
    ListChecks,
    BadgeDollarSign,
    TrendingUp,
    Target,
    Sparkles,
} from 'lucide-vue-next';

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
    intermediate_cta_title: props.coach.intermediate_cta_title || '',
    intermediate_cta_subtitle: props.coach.intermediate_cta_subtitle || '',
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
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps" variant="beta">
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <Zap class="h-6 w-6 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Étape 4</p>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-50">Sections avancées</h2>
                        <p class="text-sm text-slate-400 mt-1">
                            Personnalisez les sections qui feront la différence.
                        </p>
                    </div>
                </div>
            </section>

            <!-- CTA Button -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <Rocket class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">CTA</p>
                        <h3 class="text-base font-semibold text-slate-50">Texte du bouton principal</h3>
                        <p class="text-xs text-slate-400 mt-1">Le texte affiché sur le bouton principal de votre site.</p>
                    </div>
                </div>
                <input
                    type="text"
                    v-model="form.cta_text"
                    class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Réserver ma séance découverte"
                />
            </section>

            <!-- Method Steps -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <ListChecks class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Méthode</p>
                        <h3 class="text-base font-semibold text-slate-50">Les 3 étapes de votre méthode</h3>
                        <p class="text-xs text-slate-400 mt-1">Ces textes apparaissent dans la section “Méthode” de votre site.</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <input
                        type="text"
                        v-model="form.method_title"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-sky-500 focus:ring-sky-500"
                        placeholder="Titre de la section"
                    />
                    <input
                        type="text"
                        v-model="form.method_subtitle"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-sky-500 focus:ring-sky-500"
                        placeholder="Sous-titre de la section"
                    />
                    
                    <div v-for="i in [1, 2, 3]" :key="i" class="rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs font-semibold text-slate-200 mb-2">Étape {{ i }}</p>
                        <input
                            type="text"
                            v-model="form[`method_step${i}_title`]"
                            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 mb-2 text-sm text-slate-50 placeholder-slate-500 focus:border-sky-500 focus:ring-sky-500"
                            :placeholder="`Titre étape ${i}`"
                        />
                        <textarea
                            v-model="form[`method_step${i}_description`]"
                            rows="2"
                            class="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-50 placeholder-slate-500 focus:border-sky-500 focus:ring-sky-500 resize-none"
                            :placeholder="`Description étape ${i}`"
                        ></textarea>
                    </div>
                </div>
            </section>

            <!-- Pricing Section -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <BadgeDollarSign class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Tarifs</p>
                        <h3 class="text-base font-semibold text-slate-50">Section Tarifs</h3>
                        <p class="text-xs text-slate-400 mt-1">Ne modifie que le titre et le sous-titre de la section.</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <input
                        type="text"
                        v-model="form.pricing_title"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="Mes formules de coaching"
                    />
                    <input
                        type="text"
                        v-model="form.pricing_subtitle"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-emerald-500 focus:ring-emerald-500"
                        placeholder="Choisissez la formule qui vous correspond"
                    />
                </div>
            </section>

            <!-- Intermediate CTA -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-2">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <Zap class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">CTA</p>
                        <h3 class="text-base font-semibold text-slate-50">CTA intermédiaire (après Méthode)</h3>
                        <p class="text-xs text-slate-400 mt-1">Section d'appel à l'action entre “Méthode” et “Tarifs”.</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <input
                        type="text"
                        v-model="form.intermediate_cta_title"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Prêt à transformer votre corps et votre vie ?"
                    />
                    <textarea
                        v-model="form.intermediate_cta_subtitle"
                        rows="2"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 resize-none"
                        placeholder="Ne restez pas seul face à vos objectifs..."
                    ></textarea>
                </div>
            </section>

            <!-- Transformations Section -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-teal-500 to-blue-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <TrendingUp class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Transformations</p>
                        <h3 class="text-base font-semibold text-slate-50">Section Transformations</h3>
                        <p class="text-xs text-slate-400 mt-1">Ne modifie que le titre et le sous-titre de la section.</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <input
                        type="text"
                        v-model="form.transformations_title"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-teal-500 focus:ring-teal-500"
                        placeholder="Leurs transformations"
                    />
                    <input
                        type="text"
                        v-model="form.transformations_subtitle"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-teal-500 focus:ring-teal-500"
                        placeholder="Des résultats réels de personnes comme vous"
                    />
                </div>
            </section>

            <!-- Final CTA -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <Target class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">CTA</p>
                        <h3 class="text-base font-semibold text-slate-50">Appel à l'action final</h3>
                        <p class="text-xs text-slate-400 mt-1">Section juste avant le footer.</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <input
                        type="text"
                        v-model="form.final_cta_title"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-amber-500 focus:ring-amber-500"
                        placeholder="Prêt à commencer votre transformation ?"
                    />
                    <textarea
                        v-model="form.final_cta_subtitle"
                        rows="2"
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-amber-500 focus:ring-amber-500 resize-none"
                        placeholder="Ne laissez pas vos objectifs être de simples rêves..."
                    ></textarea>
                </div>
            </section>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button
                    @click="skip"
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-900 px-6 py-3 text-xs font-semibold text-slate-200 hover:border-slate-500 hover:bg-slate-800 transition-colors disabled:opacity-50"
                >
                    Passer →
                </button>
                
                <button
                    @click="submit('demo')"
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-full border border-slate-700 bg-slate-800/80 px-6 py-3 text-xs font-semibold text-slate-100 hover:bg-slate-700 transition-colors disabled:opacity-50"
                >
                    <Sparkles class="h-4 w-4" />
                    Remplir avec la démo
                </button>
                
                <button
                    @click="submit('save')"
                    type="button"
                    :disabled="form.processing"
                    class="flex-1 inline-flex items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-3 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 transition-colors disabled:opacity-50"
                >
                    <span v-if="!form.processing">Continuer →</span>
                    <span v-else>Enregistrement...</span>
                </button>
            </div>
        </div>
    </WizardLayout>
</template>
