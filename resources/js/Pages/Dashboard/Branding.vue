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
    color_primary: props.coach?.color_primary || '#3B82F6',
    color_secondary: props.coach?.color_secondary || '#10B981',
    site_layout: props.coach?.site_layout || props.defaultLayout || 'classic',
    logo: null,
    hero: null,
});

const logoPreview = ref(props.coach?.media?.find(m => m.collection_name === 'logo')?.original_url || null);
const heroPreview = ref(props.coach?.media?.find(m => m.collection_name === 'hero')?.original_url || null);

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
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Branding & Identité Visuelle
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Personnalisez l'apparence et l'identité de votre site
                    </p>
                </div>
                <a v-if="props.coach?.slug"
                   :href="route('coach.site', { slug: props.coach.slug })"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 rounded-xl border border-purple-200/50 dark:border-purple-500/30 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm text-sm font-semibold text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Prévisualiser le site
                </a>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Intro Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 via-pink-500 to-purple-600 rounded-2xl shadow-xl p-8 text-white">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-pink-400/20 rounded-full blur-2xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold mb-2">Créez votre identité unique</h3>
                        <p class="text-purple-100">Personnalisez les couleurs, le logo et l'apparence de votre site pour refléter votre marque.</p>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="overflow-hidden bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl shadow-xl sm:rounded-2xl border border-purple-200/50 dark:border-purple-500/30">
                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Colors Section -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            Palette de couleurs
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Définissez les couleurs principales de votre marque
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                    <!-- Primary Color -->
                                    <div class="group space-y-4">
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                            Couleur primaire
                                        </label>
                                        <div class="flex items-stretch gap-4">
                                            <input
                                                type="color"
                                                v-model="form.color_primary"
                                                class="h-20 w-20 rounded-xl cursor-pointer border-4 border-white dark:border-gray-700 shadow-lg ring-2 ring-purple-200 dark:ring-purple-500/30 hover:ring-purple-400 hover:scale-110 transition-all duration-200"
                                            />
                                            <div class="flex-1 space-y-2">
                                                <input
                                                    type="text"
                                                    v-model="form.color_primary"
                                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:text-white font-mono text-lg"
                                                    placeholder="#8B5CF6"
                                                />
                                                <div class="grid grid-cols-5 gap-2">
                                                    <button type="button" @click="form.color_primary = '#8B5CF6'" class="h-8 rounded-lg bg-purple-600 hover:ring-2 ring-purple-400 transition-all" title="Violet"></button>
                                                    <button type="button" @click="form.color_primary = '#3B82F6'" class="h-8 rounded-lg bg-blue-500 hover:ring-2 ring-blue-400 transition-all" title="Bleu"></button>
                                                    <button type="button" @click="form.color_primary = '#10B981'" class="h-8 rounded-lg bg-emerald-500 hover:ring-2 ring-emerald-400 transition-all" title="Vert"></button>
                                                    <button type="button" @click="form.color_primary = '#F59E0B'" class="h-8 rounded-lg bg-amber-500 hover:ring-2 ring-amber-400 transition-all" title="Orange"></button>
                                                    <button type="button" @click="form.color_primary = '#EF4444'" class="h-8 rounded-lg bg-red-500 hover:ring-2 ring-red-400 transition-all" title="Rouge"></button>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-start gap-2">
                                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Utilisée pour les boutons, liens et éléments d'action principaux</span>
                                        </p>
                                    </div>

                                    <!-- Secondary Color -->
                                    <div class="group space-y-4">
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                            Couleur secondaire
                                        </label>
                                        <div class="flex items-stretch gap-4">
                                            <input
                                                type="color"
                                                v-model="form.color_secondary"
                                                class="h-20 w-20 rounded-xl cursor-pointer border-4 border-white dark:border-gray-700 shadow-lg ring-2 ring-pink-200 dark:ring-pink-500/30 hover:ring-pink-400 hover:scale-110 transition-all duration-200"
                                            />
                                            <div class="flex-1 space-y-2">
                                                <input
                                                    type="text"
                                                    v-model="form.color_secondary"
                                                    class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-pink-500 focus:ring-pink-500 dark:bg-gray-700 dark:text-white font-mono text-lg"
                                                    placeholder="#EC4899"
                                                />
                                                <div class="grid grid-cols-5 gap-2">
                                                    <button type="button" @click="form.color_secondary = '#EC4899'" class="h-8 rounded-lg bg-pink-500 hover:ring-2 ring-pink-400 transition-all" title="Rose"></button>
                                                    <button type="button" @click="form.color_secondary = '#06B6D4'" class="h-8 rounded-lg bg-cyan-500 hover:ring-2 ring-cyan-400 transition-all" title="Cyan"></button>
                                                    <button type="button" @click="form.color_secondary = '#14B8A6'" class="h-8 rounded-lg bg-teal-500 hover:ring-2 ring-teal-400 transition-all" title="Turquoise"></button>
                                                    <button type="button" @click="form.color_secondary = '#F97316'" class="h-8 rounded-lg bg-orange-500 hover:ring-2 ring-orange-400 transition-all" title="Orange"></button>
                                                    <button type="button" @click="form.color_secondary = '#A855F7'" class="h-8 rounded-lg bg-purple-500 hover:ring-2 ring-purple-400 transition-all" title="Violet"></button>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-start gap-2">
                                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Pour les accents, badges et éléments de mise en valeur</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Color Preview -->
                                <div class="p-6 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900/50 dark:to-gray-800/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700">
                                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-4 flex items-center gap-2">
                                        Aperçu en contexte
                                    </h4>
                                    <div class="flex flex-wrap gap-4">
                                        <button 
                                            type="button"
                                            :style="{ backgroundColor: form.color_primary }"
                                            class="px-6 py-3 rounded-xl text-white font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all"
                                        >
                                            Bouton Principal
                                        </button>
                                        <button 
                                            type="button"
                                            :style="{ backgroundColor: form.color_secondary }"
                                            class="px-6 py-3 rounded-xl text-white font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all"
                                        >
                                            Bouton Secondaire
                                        </button>
                                        <span 
                                            :style="{ backgroundColor: form.color_primary + '20', color: form.color_primary }"
                                            class="px-4 py-2 rounded-full text-sm font-semibold"
                                        >
                                            Badge
                                        </span>
                                        <span 
                                            :style="{ color: form.color_primary }"
                                            class="px-4 py-2 text-lg font-bold underline decoration-2 underline-offset-4"
                                        >
                                            Lien texte
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="relative py-8">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t-2 border-gray-200 dark:border-gray-700"></div>
                                </div>
                            </div>

                            <!-- Layout Selection Section -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1v-3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            Style de mise en page
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Choisissez le style visuel de votre site public
                                        </p>
                                    </div>
                                </div>

                                <div v-if="availableLayouts" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div
                                        v-for="(layout, key) in availableLayouts"
                                        :key="key"
                                        @click="form.site_layout = key"
                                        :class="[
                                            'relative cursor-pointer rounded-2xl border-2 p-6 transition-all duration-300 hover:scale-105 group',
                                            form.site_layout === key
                                                ? 'border-purple-500 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 shadow-xl ring-4 ring-purple-200/50 dark:ring-purple-500/30'
                                                : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 hover:border-purple-300 dark:hover:border-purple-600 hover:shadow-lg'
                                        ]"
                                    >
                                        <!-- Selected Indicator -->
                                        <div
                                            v-if="form.site_layout === key"
                                            class="absolute -top-3 -right-3 h-10 w-10 flex items-center justify-center rounded-full bg-gradient-to-br from-purple-600 to-pink-600 text-white shadow-lg ring-4 ring-white dark:ring-gray-800"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>

                                        <!-- Layout Icon/Preview -->
                                        <div class="mb-4 h-24 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center group-hover:scale-110 transition-transform">
                                            <div class="text-5xl">{{ key === 'classic' ? '📰' : key === 'modern' ? '✨' : '🎯' }}</div>
                                        </div>

                                        <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                                            {{ layout.label }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                            {{ layout.description }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        Le layout détermine la présentation visuelle de votre site public. Vous pourrez toujours le changer plus tard sans perdre votre contenu.
                                    </p>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="relative py-8">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t-2 border-gray-200 dark:border-gray-700"></div>
                                </div>
                            </div>

                            <!-- Logo Section -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            Logo
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Téléchargez le logo de votre marque
                                        </p>
                                    </div>
                                </div>

                                <div v-if="logoPreview" class="space-y-4">
                                    <div class="relative group p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                        <img :src="logoPreview" alt="Logo" class="max-h-32 w-auto object-contain" />
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl flex items-center justify-center">
                                            <label class="cursor-pointer">
                                                <span class="px-6 py-3 bg-white text-gray-900 rounded-xl font-semibold hover:bg-gray-100 transition-colors inline-flex items-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                    Changer le logo
                                                </span>
                                                <input
                                                    type="file"
                                                    @change="handleLogoChange"
                                                    accept="image/*"
                                                    class="sr-only"
                                                />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <label class="group relative block cursor-pointer">
                                        <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-12 text-center hover:border-purple-400 dark:hover:border-purple-500 transition-all hover:bg-purple-50/50 dark:hover:bg-purple-900/10">
                                            <div class="space-y-4">
                                                <div class="mx-auto h-16 w-16 rounded-full bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                                    <svg class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                        Cliquez pour télécharger un logo
                                                    </p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        ou glissez-déposez votre fichier ici
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <input
                                            type="file"
                                            @change="handleLogoChange"
                                            accept="image/*"
                                            class="sr-only"
                                        />
                                    </label>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Format PNG ou SVG recommandé • Taille maximale : 2MB • Fond transparent préféré</span>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="relative py-8">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t-2 border-gray-200 dark:border-gray-700"></div>
                                </div>
                            </div>

                            <!-- Hero Image Section -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            Image Hero
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Image d'arrière-plan de la page d'accueil
                                        </p>
                                    </div>
                                </div>

                                <div v-if="heroPreview" class="space-y-4">
                                    <div class="relative group rounded-2xl overflow-hidden border-2 border-gray-200 dark:border-gray-700">
                                        <img :src="heroPreview" alt="Hero" class="w-full h-72 object-cover" />
                                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <label class="cursor-pointer">
                                                <span class="px-6 py-3 bg-white text-gray-900 rounded-xl font-semibold hover:bg-gray-100 transition-colors inline-flex items-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                    Changer l'image
                                                </span>
                                                <input
                                                    type="file"
                                                    @change="handleHeroChange"
                                                    accept="image/*"
                                                    class="sr-only"
                                                />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <label class="group relative block cursor-pointer">
                                        <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-16 text-center hover:border-purple-400 dark:hover:border-purple-500 transition-all hover:bg-purple-50/50 dark:hover:bg-purple-900/10">
                                            <div class="space-y-4">
                                                <div class="mx-auto h-20 w-20 rounded-full bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 flex items-center justify-center group-hover:scale-110 transition-transform">
                                                    <svg class="h-10 w-10 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                        Cliquez pour télécharger une image hero
                                                    </p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                        ou glissez-déposez votre fichier ici
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <input
                                            type="file"
                                            @change="handleHeroChange"
                                            accept="image/*"
                                            class="sr-only"
                                        />
                                    </label>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Image large recommandée (minimum 1920x1080) • Taille maximale : 5MB • Format JPG ou PNG</span>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="relative py-8">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t-2 border-gray-200 dark:border-gray-700"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-6">
                                <div class="flex items-center gap-3">
                                    <transition
                                        enter-active-class="transition ease-out duration-300"
                                        enter-from-class="opacity-0 scale-90"
                                        enter-to-class="opacity-100 scale-100"
                                        leave-active-class="transition ease-in duration-200"
                                        leave-from-class="opacity-100 scale-100"
                                        leave-to-class="opacity-0 scale-90"
                                    >
                                        <div v-if="form.recentlySuccessful" class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm font-semibold text-green-700 dark:text-green-300">
                                                Enregistré avec succès !
                                            </span>
                                        </div>
                                    </transition>
                                </div>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="group relative inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-xl font-bold text-white shadow-lg hover:shadow-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-800 disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5 transition-all duration-200"
                                >
                                    <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg v-else class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{{ form.processing ? 'Enregistrement en cours...' : 'Enregistrer les modifications' }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
