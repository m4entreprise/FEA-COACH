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

        <div class="py-12 bg-gradient-to-br from-slate-50 via-purple-50 to-slate-50 dark:from-slate-900 dark:via-purple-900/20 dark:to-slate-900 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-xl p-6 text-white transform hover:scale-[1.01] transition-all duration-300 backdrop-blur-xl border border-green-400/20">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="font-semibold">✨ {{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Header with Add Button -->
                <div class="mb-8 rounded-2xl bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 shadow-xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-3 shadow-lg">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">💰 Vos Plans Tarifaires</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Créez et gérez vos offres tarifaires affichées sur votre site public.
                                </p>
                            </div>
                        </div>
                        <button @click="openCreateModal" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-xl font-bold text-sm text-white shadow-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nouveau Plan
                        </button>
                    </div>
                </div>

                <!-- Plans Grid -->
                <div v-if="plans.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="overflow-hidden rounded-2xl bg-gradient-to-br from-white to-emerald-50 dark:from-gray-800 dark:to-emerald-900/20 shadow-xl hover:shadow-2xl transform hover:scale-[1.02] transition-all duration-300 border border-emerald-200/50 dark:border-emerald-500/30 backdrop-blur-xl"
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
                            <div class="mt-6 flex gap-3">
                                <button
                                    @click="openEditModal(plan)"
                                    class="flex-1 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                                >
                                    ✏️ Modifier
                                </button>
                                <button
                                    @click="deletePlan(plan)"
                                    class="rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="rounded-2xl bg-gradient-to-br from-white to-emerald-50 dark:from-gray-800 dark:to-emerald-900/20 shadow-xl border border-emerald-200/50 dark:border-emerald-500/30 backdrop-blur-xl p-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-4 shadow-lg">
                            <svg class="h-12 w-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">💰 Aucun plan</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Commencez par créer votre premier plan tarifaire.
                    </p>
                    <button @click="openCreateModal" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl shadow-lg hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Créer un Plan
                    </button>
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
                            class="fixed inset-0 bg-gradient-to-br from-slate-900/80 via-purple-900/80 to-slate-900/80 backdrop-blur-sm transition-opacity"
                            aria-hidden="true"
                            @click="closeModal"
                        ></div>

                        <!-- Modal panel -->
                        <div class="inline-block transform overflow-hidden rounded-2xl bg-gradient-to-br from-white to-emerald-50 dark:from-gray-800 dark:to-emerald-900/20 text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle border border-emerald-200/50 dark:border-emerald-500/30 backdrop-blur-xl">
                            <form @submit.prevent="submit">
                                <div class="px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                                    <div class="flex items-center mb-6">
                                        <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-3 shadow-lg">
                                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent" id="modal-title">
                                            {{ editingPlan ? '✏️ Modifier le plan' : '✨ Créer un nouveau plan' }}
                                        </h3>
                                    </div>

                                    <div class="space-y-6">
                                        <!-- Name -->
                                        <div>
                                            <InputLabel for="name" value="🎯 Nom du plan *" />
                                            <TextInput
                                                id="name"
                                                v-model="form.name"
                                                type="text"
                                                class="mt-1 block w-full rounded-xl shadow-md focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 transition-all duration-200"
                                                required
                                                placeholder="Ex: Découverte, Suivi Mensuel..."
                                            />
                                            <InputError class="mt-2" :message="form.errors.name" />
                                        </div>

                                        <!-- Price -->
                                        <div>
                                            <InputLabel for="price" value="💵 Prix (€)" />
                                            <TextInput
                                                id="price"
                                                v-model="form.price"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="mt-1 block w-full rounded-xl shadow-md focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 transition-all duration-200"
                                                placeholder="Ex: 49.99"
                                            />
                                            <InputError class="mt-2" :message="form.errors.price" />
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                💡 Laissez vide pour "Prix sur demande"
                                            </p>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <InputLabel for="description" value="📝 Description" />
                                            <textarea
                                                id="description"
                                                v-model="form.description"
                                                rows="3"
                                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-md focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-700/50 dark:text-gray-300 backdrop-blur-sm transition-all duration-200"
                                                placeholder="Décrivez les avantages de ce plan..."
                                            ></textarea>
                                            <InputError class="mt-2" :message="form.errors.description" />
                                        </div>

                                        <!-- CTA URL -->
                                        <div>
                                            <InputLabel for="cta_url" value="🔗 URL de réservation (optionnel)" />
                                            <TextInput
                                                id="cta_url"
                                                v-model="form.cta_url"
                                                type="url"
                                                class="mt-1 block w-full rounded-xl shadow-md focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 transition-all duration-200"
                                                placeholder="https://calendly.com/..."
                                            />
                                            <InputError class="mt-2" :message="form.errors.cta_url" />
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                💡 Lien vers Calendly, formulaire de contact, etc.
                                            </p>
                                        </div>

                                        <!-- Is Active -->
                                        <div class="flex items-center p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-200/30 dark:border-emerald-500/20">
                                            <input
                                                id="is_active"
                                                v-model="form.is_active"
                                                type="checkbox"
                                                class="h-5 w-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-600 dark:border-gray-700 dark:bg-gray-700 dark:ring-offset-gray-800"
                                            />
                                            <label for="is_active" class="ml-3 block text-sm font-medium text-gray-900 dark:text-gray-300">
                                                ✅ Plan actif (visible sur le site public)
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="bg-gradient-to-r from-emerald-50/50 to-green-50/50 dark:from-emerald-900/20 dark:to-green-900/20 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse gap-3 border-t border-emerald-200/30 dark:border-emerald-500/20">
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
                                        {{ form.processing ? 'Enregistrement...' : (editingPlan ? '✨ Mettre à jour' : '✨ Créer') }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-md px-6 py-3 bg-white dark:bg-gray-700 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto transition-all duration-200"
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
