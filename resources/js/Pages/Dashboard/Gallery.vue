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
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter une transformation
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Transformations Grid -->
                <div v-if="transformations.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="transformation in transformations"
                        :key="transformation.id"
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"
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
                                <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">Avant</span>
                                </div>
                                <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
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
                                <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">Après</span>
                                </div>
                                <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                                    APRÈS
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-2">
                                {{ transformation.title }}
                            </h3>
                            <p v-if="transformation.description" class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                {{ transformation.description }}
                            </p>
                            <button
                                @click="deleteTransformation(transformation.id)"
                                class="w-full px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded hover:bg-red-700 transition"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        Aucune transformation
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Commencez par ajouter votre première transformation avant/après
                    </p>
                    <button
                        @click="showAddModal = true"
                        class="mt-6 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
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
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="showAddModal = false"
                ></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form @submit.prevent="submit">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4" id="modal-title">
                                        Ajouter une transformation
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <!-- Title -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Titre
                                            </label>
                                            <input
                                                type="text"
                                                v-model="form.title"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                placeholder="Ex: Perte de 15kg en 3 mois"
                                                required
                                            />
                                            <div v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.title }}
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Description (optionnelle)
                                            </label>
                                            <textarea
                                                v-model="form.description"
                                                rows="3"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                placeholder="Quelques mots sur cette transformation..."
                                            ></textarea>
                                            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.description }}
                                            </div>
                                        </div>

                                        <!-- Images -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Before Image -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Photo Avant
                                                </label>
                                                <div v-if="beforePreview" class="mb-2">
                                                    <img :src="beforePreview" alt="Avant" class="w-full h-40 object-cover rounded" />
                                                </div>
                                                <input
                                                    type="file"
                                                    @change="handleBeforeChange"
                                                    accept="image/*"
                                                    required
                                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                />
                                                <div v-if="form.errors.before" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.before }}
                                                </div>
                                            </div>

                                            <!-- After Image -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Photo Après
                                                </label>
                                                <div v-if="afterPreview" class="mb-2">
                                                    <img :src="afterPreview" alt="Après" class="w-full h-40 object-cover rounded" />
                                                </div>
                                                <input
                                                    type="file"
                                                    @change="handleAfterChange"
                                                    accept="image/*"
                                                    required
                                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                                                />
                                                <div v-if="form.errors.after" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.after }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                {{ form.processing ? 'Enregistrement...' : 'Ajouter' }}
                            </button>
                            <button
                                type="button"
                                @click="showAddModal = false"
                                :disabled="form.processing"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
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
