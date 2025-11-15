<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    coach: Object,
    defaultLegalTerms: String,
});

const showPreview = ref(false);

const form = useForm({
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
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- En-tête -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                        📜 Mentions Légales & CGV
                    </h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Personnalisez vos conditions générales de vente et mentions légales
                    </p>
                </div>

                <!-- Info Card -->
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex gap-3">
                        <div class="text-blue-600 dark:text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">Informations importantes</h3>
                            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                <li>• Les informations personnelles (nom, email, adresse, TVA) sont pré-remplies automatiquement</li>
                                <li>• Personnalisez les sections entre crochets selon votre activité</li>
                                <li>• Ce texte sera accessible depuis votre site public (à intégrer)</li>
                                <li>• Pensez à consulter un professionnel du droit pour validation</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Formulaire -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                    <form @submit.prevent="submitForm">
                        <div class="p-6 space-y-6">
                            <!-- Actions rapides -->
                            <div class="flex flex-wrap gap-3">
                                <button
                                    type="button"
                                    @click="useDefaultTemplate"
                                    class="px-4 py-2 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Recharger le modèle
                                </button>
                                <button
                                    type="button"
                                    @click="copyToClipboard"
                                    class="px-4 py-2 text-sm bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Copier le texte
                                </button>
                                <button
                                    type="button"
                                    @click="showPreview = !showPreview"
                                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ showPreview ? 'Masquer' : 'Voir' }} l'aperçu
                                </button>
                            </div>

                            <!-- Aperçu -->
                            <div v-if="showPreview" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aperçu</h3>
                                <div class="prose dark:prose-invert max-w-none text-sm whitespace-pre-line text-gray-900 dark:text-gray-100">
                                    {{ form.legal_terms }}
                                </div>
                            </div>

                            <!-- Éditeur -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Texte des mentions légales et CGV
                                </label>
                                <textarea
                                    v-model="form.legal_terms"
                                    rows="25"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent font-mono text-sm"
                                    placeholder="Saisissez vos mentions légales..."
                                ></textarea>
                                <InputError :message="form.errors.legal_terms" class="mt-1" />
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ form.legal_terms?.length || 0 }} / 50 000 caractères
                                </div>
                            </div>

                            <!-- Conseils -->
                            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                                <h4 class="font-semibold text-amber-900 dark:text-amber-100 mb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Sections à personnaliser
                                </h4>
                                <ul class="text-sm text-amber-800 dark:text-amber-200 space-y-1">
                                    <li>• <strong>Lieu des séances</strong> : précisez studio, domicile, salle ou en ligne</li>
                                    <li>• <strong>Modes de paiement</strong> : listez vos options acceptées</li>
                                    <li>• <strong>Délais d'annulation</strong> : ajustez selon votre politique (24h, 48h...)</li>
                                    <li>• <strong>Durée de validité</strong> : définissez la durée des packs</li>
                                    <li>• <strong>Assurance</strong> : indiquez votre assureur et numéro de police</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                            <button
                                type="button"
                                @click="form.reset()"
                                class="px-6 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                Réinitialiser
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 flex items-center gap-2"
                            >
                                <svg v-if="!form.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Enregistrement...' : 'Enregistrer les mentions légales' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Note de bas de page -->
                <div class="mt-6 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>Note :</strong> Ces mentions légales sont fournies à titre indicatif. Il est recommandé de les faire valider par un professionnel du droit pour vous assurer qu'elles sont conformes à votre situation et à la législation en vigueur.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
