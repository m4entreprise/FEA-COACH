<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    availability: Array,
});

const showModal = ref(false);
const selectedDay = ref(null);

const form = useForm({
    day_of_week: 1,
    start_time: '09:00',
    end_time: '18:00',
    is_active: true,
});

const days = [
    { value: 1, name: 'Lundi' },
    { value: 2, name: 'Mardi' },
    { value: 3, name: 'Mercredi' },
    { value: 4, name: 'Jeudi' },
    { value: 5, name: 'Vendredi' },
    { value: 6, name: 'Samedi' },
    { value: 0, name: 'Dimanche' },
];

const getDayName = (day) => {
    return days.find(d => d.value === day)?.name || 'Inconnu';
};

const openModal = (day) => {
    selectedDay.value = day;
    form.day_of_week = day;
    form.start_time = '09:00';
    form.end_time = '18:00';
    form.is_active = true;
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    form.post(route('dashboard.availability.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const deleteSlot = (slot) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?')) {
        useForm({}).delete(route('dashboard.availability.destroy', slot.id));
    }
};
</script>

<template>
    <Head title="Disponibilités" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mes Disponibilités
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <p class="text-gray-600 mb-4">
                            Définissez vos créneaux hebdomadaires. Les clients pourront réserver sur ces plages horaires.
                        </p>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <p class="text-sm text-blue-800">
                                💡 Conseil : Laissez des pauses entre vos créneaux pour éviter les enchaînements trop serrés.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div
                        v-for="dayData in availability"
                        :key="dayData.day"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ getDayName(dayData.day) }}
                                </h3>
                                <button
                                    @click="openModal(dayData.day)"
                                    class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                >
                                    <PlusIcon class="h-4 w-4 mr-1" />
                                    Ajouter un créneau
                                </button>
                            </div>

                            <div v-if="dayData.slots.length === 0" class="text-center py-4 text-gray-500">
                                Aucun créneau défini pour ce jour
                            </div>

                            <div v-else class="space-y-2">
                                <div
                                    v-for="slot in dayData.slots"
                                    :key="slot.id"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                                >
                                    <div class="flex items-center">
                                        <span class="text-gray-900 font-medium">
                                            {{ slot.start_time.substring(0, 5) }} - {{ slot.end_time.substring(0, 5) }}
                                        </span>
                                        <span
                                            v-if="slot.is_active"
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
                                    <button
                                        @click="deleteSlot(slot)"
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
                                Ajouter un créneau - {{ getDayName(selectedDay) }}
                            </h3>

                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Heure de début *</label>
                                        <input
                                            v-model="form.start_time"
                                            type="time"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Heure de fin *</label>
                                        <input
                                            v-model="form.end_time"
                                            type="time"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Créneau actif</span>
                                </div>

                                <div v-if="form.errors.start_time || form.errors.end_time" class="bg-red-50 border-l-4 border-red-400 p-4">
                                    <p class="text-sm text-red-800">
                                        {{ form.errors.start_time || form.errors.end_time }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            >
                                Ajouter
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
