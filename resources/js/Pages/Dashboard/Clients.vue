<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    plans: Array,
});

const showModal = ref(false);
const editingPlan = ref(null);

const form = useForm({
    name: '',
    description: '',
    price: '',
    cta_url: '',
    is_active: true,
});

const openCreateModal = () => {
    editingPlan.value = null;
    form.reset();
    form.clearErrors();
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (plan) => {
    editingPlan.value = plan;
    form.name = plan.name;
    form.description = plan.description || '';
    form.price = plan.price || '';
    form.cta_url = plan.cta_url || '';
    form.is_active = plan.is_active;
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingPlan.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingPlan.value) {
        // Update existing plan
        form.patch(route('dashboard.plans.update', editingPlan.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        // Create new plan
        form.post(route('dashboard.plans.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deletePlan = (plan) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le plan "${plan.name}" ?`)) {
        router.delete(route('dashboard.plans.destroy', plan.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Gestion des Plans" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Gestion des Plans Tarifaires
            </h2>
        </template>

        <div class="py-12">
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
                        Créez et gérez vos offres tarifaires affichées sur votre site public.
                    </p>
                    <PrimaryButton @click="openCreateModal">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouveau Plan
                    </PrimaryButton>
                </div>

                <!-- Plans Grid -->
                <div v-if="plans.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="overflow-hidden rounded-lg bg-white shadow-sm transition-shadow hover:shadow-md dark:bg-gray-800"
                    >
                        <div class="p-6">
                            <!-- Status Badge -->
                            <div class="mb-4 flex items-start justify-between">
                                <span
                                    :class="[
                                        plan.is_active
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                        'inline-flex rounded-full px-2 py-1 text-xs font-semibold'
                                    ]"
                                >
                                    {{ plan.is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>

                            <!-- Plan Name & Price -->
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ plan.name }}
                            </h3>
                            <p v-if="plan.price" class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                                {{ parseFloat(plan.price).toFixed(2) }}€
                            </p>
                            <p v-else class="mt-2 text-sm italic text-gray-500 dark:text-gray-400">
                                Prix sur demande
                            </p>

                            <!-- Description -->
                            <p v-if="plan.description" class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ plan.description }}
                            </p>

                            <!-- CTA URL -->
                            <p v-if="plan.cta_url" class="mt-2 truncate text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-medium">Lien :</span>
                                <a :href="plan.cta_url" target="_blank" class="ml-1 text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    {{ plan.cta_url }}
                                </a>
                            </p>

                            <!-- Actions -->
                            <div class="mt-6 flex gap-2">
                                <button
                                    @click="openEditModal(plan)"
                                    class="flex-1 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                                >
                                    Modifier
                                </button>
                                <button
                                    @click="deletePlan(plan)"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucun plan</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Commencez par créer votre premier plan tarifaire.
                    </p>
                    <div class="mt-6">
                        <PrimaryButton @click="openCreateModal">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Créer un Plan
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
                                        {{ editingPlan ? 'Modifier le plan' : 'Créer un nouveau plan' }}
                                    </h3>

                                    <div class="space-y-4">
                                        <!-- Name -->
                                        <div>
                                            <InputLabel for="name" value="Nom du plan *" />
                                            <TextInput
                                                id="name"
                                                v-model="form.name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                                placeholder="Ex: Découverte, Suivi Mensuel..."
                                            />
                                            <InputError class="mt-2" :message="form.errors.name" />
                                        </div>

                                        <!-- Price -->
                                        <div>
                                            <InputLabel for="price" value="Prix (€)" />
                                            <TextInput
                                                id="price"
                                                v-model="form.price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="mt-1 block w-full"
                                                placeholder="Ex: 49.99"
                                            />
                                            <InputError class="mt-2" :message="form.errors.price" />
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Laissez vide pour "Prix sur demande"
                                            </p>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <InputLabel for="description" value="Description" />
                                            <textarea
                                                id="description"
                                                v-model="form.description"
                                                rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                                placeholder="Décrivez les avantages de ce plan..."
                                            ></textarea>
                                            <InputError class="mt-2" :message="form.errors.description" />
                                        </div>

                                        <!-- CTA URL -->
                                        <div>
                                            <InputLabel for="cta_url" value="URL de réservation (optionnel)" />
                                            <TextInput
                                                id="cta_url"
                                                v-model="form.cta_url"
                                                type="url"
                                                class="mt-1 block w-full"
                                                placeholder="https://calendly.com/..."
                                            />
                                            <InputError class="mt-2" :message="form.errors.cta_url" />
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Lien vers Calendly, formulaire de contact, etc.
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
                                                Plan actif (visible sur le site public)
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
                                        {{ editingPlan ? 'Mettre à jour' : 'Créer' }}
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
