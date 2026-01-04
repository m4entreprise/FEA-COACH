<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { Toaster, toast } from 'vue-sonner';

const props = defineProps({
    services: Array,
});

const showModal = ref(false);
const editingService = ref(null);

const form = useForm({
    name: '',
    description: '',
    duration_minutes: 60,
    price: 50,
    currency: 'EUR',
    is_active: true,
    booking_enabled: true,
    max_advance_booking_days: 60,
    min_advance_booking_hours: 24,
});
const deleteForm = useForm({});

const openCreateModal = () => {
    editingService.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (service) => {
    editingService.value = service;
    form.name = service.name;
    form.description = service.description;
    form.duration_minutes = service.duration_minutes;
    form.price = service.price;
    form.currency = service.currency;
    form.is_active = service.is_active;
    form.booking_enabled = service.booking_enabled;
    form.max_advance_booking_days = service.max_advance_booking_days;
    form.min_advance_booking_hours = service.min_advance_booking_hours;
    showModal.value = true;
};

const submit = () => {
    if (editingService.value) {
        form.patch(route('dashboard.services.update', editingService.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
                toast.success('Service mis à jour', {
                    description: `${editingService.value.name} est à jour sur votre vitrine.`,
                });
            },
            onError: () => {
                toast.error('Impossible de mettre à jour', {
                    description: 'Vérifiez les champs requis puis réessayez.',
                });
            },
        });
    } else {
        form.post(route('dashboard.services.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
                toast.success('Service créé', {
                    description: 'Votre nouvelle offre est prête à être proposée.',
                });
            },
            onError: () => {
                toast.error('Création impossible', {
                    description: 'Corrigez les erreurs de formulaire puis réessayez.',
                });
            },
        });
    }
};

const deleteService = (service) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
        deleteForm.delete(route('dashboard.services.destroy', service.id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Service supprimé');
            },
            onError: () => {
                toast.error('Suppression impossible', {
                    description: 'Réessayez dans un instant.',
                });
            },
        });
    }
};
</script>

<template>
    <Head title="Mes Services" />

    <AuthenticatedLayout>
        <Toaster rich-colors position="top-right" />
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mes Services
                </h2>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                >
                    <PlusIcon class="h-4 w-4 mr-2" />
                    Ajouter un service
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="services.length === 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        Aucun service créé. Commencez par ajouter vos types de séances.
                    </div>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="service in services"
                        :key="service.id"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ service.name }}</h3>
                                        <span
                                            v-if="service.is_active"
                                            class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                        >
                                            Actif
                                        </span>
                                        <span
                                            v-else
                                            class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            Inactif
                                        </span>
                                    </div>
                                    <p v-if="service.description" class="text-gray-600 mb-3">{{ service.description }}</p>
                                    <div class="flex items-center space-x-6 text-sm text-gray-500">
                                        <span>⏱️ {{ service.duration_minutes }} min</span>
                                        <span>💰 {{ service.price }}€</span>
                                        <span v-if="service.booking_enabled" class="text-green-600">✅ Réservable</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Réservable jusqu'à {{ service.min_advance_booking_hours }}h à l'avance
                                    </p>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <button
                                        @click="openEditModal(service)"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded"
                                    >
                                        <PencilIcon class="h-5 w-5" />
                                    </button>
                                    <button
                                        @click="deleteService(service)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded"
                                    >
                                        <TrashIcon class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submit">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ editingService ? 'Modifier le service' : 'Nouveau service' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du service *</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Ex: Séance découverte"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Description du service..."
                                    ></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Durée (minutes) *</label>
                                        <input
                                            v-model.number="form.duration_minutes"
                                            type="number"
                                            min="15"
                                            max="480"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix (€) *</label>
                                        <input
                                            v-model.number="form.price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Délai minimum de réservation (heures)
                                    </label>
                                    <input
                                        v-model.number="form.min_advance_booking_hours"
                                        type="number"
                                        min="0"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        Les clients doivent réserver au moins X heures à l'avance
                                    </p>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                        <span class="ml-2 text-sm text-gray-700">Service actif</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input v-model="form.booking_enabled" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                        <span class="ml-2 text-sm text-gray-700">Réservable en ligne</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                {{ editingService ? 'Mettre à jour' : 'Créer' }}
                            </button>
                            <button
                                type="button"
                                @click="showModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
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
