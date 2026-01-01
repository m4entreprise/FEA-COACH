<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    faqs: Array,
});

const showModal = ref(false);
const editingFaq = ref(null);

const form = useForm({
    question: '',
    answer: '',
    order: 0,
    is_active: true,
});

const openCreateModal = () => {
    editingFaq.value = null;
    form.reset();
    form.clearErrors();
    form.is_active = true;
    form.order = 0;
    showModal.value = true;
};

const openEditModal = (faq) => {
    editingFaq.value = faq;
    form.question = faq.question;
    form.answer = faq.answer;
    form.order = faq.order;
    form.is_active = faq.is_active;
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingFaq.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingFaq.value) {
        // Update existing FAQ
        form.patch(route('dashboard.faq.update', editingFaq.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        // Create new FAQ
        form.post(route('dashboard.faq.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteFaq = (faq) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer cette question ?\n"${faq.question}"`)) {
        router.delete(route('dashboard.faq.destroy', faq.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Gestion de la FAQ" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Gestion de la FAQ
            </h2>
        </template>

        <div class="py-10 md:py-12 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Header with Add Button -->
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Créez et gérez les questions fréquemment posées affichées sur votre site public.
                    </p>
                    <PrimaryButton @click="openCreateModal">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouvelle Question
                    </PrimaryButton>
                </div>

                <!-- FAQs List -->
                <div v-if="faqs.length > 0" class="space-y-4">
                    <div
                        v-for="faq in faqs"
                        :key="faq.id"
                        class="overflow-hidden rounded-lg bg-white shadow-sm transition-shadow hover:shadow-md dark:bg-gray-800"
                    >
                        <div class="p-6">
                            <!-- Status Badge & Order -->
                            <div class="mb-4 flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <span
                                        :class="[
                                            faq.is_active
                                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold'
                                        ]"
                                    >
                                        {{ faq.is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Ordre: {{ faq.order }}
                                    </span>
                                </div>
                            </div>

                            <!-- Question -->
                            <div class="mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ faq.question }}
                                </h3>
                            </div>

                            <!-- Answer -->
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">
                                    {{ faq.answer }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <button
                                    @click="openEditModal(faq)"
                                    class="flex-1 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                                >
                                    Modifier
                                </button>
                                <button
                                    @click="deleteFaq(faq)"
                                    class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2"
                                >
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="rounded-lg bg-white p-12 text-center shadow-sm dark:bg-gray-800">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucune question</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Commencez par créer votre première question fréquemment posée.
                    </p>
                    <div class="mt-6">
                        <PrimaryButton @click="openCreateModal">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Créer une Question
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Modal Create/Edit -->
                <div
                    v-if="showModal"
                    class="fixed inset-0 z-50 overflow-y-auto"
                    aria-labelledby="modal-title"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                        <!-- Background overlay -->
                        <div
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            aria-hidden="true"
                            @click="closeModal"
                        ></div>

                        <!-- Modal panel -->
                        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                            <form @submit.prevent="submit">
                                <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                        {{ editingFaq ? 'Modifier la question' : 'Créer une nouvelle question' }}
                                    </h3>

                                    <div class="space-y-4">
                                        <!-- Question -->
                                        <div>
                                            <InputLabel for="question" value="Question *" />
                                            <TextInput
                                                id="question"
                                                v-model="form.question"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                                placeholder="Ex: Combien coûte un accompagnement ?"
                                            />
                                            <InputError class="mt-2" :message="form.errors.question" />
                                        </div>

                                        <!-- Answer -->
                                        <div>
                                            <InputLabel for="answer" value="Réponse *" />
                                            <textarea
                                                id="answer"
                                                v-model="form.answer"
                                                rows="5"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                                placeholder="Rédigez votre réponse détaillée..."
                                            ></textarea>
                                            <InputError class="mt-2" :message="form.errors.answer" />
                                        </div>

                                        <!-- Order -->
                                        <div>
                                            <InputLabel for="order" value="Ordre d'affichage" />
                                            <TextInput
                                                id="order"
                                                v-model="form.order"
                                                type="number"
                                                min="0"
                                                class="mt-1 block w-full"
                                                placeholder="0"
                                            />
                                            <InputError class="mt-2" :message="form.errors.order" />
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Les questions sont triées par ordre croissant (0 = en premier)
                                            </p>
                                        </div>

                                        <!-- Is Active -->
                                        <div class="flex items-center">
                                            <input
                                                id="is_active"
                                                v-model="form.is_active"
                                                type="checkbox"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:ring-offset-gray-800"
                                            />
                                            <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                                Question active (visible sur le site public)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="bg-gray-50 px-4 py-3 dark:bg-gray-700 sm:flex sm:flex-row-reverse sm:px-6">
                                    <PrimaryButton
                                        type="submit"
                                        class="w-full justify-center sm:ml-3 sm:w-auto"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing"
                                    >
                                        {{ editingFaq ? 'Mettre à jour' : 'Créer' }}
                                    </PrimaryButton>
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-100 dark:ring-gray-500 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto"
                                    >
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
