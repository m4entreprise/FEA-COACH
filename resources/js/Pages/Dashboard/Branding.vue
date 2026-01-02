<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    coach: Object,
    availableLayouts: Object,
    defaultLayout: String,
});

const form = useForm({
    color_primary: props.coach.color_primary || '#3B82F6',
    color_secondary: props.coach.color_secondary || '#10B981',
    site_layout: props.coach.site_layout || props.defaultLayout || 'classic',
    logo: null,
    hero: null,
});

const logoPreview = ref(props.coach.media?.find(m => m.collection_name === 'logo')?.original_url || null);
const heroPreview = ref(props.coach.media?.find(m => m.collection_name === 'hero')?.original_url || null);

const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const handleHeroChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.hero = file;
        heroPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('dashboard.branding.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Branding" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Branding
            </h2>
        </template>

        <div class="py-10 md:py-12 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="form.recentlySuccessful" class="rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-xl p-6 text-white transform hover:scale-[1.01] transition-all duration-300 backdrop-blur-xl border border-green-400/20">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="font-semibold">✨ Modifications enregistrées avec succès !</p>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="rounded-2xl bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 shadow-xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl transform hover:scale-[1.01] transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 shadow-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">🎨 Personnalisez votre marque</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Définissez l'identité visuelle de votre site : couleurs, logo, image hero et style de mise en page. Tous ces éléments seront appliqués automatiquement à votre site public.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Colors Section -->
                    <div class="overflow-hidden bg-gradient-to-br from-white to-indigo-50 dark:from-gray-800 dark:to-indigo-900/20 shadow-xl sm:rounded-2xl border border-indigo-200/50 dark:border-indigo-500/30 backdrop-blur-xl hover:shadow-2xl transition-all duration-300">
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-3 shadow-lg">
                                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                    🎨 Couleurs de la marque
                                </h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Choisissez les couleurs principales qui représentent votre identité visuelle
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Primary Color -->
                                <div class="bg-white/50 dark:bg-gray-900/50 rounded-xl p-6 border border-indigo-200/30 dark:border-indigo-500/20">
                                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        💎 Couleur primaire
                                    </label>
                                    <div class="flex items-center space-x-4 mb-3">
                                        <input
                                            type="color"
                                            v-model="form.color_primary"
                                            class="h-16 w-16 rounded-xl cursor-pointer border-2 border-white shadow-lg hover:scale-110 transition-transform duration-200"
                                        />
                                        <input
                                            type="text"
                                            v-model="form.color_primary"
                                            class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white font-mono text-sm"
                                            placeholder="#3B82F6"
                                        />
                                    </div>
                                    <div 
                                        class="h-12 rounded-lg shadow-inner transition-all duration-300"
                                        :style="{ backgroundColor: form.color_primary }"
                                    ></div>
                                    <p class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                        Utilisée pour les boutons et éléments importants
                                    </p>
                                </div>

                                <!-- Secondary Color -->
                                <div class="bg-white/50 dark:bg-gray-900/50 rounded-xl p-6 border border-indigo-200/30 dark:border-indigo-500/20">
                                    <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-3">
                                        ✨ Couleur secondaire
                                    </label>
                                    <div class="flex items-center space-x-4 mb-3">
                                        <input
                                            type="color"
                                            v-model="form.color_secondary"
                                            class="h-16 w-16 rounded-xl cursor-pointer border-2 border-white shadow-lg hover:scale-110 transition-transform duration-200"
                                        />
                                        <input
                                            type="text"
                                            v-model="form.color_secondary"
                                            class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white font-mono text-sm"
                                            placeholder="#10B981"
                                        />
                                    </div>
                                    <div 
                                        class="h-12 rounded-lg shadow-inner transition-all duration-300"
                                        :style="{ backgroundColor: form.color_secondary }"
                                    ></div>
                                    <p class="mt-3 text-xs text-gray-600 dark:text-gray-400">
                                        Pour les accents et variations
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Layout Selection Section -->
                    <div class="overflow-hidden bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 shadow-xl sm:rounded-2xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl hover:shadow-2xl transition-all duration-300">
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 shadow-lg">
                                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z"/>
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                    📐 Style de mise en page
                                </h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Choisissez le style de présentation visuelle de votre site public
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div
                                    v-for="(layout, key) in availableLayouts"
                                    :key="key"
                                    @click="form.site_layout = key"
                                    :class="[
                                        'relative cursor-pointer rounded-xl border-2 p-6 transition-all duration-300 group',
                                        form.site_layout === key
                                            ? 'border-purple-600 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 shadow-lg scale-105'
                                            : 'border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-900/50 hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-md hover:scale-102'
                                    ]"
                                >
                                    <!-- Selected Indicator -->
                                    <div
                                        v-if="form.site_layout === key"
                                        class="absolute top-3 right-3 h-7 w-7 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-600 to-pink-600 text-white shadow-lg"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>

                                    <h4 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                        {{ layout.label }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                        {{ layout.description }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-6 rounded-xl bg-purple-50 dark:bg-purple-900/20 p-4 border border-purple-200/30 dark:border-purple-500/20">
                                <p class="text-xs text-purple-700 dark:text-purple-300">
                                    <strong>💡 Info :</strong> Le layout détermine la présentation visuelle de votre site public. Vous pourrez toujours le changer plus tard.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Logo Section -->
                    <div class="overflow-hidden bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 shadow-xl sm:rounded-2xl border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl hover:shadow-2xl transition-all duration-300">
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 shadow-lg">
                                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                                    🏷️ Logo de votre marque
                                </h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Importez votre logo qui apparaîtra dans l'en-tête de votre site
                            </p>
                            <div class="space-y-4">
                                <div v-if="logoPreview" class="flex items-center space-x-6 bg-white/50 dark:bg-gray-900/50 rounded-xl p-6 border border-blue-200/30 dark:border-blue-500/20">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 rounded-xl p-4 shadow-lg">
                                        <img :src="logoPreview" alt="Logo" class="h-24 w-auto max-w-[200px] object-contain" />
                                    </div>
                                    <div class="flex-1">
                                        <label class="block cursor-pointer group">
                                            <span class="sr-only">Choisir un nouveau logo</span>
                                            <input
                                                type="file"
                                                @change="handleLogoChange"
                                                accept="image/*"
                                                class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-blue-500 file:to-cyan-600 file:text-white hover:file:from-blue-600 hover:file:to-cyan-700 file:shadow-lg file:transition-all file:duration-200 hover:file:scale-105"
                                            />
                                        </label>
                                    </div>
                                </div>
                                <div v-else class="bg-white/50 dark:bg-gray-900/50 rounded-xl p-8 border-2 border-dashed border-blue-300 dark:border-blue-500/30 hover:border-blue-400 dark:hover:border-blue-400 transition-colors">
                                    <label class="block cursor-pointer group">
                                        <span class="sr-only">Choisir un logo</span>
                                        <div class="text-center">
                                            <svg class="mx-auto h-16 w-16 text-blue-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-4 text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                Cliquez pour télécharger votre logo
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            @change="handleLogoChange"
                                            accept="image/*"
                                            class="hidden"
                                        />
                                    </label>
                                </div>
                                <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200/30 dark:border-blue-500/20">
                                    <p class="text-xs text-blue-700 dark:text-blue-300">
                                        <strong>💡 Conseils :</strong> Format PNG ou SVG recommandé. Taille maximale : 2MB
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image Section -->
                    <div class="overflow-hidden bg-gradient-to-br from-white to-emerald-50 dark:from-gray-800 dark:to-emerald-900/20 shadow-xl sm:rounded-2xl border border-emerald-200/50 dark:border-emerald-500/30 backdrop-blur-xl hover:shadow-2xl transition-all duration-300">
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-3 shadow-lg">
                                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">
                                    🖼️ Image Hero
                                </h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Image d'arrière-plan de votre page d'accueil pour captiver vos visiteurs
                            </p>
                            <div class="space-y-4">
                                <div v-if="heroPreview" class="space-y-4">
                                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border-2 border-emerald-200 dark:border-emerald-500/30">
                                        <img :src="heroPreview" alt="Hero" class="w-full h-64 object-cover" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                    </div>
                                    <label class="block cursor-pointer group">
                                        <span class="sr-only">Choisir une nouvelle image hero</span>
                                        <input
                                            type="file"
                                            @change="handleHeroChange"
                                            accept="image/*"
                                            class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-emerald-500 file:to-green-600 file:text-white hover:file:from-emerald-600 hover:file:to-green-700 file:shadow-lg file:transition-all file:duration-200 hover:file:scale-105"
                                        />
                                    </label>
                                </div>
                                <div v-else class="bg-white/50 dark:bg-gray-900/50 rounded-xl p-12 border-2 border-dashed border-emerald-300 dark:border-emerald-500/30 hover:border-emerald-400 dark:hover:border-emerald-400 transition-colors">
                                    <label class="block cursor-pointer group">
                                        <span class="sr-only">Choisir une image hero</span>
                                        <div class="text-center">
                                            <svg class="mx-auto h-20 w-20 text-emerald-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-4 text-base font-semibold text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                                Cliquez pour télécharger une image hero
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            @change="handleHeroChange"
                                            accept="image/*"
                                            class="hidden"
                                        />
                                    </label>
                                </div>
                                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/20 p-4 border border-emerald-200/30 dark:border-emerald-500/20">
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300">
                                        <strong>💡 Conseils :</strong> Image large recommandée (minimum 1920x1080). Taille maximale : 5MB
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-xl font-bold text-white shadow-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ form.processing ? 'Enregistrement en cours...' : '💾 Enregistrer les modifications' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
