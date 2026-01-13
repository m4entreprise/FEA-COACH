<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import WizardLayout from '@/Components/WizardLayout.vue';
import { FileText, Target, BarChart3, User as UserIcon, Zap, Sparkles, Star } from 'lucide-vue-next';
import SetupLivePreview from '@/Components/SetupLivePreview.vue';

const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    coach: Object,
});

const isStatsEnabled = props.coach?.show_stats !== false;

const form = useForm({
    action: 'save',
    hero_title: props.coach.hero_title || '',
    hero_subtitle: props.coach.hero_subtitle || '',
    about_text: props.coach.about_text || '',
    method_text: props.coach.method_text || '',
    satisfaction_rate: isStatsEnabled ? (props.coach.satisfaction_rate ?? 100) : null,
    average_rating: isStatsEnabled ? (props.coach.average_rating ?? 5.0) : null,
});

const previewPayload = () => ({
    hero_title: form.hero_title,
    hero_subtitle: form.hero_subtitle,
    about_text: form.about_text,
    method_text: form.method_text,
    satisfaction_rate: form.satisfaction_rate,
    average_rating: form.average_rating,
    site_layout: props.coach.site_layout,
});

const submit = (action) => {
    form.action = action;
    form.post(route('setup.step3.save'), {
    });
};

const skip = () => {
    form.post(route('setup.skip', { step: props.currentStep }));
};
</script>

<template>
    <Head title="Étape 3 : Contenu Principal" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps" variant="beta">
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <FileText class="h-6 w-6 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Étape 3</p>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-50">Contenu principal</h2>
                        <p class="text-sm text-slate-400 mt-1">
                            Créez une connexion authentique avec vos futurs clients.
                        </p>
                    </div>
                </div>
            </section>

            <!--Hero Section -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <Target class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Hero</p>
                        <h3 class="text-base font-semibold text-slate-50">Section Hero (première impression)</h3>
                        <p class="text-xs text-slate-400 mt-1">Optimisez le titre et le sous-titre affichés en haut de votre page.</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-200 mb-2">
                            Titre principal
                        </label>
                        <input
                            type="text"
                            v-model="form.hero_title"
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500"
                            placeholder="Transformez votre vie dès aujourd'hui"
                        />
                        <p class="mt-1 text-[11px] text-slate-500">{{ form.hero_title.length }}/255</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-200 mb-2">
                            Sous-titre
                        </label>
                        <textarea
                            v-model="form.hero_subtitle"
                            rows="2"
                            class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500 resize-none"
                            placeholder="Coaching personnalisé pour atteindre vos objectifs"
                        ></textarea>
                        <p class="mt-1 text-[11px] text-slate-500">{{ form.hero_subtitle.length }}/500</p>
                    </div>
                </div>
            </section>

            <!-- Stats -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <BarChart3 class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Stats</p>
                        <h3 class="text-base font-semibold text-slate-50">Statistiques</h3>
                        <p class="text-xs text-slate-400 mt-1">Ces valeurs apparaissent dans la section À propos sur votre site.</p>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                    <p class="text-xs font-semibold text-slate-200">Optionnel</p>
                    <p class="mt-1 text-xs text-slate-400">
                        Vous pouvez ne rien mettre pour le moment et ajouter ces statistiques plus tard, quand vous aurez des clients.
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-200 mb-2">
                            Taux de satisfaction (%)
                        </label>
                        <div class="flex items-center space-x-3">
                            <input
                                type="number"
                                v-model="form.satisfaction_rate"
                                min="0"
                                max="100"
                                class="flex-1 rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 focus:border-sky-500 focus:ring-sky-500"
                            />
                            <span class="text-sm font-semibold text-sky-200">{{ form.satisfaction_rate ?? '—' }}%</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-200 mb-2">
                            Note moyenne (étoiles)
                        </label>
                        <div class="flex items-center space-x-3">
                            <input
                                type="number"
                                v-model="form.average_rating"
                                min="0"
                                max="5"
                                step="0.1"
                                class="flex-1 rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 focus:border-sky-500 focus:ring-sky-500"
                            />
                            <span class="inline-flex items-center gap-1 text-sm font-semibold text-amber-200">
                                <Star class="h-4 w-4" />
                                {{ form.average_rating ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- About -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <UserIcon class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">À propos</p>
                        <h3 class="text-base font-semibold text-slate-50">À propos de vous</h3>
                        <p class="text-xs text-slate-400 mt-1">Présentez votre parcours et votre expertise.</p>
                    </div>
                </div>
                
                <textarea
                    v-model="form.about_text"
                    rows="4"
                    class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:border-emerald-500 focus:ring-emerald-500 resize-none"
                    placeholder="Partagez votre parcours, vos certifications, votre passion pour le coaching..."
                ></textarea>
                <p class="mt-1 text-[11px] text-slate-500">{{ form.about_text.length }}/5000</p>
            </section>

            <SetupLivePreview
                :payload="previewPayload()"
                title="Aperçu en direct"
                subtitle="Prévisualisez votre site en temps réel."
            />

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
                    Utiliser le contenu de démo
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
