<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    coach: Object,
    defaultLegalTerms: String,
    user: Object,
});

const showPreview = ref(false);

const form = useForm({
    vat_number: props.user?.vat_number || '',
    legal_terms: props.coach.legal_terms || props.defaultLegalTerms,
});

const submitForm = () => {
    form.post(route('dashboard.legal.update'), {
        preserveScroll: true,
    });
};

const useDefaultTemplate = () => {
    form.legal_terms = props.defaultLegalTerms;
};

const copyToClipboard = () => {
    navigator.clipboard.writeText(form.legal_terms);
    alert('Texte copié dans le presse-papier !');
};
</script>

<template>
    <Head title="Mentions Légales" />

    <AuthenticatedLayout>
        <div class="py-12 bg-gradient-to-br from-slate-50 via-purple-50 to-slate-50 dark:from-slate-900 dark:via-purple-900/20 dark:to-slate-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- En-tête -->
                <div class="mb-8 rounded-2xl bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 shadow-xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl p-8">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-4 shadow-lg">
                            <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
                                📜 Mentions Légales & CGV
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                Personnalisez vos conditions générales de vente et mentions légales
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="mb-6 rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl shadow-lg p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-3 shadow-md">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100 mb-3">💡 Informations importantes</h3>
                            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 dark:text-blue-400">•</span>
                                    <span>Les informations personnelles (nom, email, adresse, TVA) sont pré-remplies automatiquement</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 dark:text-blue-400">•</span>
                                    <span>Personnalisez les sections entre crochets selon votre activité</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 dark:text-blue-400">•</span>
                                    <span>Ce texte sera accessible depuis votre site public (à intégrer)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-blue-500 dark:text-blue-400">•</span>
                                    <span>Pensez à consulter un professionnel du droit pour validation</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Formulaire -->
                <div class="bg-gradient-to-br from-white to-purple-50/30 dark:from-gray-800 dark:to-purple-900/10 rounded-2xl shadow-xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl">
                    <form @submit.prevent="submitForm">
                        <div class="p-6 space-y-6">
                            <!-- Actions rapides -->
                            <div class="flex flex-wrap gap-3">
                                <button
                                    type="button"
                                    @click="useDefaultTemplate"
                                    class="px-5 py-2.5 text-sm bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl shadow-lg hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 flex items-center gap-2 font-semibold"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Recharger le modèle
                                </button>
                                <button
                                    type="button"
                                    @click="copyToClipboard"
                                    class="px-5 py-2.5 text-sm bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl shadow-lg hover:from-gray-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-200 flex items-center gap-2 font-semibold"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Copier le texte
                                </button>
                                <button
                                    type="button"
                                    @click="showPreview = !showPreview"
                                    class="px-5 py-2.5 text-sm bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl shadow-lg hover:from-blue-700 hover:to-cyan-700 transform hover:scale-105 transition-all duration-200 flex items-center gap-2 font-semibold"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ showPreview ? '👁️ Masquer' : '👁️ Voir' }} l'aperçu
                                </button>
                            </div>

                            <!-- Numéro de TVA -->
                            <div class="rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl shadow-lg p-6">
                                <div class="flex items-start gap-4 mb-4">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 shadow-md">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-purple-900 dark:text-purple-100 mb-2">💼 Numéro de TVA</h3>
                                        <p class="text-sm text-purple-800 dark:text-purple-200 mb-4">
                                            Ce numéro sera affiché dans le footer de votre site public et dans vos mentions légales.
                                        </p>
                                        <div>
                                            <label class="block text-sm font-semibold text-purple-900 dark:text-purple-100 mb-2">
                                                N° TVA (si applicable)
                                            </label>
                                            <input
                                                v-model="form.vat_number"
                                                type="text"
                                                class="w-full md:w-1/2 px-4 py-2.5 rounded-xl border border-purple-300 dark:border-purple-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent shadow-md transition-all duration-200"
                                                placeholder="BE0123456789"
                                            />
                                            <InputError :message="form.errors.vat_number" class="mt-2" />
                                            <p class="mt-2 text-xs text-purple-700 dark:text-purple-300 flex items-start gap-1">
                                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Laissez vide si vous n'êtes pas assujetti à la TVA.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aperçu -->
                            <div v-if="showPreview" class="rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl shadow-lg p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-2.5 shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100">👁️ Aperçu</h3>
                                </div>
                                <div class="prose dark:prose-invert max-w-none text-sm whitespace-pre-line text-gray-900 dark:text-gray-100 bg-white/50 dark:bg-gray-800/50 rounded-xl p-4">
                                    {{ form.legal_terms }}
                                </div>
                            </div>

                            <!-- Éditeur -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                    📝 Texte des mentions légales et CGV
                                </label>
                                <textarea
                                    v-model="form.legal_terms"
                                    rows="25"
                                    class="w-full px-4 py-3 rounded-xl border border-purple-300 dark:border-purple-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent font-mono text-sm shadow-md backdrop-blur-sm transition-all duration-200"
                                    placeholder="Saisissez vos mentions légales..."
                                ></textarea>
                                <InputError :message="form.errors.legal_terms" class="mt-1" />
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-xs font-semibold text-purple-700 dark:text-purple-300">
                                        📊 {{ form.legal_terms?.length || 0 }} / 50 000 caractères
                                    </span>
                                </div>
                            </div>

                            <!-- Conseils -->
                            <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200/50 dark:border-amber-500/30 backdrop-blur-xl shadow-lg p-6">
                                <div class="flex items-start gap-3 mb-4">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-2.5 shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-bold text-amber-900 dark:text-amber-100 mb-3">✏️ Sections à personnaliser</h4>
                                        <ul class="text-sm text-amber-800 dark:text-amber-200 space-y-2">
                                            <li class="flex items-start gap-2">
                                                <span class="text-amber-500 dark:text-amber-400">•</span>
                                                <span><strong class="font-bold">Lieu des séances</strong> : précisez studio, domicile, salle ou en ligne</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="text-amber-500 dark:text-amber-400">•</span>
                                                <span><strong class="font-bold">Modes de paiement</strong> : listez vos options acceptées</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="text-amber-500 dark:text-amber-400">•</span>
                                                <span><strong class="font-bold">Délais d'annulation</strong> : ajustez selon votre politique (24h, 48h...)</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="text-amber-500 dark:text-amber-400">•</span>
                                                <span><strong class="font-bold">Durée de validité</strong> : définissez la durée des packs</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <span class="text-amber-500 dark:text-amber-400">•</span>
                                                <span><strong class="font-bold">Assurance</strong> : indiquez votre assureur et numéro de police</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="bg-gradient-to-r from-purple-50/50 to-pink-50/50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-5 border-t border-purple-200/30 dark:border-purple-500/20 flex justify-end gap-3">
                            <button
                                type="button"
                                @click="form.reset()"
                                class="px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 shadow-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transform hover:scale-105 transition-all duration-200"
                            >
                                Réinitialiser
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 shadow-lg hover:shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200 flex items-center gap-2"
                            >
                                <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Enregistrement...' : '✨ Enregistrer les mentions légales' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Note de bas de page -->
                <div class="mt-6 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200/50 dark:border-amber-500/30 backdrop-blur-xl shadow-lg p-6">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-2.5 shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-amber-800 dark:text-amber-200">
                            <strong class="font-bold">⚠️ Note importante :</strong> Ces mentions légales sont fournies à titre indicatif. Il est recommandé de les faire valider par un professionnel du droit pour vous assurer qu'elles sont conformes à votre situation et à la législation en vigueur.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
