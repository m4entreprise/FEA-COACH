<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    coach: Object,
});

const form = useForm({
    color_primary: props.coach.color_primary || '#3B82F6',
    color_secondary: props.coach.color_secondary || '#10B981',
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

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Colors Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    Couleurs de la marque
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Primary Color -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Couleur primaire
                                        </label>
                                        <div class="flex items-center space-x-4">
                                            <input
                                                type="color"
                                                v-model="form.color_primary"
                                                class="h-12 w-24 rounded cursor-pointer"
                                            />
                                            <input
                                                type="text"
                                                v-model="form.color_primary"
                                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                placeholder="#3B82F6"
                                            />
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur principale utilisée pour les boutons et éléments importants
                                        </p>
                                    </div>

                                    <!-- Secondary Color -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Couleur secondaire
                                        </label>
                                        <div class="flex items-center space-x-4">
                                            <input
                                                type="color"
                                                v-model="form.color_secondary"
                                                class="h-12 w-24 rounded cursor-pointer"
                                            />
                                            <input
                                                type="text"
                                                v-model="form.color_secondary"
                                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                placeholder="#10B981"
                                            />
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Couleur secondaire pour les accents et variations
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Logo Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    Logo
                                </h3>
                                <div class="space-y-4">
                                    <div v-if="logoPreview" class="flex items-center space-x-4">
                                        <img :src="logoPreview" alt="Logo" class="h-24 w-auto object-contain bg-gray-100 rounded p-2" />
                                        <div class="flex-1">
                                            <label class="block">
                                                <span class="sr-only">Choisir un nouveau logo</span>
                                                <input
                                                    type="file"
                                                    @change="handleLogoChange"
                                                    accept="image/*"
                                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                />
                                            </label>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <label class="block">
                                            <span class="sr-only">Choisir un logo</span>
                                            <input
                                                type="file"
                                                @change="handleLogoChange"
                                                accept="image/*"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                            />
                                        </label>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        Format PNG ou SVG recommandé. Taille maximale : 2MB
                                    </p>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Hero Image Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    Image Hero (arrière-plan de la page d'accueil)
                                </h3>
                                <div class="space-y-4">
                                    <div v-if="heroPreview" class="flex flex-col space-y-4">
                                        <img :src="heroPreview" alt="Hero" class="w-full h-64 object-cover rounded-lg" />
                                        <label class="block">
                                            <span class="sr-only">Choisir une nouvelle image hero</span>
                                            <input
                                                type="file"
                                                @change="handleHeroChange"
                                                accept="image/*"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                            />
                                        </label>
                                    </div>
                                    <div v-else>
                                        <label class="block">
                                            <span class="sr-only">Choisir une image hero</span>
                                            <input
                                                type="file"
                                                @change="handleHeroChange"
                                                accept="image/*"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                            />
                                        </label>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        Image large recommandée (minimum 1920x1080). Taille maximale : 5MB
                                    </p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-4">
                                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                                    Enregistré avec succès !
                                </p>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ form.processing ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
