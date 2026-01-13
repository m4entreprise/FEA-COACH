<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import WizardLayout from '@/Components/WizardLayout.vue';
import {
    Sparkles,
    Palette,
    FileText,
    Zap,
    Rocket,
    Image as ImageIcon,
    BadgeDollarSign,
    HelpCircle,
    Target,
    Info,
    Check,
} from 'lucide-vue-next';
import SetupLivePreview from '@/Components/SetupLivePreview.vue';

const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    coach: Object,
    user: Object,
});

const form = useForm({});

const previewPayload = () => ({
    color_primary: props.coach.color_primary,
    color_secondary: props.coach.color_secondary,
    site_layout: props.coach.site_layout,
    show_stats: props.coach.show_stats,
});

const complete = () => {
    form.post(route('setup.step5.save'));
};
</script>

<template>
    <Head title="Étape 5 : Finalisation" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps" variant="beta">
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <Sparkles class="h-6 w-6 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Étape 5</p>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-50">Finalisation</h2>
                        <p class="text-sm text-slate-400 mt-1">Votre site est prêt. Vous pourrez ajuster tous les détails depuis le dashboard.</p>
                    </div>
                </div>
            </section>

            <SetupLivePreview
                :payload="previewPayload()"
                title="Aperçu en direct"
                subtitle="Vérifiez l’affichage final de votre site avant de terminer."
            />

            <section class="grid md:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                            <Palette class="h-5 w-5 text-white" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Branding</p>
                            <h3 class="text-base font-semibold text-slate-50">Identité visuelle</h3>
                            <div class="mt-3 space-y-2 text-xs text-slate-300">
                                <p class="inline-flex items-center gap-2">
                                    <Check class="h-4 w-4 text-emerald-300" />
                                    Slug personnalisé
                                </p>
                                <p class="font-mono text-purple-300">{{ coach.slug }}.unicoach.app</p>
                                <div class="flex items-center gap-2 pt-1">
                                    <div class="w-7 h-7 rounded border border-slate-800" :style="{ backgroundColor: coach.color_primary }"></div>
                                    <div class="w-7 h-7 rounded border border-slate-800" :style="{ backgroundColor: coach.color_secondary }"></div>
                                    <span class="text-[11px] text-slate-500">Vos couleurs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg flex-shrink-0">
                            <FileText class="h-5 w-5 text-white" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Contenu</p>
                            <h3 class="text-base font-semibold text-slate-50">Contenu</h3>
                            <div class="mt-3 space-y-2 text-xs text-slate-300">
                                <p class="inline-flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Textes personnalisés</p>
                                <p class="inline-flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Statistiques configurées</p>
                                <p class="inline-flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Sections optimisées</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-500 flex items-center justify-center shadow-lg flex-shrink-0">
                            <Zap class="h-5 w-5 text-white" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Fonctionnalités</p>
                            <h3 class="text-base font-semibold text-slate-50">Fonctionnalités</h3>
                            <div class="mt-3 space-y-2 text-xs text-slate-300">
                                <p class="inline-flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Dashboard complet</p>
                                <p class="inline-flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Gestion des plans</p>
                                <p class="inline-flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Galerie transformations</p>
                                <p class="inline-flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />FAQ personnalisable</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-lg flex-shrink-0">
                            <Rocket class="h-5 w-5 text-white" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs uppercase tracking-wide text-slate-500">À faire</p>
                            <h3 class="text-base font-semibold text-slate-50">Prochaines étapes</h3>
                            <div class="mt-3 space-y-2 text-xs text-slate-300">
                                <p class="inline-flex items-center gap-2"><ImageIcon class="h-4 w-4 text-slate-200" />Ajouter des transformations</p>
                                <p class="inline-flex items-center gap-2"><BadgeDollarSign class="h-4 w-4 text-slate-200" />Créer vos plans tarifaires</p>
                                <p class="inline-flex items-center gap-2"><HelpCircle class="h-4 w-4 text-slate-200" />Remplir votre FAQ</p>
                                <p class="inline-flex items-center gap-2"><Target class="h-4 w-4 text-slate-200" />Partager votre site</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-amber-500/30 bg-amber-950/20 p-6 shadow-xl">
                <div class="flex items-start gap-3">
                    <Info class="h-5 w-5 text-amber-300 flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="text-sm font-semibold text-amber-100 mb-1">Vous pourrez tout modifier plus tard</p>
                        <p class="text-xs text-amber-100/80 mb-4">
                            Ce wizard vous a permis de créer une base solide. Depuis votre dashboard, vous pourrez à tout moment :
                        </p>
                        <ul class="space-y-2 text-xs text-amber-100/80">
                            <li class="flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Modifier vos couleurs et votre branding</li>
                            <li class="flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Changer vos images et votre logo</li>
                            <li class="flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Personnaliser tous vos textes</li>
                            <li class="flex items-center gap-2"><Check class="h-4 w-4 text-emerald-300" />Ajouter vos plans, transformations et FAQ</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-base md:text-lg font-semibold text-slate-50">Prêt à lancer votre activité de coaching ?</h3>
                        <p class="text-xs text-slate-400 mt-1">Accédez à votre dashboard pour gérer votre site.</p>
                    </div>

                    <button
                        @click="complete"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-3 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="!form.processing" class="inline-flex items-center gap-2">
                            <Rocket class="h-4 w-4" />
                            Accéder à mon dashboard
                        </span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Finalisation...
                        </span>
                    </button>
                </div>
            </section>
        </div>
    </WizardLayout>
</template>
