<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    coach: Object,
});

const form = useForm({
    hero_title: props.coach.hero_title || '',
    hero_subtitle: props.coach.hero_subtitle || '',
    about_text: props.coach.about_text || '',
    method_text: props.coach.method_text || '',
    cta_text: props.coach.cta_text || 'Commencer maintenant',
});

const submit = () => {
    form.post(route('dashboard.content.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Contenu" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Contenu du site
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Hero Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    Section Hero (page d'accueil)
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Titre principal
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.hero_title"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Transformez votre corps, transformez votre vie"
                                            maxlength="255"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">
                                            Le titre principal affiché en grand sur la page d'accueil (max 255 caractères)
                                        </p>
                                        <div v-if="form.errors.hero_title" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.hero_title }}
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Sous-titre
                                        </label>
                                        <input
                                            type="text"
                                            v-model="form.hero_subtitle"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            placeholder="Coaching sportif personnalisé pour atteindre vos objectifs"
                                            maxlength="500"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">
                                            Le sous-titre affiché sous le titre principal (max 500 caractères)
                                        </p>
                                        <div v-if="form.errors.hero_subtitle" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.hero_subtitle }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- About Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    Section "À propos"
                                </h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Texte de présentation
                                    </label>
                                    <textarea
                                        v-model="form.about_text"
                                        rows="6"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Parlez de votre expérience, vos qualifications, votre passion..."
                                        maxlength="5000"
                                    ></textarea>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Présentez-vous et votre approche du coaching (max 5000 caractères)
                                    </p>
                                    <div v-if="form.errors.about_text" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.about_text }}
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Method Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    Section "Ma méthode"
                                </h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Description de votre méthode
                                    </label>
                                    <textarea
                                        v-model="form.method_text"
                                        rows="6"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Décrivez votre approche, votre méthode d'entraînement, votre philosophie..."
                                        maxlength="5000"
                                    ></textarea>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Expliquez votre méthode de coaching et ce qui vous différencie (max 5000 caractères)
                                    </p>
                                    <div v-if="form.errors.method_text" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.method_text }}
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- CTA Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    Appel à l'action
                                </h3>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Texte du bouton CTA
                                    </label>
                                    <input
                                        type="text"
                                        v-model="form.cta_text"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Commencer maintenant"
                                        maxlength="100"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        Le texte affiché sur les boutons d'appel à l'action (max 100 caractères)
                                    </p>
                                    <div v-if="form.errors.cta_text" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.cta_text }}
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Box -->
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-6">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                    Aperçu rapide
                                </h4>
                                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                    <p><strong>Hero:</strong> {{ form.hero_title || '(vide)' }}</p>
                                    <p><strong>Sous-titre:</strong> {{ form.hero_subtitle || '(vide)' }}</p>
                                    <p><strong>CTA:</strong> {{ form.cta_text || '(vide)' }}</p>
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
