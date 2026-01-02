<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    transformations: Array,
});

const showAddModal = ref(false);
const beforePreview = ref(null);
const afterPreview = ref(null);

const form = useForm({
    title: '',
    description: '',
    before: null,
    after: null,
});

const handleBeforeChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.before = file;
        beforePreview.value = URL.createObjectURL(file);
    }
};

const handleAfterChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.after = file;
        afterPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('dashboard.gallery.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showAddModal.value = false;
            beforePreview.value = null;
            afterPreview.value = null;
        },
    });
};

const deleteTransformation = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette transformation ?')) {
        router.delete(route('dashboard.gallery.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Galerie" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Galerie de transformations
                </h2>
                <button
                    @click="showAddModal = true"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-xl font-bold text-sm text-white shadow-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter une transformation
                </button>
            </div>
        </template>

        <div class="py-10 md:py-12 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Transformations Grid -->
                <div v-if="transformations.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="transformation in transformations"
                        :key="transformation.id"
                        class="bg-slate-900/85 rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transform hover:scale-[1.02] transition-all duration-300 border border-slate-800 backdrop-blur-xl"
                    >
                        <!-- Images -->
                        <div class="grid grid-cols-2">
                            <div class="relative">
                                <img
                                    v-if="transformation.media?.find(m => m.collection_name === 'before')"
                                    :src="transformation.media.find(m => m.collection_name === 'before').original_url"
                                    alt="Avant"
                                    class="w-full h-48 object-cover"
                                />
                                <div v-else class="w-full h-48 bg-slate-800 flex items-center justify-center">
                                    <span class="text-slate-400 text-sm">Avant</span>
                                </div>
                                <div class="absolute top-2 left-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg">
                                    AVANT
                                </div>
                            </div>
                            <div class="relative">
                                <img
                                    v-if="transformation.media?.find(m => m.collection_name === 'after')"
                                    :src="transformation.media.find(m => m.collection_name === 'after').original_url"
                                    alt="Après"
                                    class="w-full h-48 object-cover"
                                />
                                <div v-else class="w-full h-48 bg-slate-800 flex items-center justify-center">
                                    <span class="text-slate-400 text-sm">Apres</span>
                                </div>
                                <div class="absolute top-2 right-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg">
                                    APRÈS
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="font-bold text-lg text-slate-50 mb-2">
                                {{ transformation.title }}
                            </h3>
                            <p v-if="transformation.description" class="text-sm text-slate-300 mb-4 line-clamp-2">
                                {{ transformation.description }}
                            </p>
                            <button
                                @click="deleteTransformation(transformation.id)"
                                class="w-full px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold rounded-xl shadow-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            >
                                🗑️ Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-slate-900/85 rounded-2xl shadow-xl border border-slate-800 backdrop-blur-xl p-12 text-center text-slate-50">
                    <div class="flex justify-center mb-4">
                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-4 shadow-lg">
                            <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-2">
                        Aucune transformation
                    </h3>
                    <p class="text-sm text-slate-300 mb-6">
                        Commencez par ajouter votre premiere transformation avant/apres
                    </p>
                    <button
                        @click="showAddModal = true"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl shadow-lg hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter une transformation
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Transformation Modal -->
        <div
            v-if="showAddModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div
                    class="fixed inset-0 bg-gradient-to-br from-slate-900/80 via-purple-900/80 to-slate-900/80 backdrop-blur-sm transition-opacity"
                    @click="showAddModal = false"
                ></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl">
                    <form @submit.prevent="submit">
                        <div class="px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                    <div class="flex items-center mb-6">
                                        <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 shadow-lg">
                                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent" id="modal-title">
                                            📸 Ajouter une transformation
                                        </h3>
                                    </div>
                                    
                                    <div class="space-y-6">
                                        <!-- Title -->
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                🎯 Titre
                                            </label>
                                            <input
                                                type="text"
                                                v-model="form.title"
                                                class="block w-full rounded-xl border-gray-300 shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white backdrop-blur-sm transition-all duration-200"
                                                placeholder="Ex: Perte de 15kg en 3 mois"
                                                required
                                            />
                                            <div v-if="form.errors.title" class="mt-2 text-sm text-red-600 font-medium">
                                                {{ form.errors.title }}
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                📝 Description (optionnelle)
                                            </label>
                                            <textarea
                                                v-model="form.description"
                                                rows="3"
                                                class="block w-full rounded-xl border-gray-300 shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white backdrop-blur-sm transition-all duration-200"
                                                placeholder="Quelques mots sur cette transformation..."
                                            ></textarea>
                                            <div v-if="form.errors.description" class="mt-2 text-sm text-red-600 font-medium">
                                                {{ form.errors.description }}
                                            </div>
                                        </div>

                                        <!-- Images -->
                                        <div class="grid grid-cols-2 gap-6">
                                            <!-- Before Image -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                    🔴 Photo Avant
                                                </label>
                                                <div v-if="beforePreview" class="mb-3">
                                                    <img :src="beforePreview" alt="Avant" class="w-full h-40 object-cover rounded-xl shadow-lg border-2 border-red-200 dark:border-red-500/30" />
                                                </div>
                                                <input
                                                    type="file"
                                                    @change="handleBeforeChange"
                                                    accept="image/*"
                                                    required
                                                    class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-red-50 file:to-red-100 file:text-red-700 hover:file:from-red-100 hover:file:to-red-200 file:shadow-md file:transition-all file:duration-200 dark:file:from-red-900/30 dark:file:to-red-800/30 dark:file:text-red-300"
                                                />
                                                <div v-if="form.errors.before" class="mt-2 text-sm text-red-600 font-medium">
                                                    {{ form.errors.before }}
                                                </div>
                                            </div>

                                            <!-- After Image -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                    🟢 Photo Après
                                                </label>
                                                <div v-if="afterPreview" class="mb-3">
                                                    <img :src="afterPreview" alt="Après" class="w-full h-40 object-cover rounded-xl shadow-lg border-2 border-green-200 dark:border-green-500/30" />
                                                </div>
                                                <input
                                                    type="file"
                                                    @change="handleAfterChange"
                                                    accept="image/*"
                                                    required
                                                    class="block w-full text-sm text-gray-600 dark:text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-green-50 file:to-emerald-100 file:text-green-700 hover:file:from-green-100 hover:file:to-emerald-200 file:shadow-md file:transition-all file:duration-200 dark:file:from-green-900/30 dark:file:to-emerald-800/30 dark:file:text-green-300"
                                                />
                                                <div v-if="form.errors.after" class="mt-2 text-sm text-red-600 font-medium">
                                                    {{ form.errors.after }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-50/50 to-pink-50/50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse gap-3 border-t border-purple-200/30 dark:border-purple-500/20">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full inline-flex justify-center items-center rounded-xl border border-transparent shadow-lg px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-base font-bold text-white hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg v-else class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ form.processing ? 'Enregistrement...' : '✨ Ajouter' }}
                            </button>
                            <button
                                type="button"
                                @click="showAddModal = false"
                                :disabled="form.processing"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-md px-6 py-3 bg-white dark:bg-gray-700 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto transition-all duration-200"
                            >
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
