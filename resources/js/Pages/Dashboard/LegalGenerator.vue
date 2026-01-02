<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import InputError from '@/Components/InputError.vue';
import axios from 'axios';

const props = defineProps({
    coach: Object,
    user: Object,
});

const showPreview = ref(false);
const previewHtml = ref('');
const isGeneratingPreview = ref(false);

const form = useForm({
    // User entity fields
    entity_type: props.user?.entity_type || 'PP',
    legal_name: props.user?.legal_name || props.user?.name || '',
    company_number: props.user?.company_number || '',
    legal_representative: props.user?.legal_representative || '',
    phone_contact: props.user?.phone_contact || '',
    vat_number: props.user?.vat_number || '',
    legal_address: props.user?.legal_address || '',
    
    // Coach service flags
    is_coaching_presentiel: props.coach?.is_coaching_presentiel || false,
    is_coaching_online: props.coach?.is_coaching_online || false,
    has_digital_products: props.coach?.has_digital_products || false,
    has_subscriptions: props.coach?.has_subscriptions || false,
    use_client_photos: props.coach?.use_client_photos || false,
    
    // Business rules
    vat_regime: props.coach?.vat_regime || 'ASSUJETTI',
    cancellation_delay: props.coach?.cancellation_delay || 24,
    tribunal_city: props.coach?.tribunal_city || 'Bruxelles',
    insurance_company: props.coach?.insurance_company || '',
    insurance_policy_number: props.coach?.insurance_policy_number || '',
});

const belgianCities = [
    'Bruxelles', 'Anvers', 'Gand', 'Charleroi', 'Liège', 'Bruges', 
    'Namur', 'Louvain', 'Mons', 'Malines', 'Alost', 'Hasselt',
    'Ostende', 'Genk', 'Seraing', 'Tournai', 'Verviers'
];

const isEntitySociety = computed(() => form.entity_type === 'SOC');

const submitForm = () => {
    form.post(route('dashboard.legal.update'), {
        preserveScroll: true,
        onSuccess: () => {
            generatePreview();
        }
    });
};

const generatePreview = async () => {
    if (isGeneratingPreview.value) return;
    
    isGeneratingPreview.value = true;
    try {
        const response = await axios.post(route('api.legal.generate-preview'), {
            ...form.data(),
            nom_commercial: props.coach.name,
            email: props.user.email,
        });
        previewHtml.value = response.data.html;
        showPreview.value = true;
    } catch (error) {
        console.error('Erreur génération aperçu:', error);
    } finally {
        isGeneratingPreview.value = false;
    }
};

watch(() => [
    form.entity_type,
    form.is_coaching_presentiel,
    form.is_coaching_online,
    form.has_digital_products,
    form.vat_regime,
], () => {
    if (showPreview.value) {
        generatePreview();
    }
}, { deep: true });

const copyToClipboard = () => {
    navigator.clipboard.writeText(previewHtml.value);
    alert('HTML copié dans le presse-papier !');
};
</script>

<template>
    <Head title="Générateur de Mentions Légales" />

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
                                ⚖️ Générateur de Mentions Légales
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                Créez automatiquement vos CGV et politique de confidentialité conformes à la législation belge
                            </p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Colonne gauche : Formulaire -->
                        <div class="space-y-6">
                            <!-- 1. Identité de l'entité -->
                            <div class="bg-gradient-to-br from-white to-purple-50/30 dark:from-gray-800 dark:to-purple-900/10 rounded-2xl shadow-xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-2.5 shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">📋 Identité de l'entité</h3>
                                </div>

                                <div class="space-y-4">
                                    <!-- Type d'entité -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Type d'entité *
                                        </label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center flex-1 p-3 rounded-lg border-2 cursor-pointer transition-all"
                                                   :class="form.entity_type === 'PP' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-300 dark:border-gray-600'">
                                                <input type="radio" v-model="form.entity_type" value="PP" class="mr-2">
                                                <span class="text-sm font-medium">Personne Physique</span>
                                            </label>
                                            <label class="flex items-center flex-1 p-3 rounded-lg border-2 cursor-pointer transition-all"
                                                   :class="form.entity_type === 'SOC' ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-300 dark:border-gray-600'">
                                                <input type="radio" v-model="form.entity_type" value="SOC" class="mr-2">
                                                <span class="text-sm font-medium">Société</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Nom légal -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            {{ isEntitySociety ? 'Dénomination sociale *' : 'Nom complet *' }}
                                        </label>
                                        <input
                                            v-model="form.legal_name"
                                            type="text"
                                            class="w-full px-4 py-2.5 rounded-xl border border-purple-300 dark:border-purple-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent shadow-md transition-all duration-200"
                                            :placeholder="isEntitySociety ? 'ex: FitCoach SPRL' : 'ex: Jean Dupont'"
                                        />
                                        <InputError :message="form.errors.legal_name" class="mt-1" />
                                    </div>

                                    <!-- N° BCE -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Numéro BCE *
                                        </label>
                                        <input
                                            v-model="form.company_number"
                                            type="text"
                                            class="w-full px-4 py-2.5 rounded-xl border border-purple-300 dark:border-purple-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent shadow-md transition-all duration-200"
                                            placeholder="0xxx.xxx.xxx"
                                        />
                                        <InputError :message="form.errors.company_number" class="mt-1" />
                                    </div>

                                    <!-- Représentant légal (si société) -->
                                    <div v-if="isEntitySociety">
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Représentant légal *
                                        </label>
                                        <input
                                            v-model="form.legal_representative"
                                            type="text"
                                            class="w-full px-4 py-2.5 rounded-xl border border-purple-300 dark:border-purple-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent shadow-md transition-all duration-200"
                                            placeholder="ex: Jean Dupont, Gérant"
                                        />
                                        <InputError :message="form.errors.legal_representative" class="mt-1" />
                                    </div>

                                    <!-- Adresse -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Adresse du siège *
                                        </label>
                                        <textarea
                                            v-model="form.legal_address"
                                            rows="2"
                                            class="w-full px-4 py-2.5 rounded-xl border border-purple-300 dark:border-purple-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent shadow-md transition-all duration-200"
                                            placeholder="Rue, Numéro, CP, Ville"
                                        ></textarea>
                                        <InputError :message="form.errors.legal_address" class="mt-1" />
                                    </div>

                                    <!-- N° TVA -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Numéro de TVA
                                        </label>
                                        <input
                                            v-model="form.vat_number"
                                            type="text"
                                            class="w-full px-4 py-2.5 rounded-xl border border-purple-300 dark:border-purple-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent shadow-md transition-all duration-200"
                                            placeholder="BE 0xxx.xxx.xxx"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Laissez vide si régime de franchise</p>
                                    </div>

                                    <!-- Téléphone -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Téléphone professionnel
                                        </label>
                                        <input
                                            v-model="form.phone_contact"
                                            type="text"
                                            class="w-full px-4 py-2.5 rounded-xl border border-purple-300 dark:border-purple-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent shadow-md transition-all duration-200"
                                            placeholder="+32 xxx xx xx xx"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Types de services -->
                            <div class="bg-gradient-to-br from-white to-green-50/30 dark:from-gray-800 dark:to-green-900/10 rounded-2xl shadow-xl border border-green-200/50 dark:border-green-500/30 backdrop-blur-xl p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-2.5 shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">🏋️ Types de Services</h3>
                                </div>

                                <div class="space-y-3">
                                    <label class="flex items-center p-3 rounded-lg border-2 cursor-pointer transition-all hover:bg-green-50 dark:hover:bg-green-900/20"
                                           :class="form.is_coaching_presentiel ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600'">
                                        <input type="checkbox" v-model="form.is_coaching_presentiel" class="mr-3 h-5 w-5 text-green-500 rounded">
                                        <div class="flex-1">
                                            <span class="font-semibold text-gray-900 dark:text-white">Coaching en présentiel</span>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Séances en salle, domicile ou extérieur</p>
                                        </div>
                                    </label>

                                    <label class="flex items-center p-3 rounded-lg border-2 cursor-pointer transition-all hover:bg-green-50 dark:hover:bg-green-900/20"
                                           :class="form.is_coaching_online ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600'">
                                        <input type="checkbox" v-model="form.is_coaching_online" class="mr-3 h-5 w-5 text-green-500 rounded">
                                        <div class="flex-1">
                                            <span class="font-semibold text-gray-900 dark:text-white">Coaching en ligne</span>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Visio ou suivi via application</p>
                                        </div>
                                    </label>

                                    <label class="flex items-center p-3 rounded-lg border-2 cursor-pointer transition-all hover:bg-green-50 dark:hover:bg-green-900/20"
                                           :class="form.has_digital_products ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600'">
                                        <input type="checkbox" v-model="form.has_digital_products" class="mr-3 h-5 w-5 text-green-500 rounded">
                                        <div class="flex-1">
                                            <span class="font-semibold text-gray-900 dark:text-white">Produits numériques</span>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Ebooks, PDF, vidéos pré-enregistrées</p>
                                        </div>
                                    </label>

                                    <label class="flex items-center p-3 rounded-lg border-2 cursor-pointer transition-all hover:bg-green-50 dark:hover:bg-green-900/20"
                                           :class="form.has_subscriptions ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600'">
                                        <input type="checkbox" v-model="form.has_subscriptions" class="mr-3 h-5 w-5 text-green-500 rounded">
                                        <div class="flex-1">
                                            <span class="font-semibold text-gray-900 dark:text-white">Abonnements récurrents</span>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Reconduction tacite</p>
                                        </div>
                                    </label>

                                    <label class="flex items-center p-3 rounded-lg border-2 cursor-pointer transition-all hover:bg-green-50 dark:hover:bg-green-900/20"
                                           :class="form.use_client_photos ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600'">
                                        <input type="checkbox" v-model="form.use_client_photos" class="mr-3 h-5 w-5 text-green-500 rounded">
                                        <div class="flex-1">
                                            <span class="font-semibold text-gray-900 dark:text-white">Photos avant/après</span>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Utilisation à des fins promotionnelles</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- 3. Règles métier -->
                            <div class="bg-gradient-to-br from-white to-amber-50/30 dark:from-gray-800 dark:to-amber-900/10 rounded-2xl shadow-xl border border-amber-200/50 dark:border-amber-500/30 backdrop-blur-xl p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-2.5 shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">⚖️ Règles Métier</h3>
                                </div>

                                <div class="space-y-4">
                                    <!-- Régime TVA -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Régime TVA *
                                        </label>
                                        <div class="flex gap-4">
                                            <label class="flex items-center flex-1 p-3 rounded-lg border-2 cursor-pointer transition-all"
                                                   :class="form.vat_regime === 'ASSUJETTI' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-300 dark:border-gray-600'">
                                                <input type="radio" v-model="form.vat_regime" value="ASSUJETTI" class="mr-2">
                                                <span class="text-sm font-medium">Assujetti (21%)</span>
                                            </label>
                                            <label class="flex items-center flex-1 p-3 rounded-lg border-2 cursor-pointer transition-all"
                                                   :class="form.vat_regime === 'FRANCHISE' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-300 dark:border-gray-600'">
                                                <input type="radio" v-model="form.vat_regime" value="FRANCHISE" class="mr-2">
                                                <span class="text-sm font-medium">Franchise</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Délai annulation -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Délai d'annulation (heures) *
                                        </label>
                                        <input
                                            v-model.number="form.cancellation_delay"
                                            type="number"
                                            min="0"
                                            step="1"
                                            class="w-full px-4 py-2.5 rounded-xl border border-amber-300 dark:border-amber-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent shadow-md transition-all duration-200"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Minimum pour annuler sans frais (ex: 24 ou 48h)</p>
                                    </div>

                                    <!-- Tribunal -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Tribunal compétent *
                                        </label>
                                        <select
                                            v-model="form.tribunal_city"
                                            class="w-full px-4 py-2.5 rounded-xl border border-amber-300 dark:border-amber-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent shadow-md transition-all duration-200"
                                        >
                                            <option v-for="city in belgianCities" :key="city" :value="city">{{ city }}</option>
                                        </select>
                                    </div>

                                    <!-- Assurance (optionnel) -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            Assureur (optionnel)
                                        </label>
                                        <input
                                            v-model="form.insurance_company"
                                            type="text"
                                            class="w-full px-4 py-2.5 rounded-xl border border-amber-300 dark:border-amber-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent shadow-md transition-all duration-200"
                                            placeholder="ex: AXA, Ethias..."
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            N° de police (optionnel)
                                        </label>
                                        <input
                                            v-model="form.insurance_policy_number"
                                            type="text"
                                            class="w-full px-4 py-2.5 rounded-xl border border-amber-300 dark:border-amber-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent shadow-md transition-all duration-200"
                                            placeholder="Numéro de police"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne droite : Aperçu -->
                        <div class="space-y-6">
                            <!-- Actions -->
                            <div class="bg-gradient-to-br from-white to-purple-50/30 dark:from-gray-800 dark:to-purple-900/10 rounded-2xl shadow-xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl p-6">
                                <div class="flex flex-wrap gap-3">
                                    <button
                                        type="button"
                                        @click="generatePreview"
                                        :disabled="isGeneratingPreview"
                                        class="flex-1 px-5 py-2.5 text-sm bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl shadow-lg hover:from-blue-700 hover:to-cyan-700 transform hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2 font-semibold disabled:opacity-50"
                                    >
                                        <svg v-if="!isGeneratingPreview" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ isGeneratingPreview ? 'Génération...' : '👁️ Générer l\'aperçu' }}
                                    </button>
                                    <button
                                        v-if="showPreview"
                                        type="button"
                                        @click="copyToClipboard"
                                        class="px-5 py-2.5 text-sm bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl shadow-lg hover:from-gray-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-200 flex items-center gap-2 font-semibold"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Copier
                                    </button>
                                </div>
                            </div>

                            <!-- Aperçu -->
                            <div v-if="showPreview" class="bg-gradient-to-br from-white to-blue-50/30 dark:from-gray-800 dark:to-blue-900/10 rounded-2xl shadow-xl border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-2.5 shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100">👁️ Aperçu en temps réel</h3>
                                </div>
                                <div 
                                    class="prose prose-sm max-w-none bg-white dark:bg-gray-900 rounded-xl p-6 max-h-[600px] overflow-y-auto border border-gray-200 dark:border-gray-700"
                                    v-html="previewHtml"
                                ></div>
                            </div>

                            <!-- Info -->
                            <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200/50 dark:border-amber-500/30 backdrop-blur-xl shadow-lg rounded-2xl p-6">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-2.5 shadow-md">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-amber-800 dark:text-amber-200">
                                            <strong class="font-bold">⚠️ Note importante :</strong> Les mentions légales générées sont conformes à la législation belge mais restent fournies à titre indicatif. Nous vous recommandons de les faire valider par un professionnel du droit.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="mt-6 bg-gradient-to-r from-purple-50/50 to-pink-50/50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-6 flex justify-end gap-3">
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
                            {{ form.processing ? 'Enregistrement...' : '✨ Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
