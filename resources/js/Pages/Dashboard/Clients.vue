<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    clients: Array,
});

const showClientModal = ref(false);
const showNotesModal = ref(false);
const editingClient = ref(null);
const selectedClient = ref(null);

const clientForm = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    vat_number: '',
});

const noteForm = useForm({
    content: '',
});

const editingNote = ref(null);

// Client Management
const openCreateModal = () => {
    editingClient.value = null;
    clientForm.reset();
    clientForm.clearErrors();
    showClientModal.value = true;
};

const openEditModal = (client) => {
    editingClient.value = client;
    clientForm.first_name = client.first_name;
    clientForm.last_name = client.last_name;
    clientForm.email = client.email || '';
    clientForm.phone = client.phone || '';
    clientForm.address = client.address || '';
    clientForm.vat_number = client.vat_number || '';
    clientForm.clearErrors();
    showClientModal.value = true;
};

const closeClientModal = () => {
    showClientModal.value = false;
    editingClient.value = null;
    clientForm.reset();
    clientForm.clearErrors();
};

const submitClient = () => {
    if (editingClient.value) {
        clientForm.patch(route('dashboard.clients.update', editingClient.value.id), {
            preserveScroll: true,
            onSuccess: () => closeClientModal(),
        });
    } else {
        clientForm.post(route('dashboard.clients.store'), {
            preserveScroll: true,
            onSuccess: () => closeClientModal(),
        });
    }
};

const deleteClient = (client) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le client "${client.first_name} ${client.last_name}" ? Toutes ses notes seront également supprimées.`)) {
        router.delete(route('dashboard.clients.destroy', client.id), {
            preserveScroll: true,
        });
    }
};

// Notes Management
const openNotesModal = (client) => {
    selectedClient.value = client;
    editingNote.value = null;
    noteForm.reset();
    noteForm.clearErrors();
    showNotesModal.value = true;
};

const closeNotesModal = () => {
    showNotesModal.value = false;
    selectedClient.value = null;
    editingNote.value = null;
    noteForm.reset();
    noteForm.clearErrors();
};

const openEditNote = (note) => {
    editingNote.value = note;
    noteForm.content = note.content;
    noteForm.clearErrors();
};

const cancelEditNote = () => {
    editingNote.value = null;
    noteForm.reset();
    noteForm.clearErrors();
};

const submitNote = () => {
    if (editingNote.value) {
        noteForm.patch(route('dashboard.clients.notes.update', editingNote.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                editingNote.value = null;
                noteForm.reset();
                router.reload({ only: ['clients'] });
            },
        });
    } else {
        noteForm.post(route('dashboard.clients.notes.store', selectedClient.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                noteForm.reset();
                router.reload({ only: ['clients'] });
            },
        });
    }
};

const deleteNote = (note) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette note ?')) {
        router.delete(route('dashboard.clients.notes.destroy', note.id), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['clients'] });
            },
        });
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Gestion des Clients" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Gestion des Clients
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
                        Gérez vos clients et leurs informations.
                    </p>
                    <PrimaryButton @click="openCreateModal">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouveau Client
                    </PrimaryButton>
                </div>

                <!-- Clients Table -->
                <div v-if="clients.length > 0" class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Nom
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Téléphone
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Notes
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                <tr v-for="client in clients" :key="client.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ client.first_name }} {{ client.last_name }}
                                        </div>
                                        <div v-if="client.vat_number" class="text-xs text-gray-500 dark:text-gray-400">
                                            TVA: {{ client.vat_number }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div v-if="client.email" class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ client.email }}
                                        </div>
                                        <div v-else class="text-sm italic text-gray-400 dark:text-gray-500">
                                            Non renseigné
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div v-if="client.phone" class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ client.phone }}
                                        </div>
                                        <div v-else class="text-sm italic text-gray-400 dark:text-gray-500">
                                            Non renseigné
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ client.notes.length }} note{{ client.notes.length > 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <button
                                            @click="openNotesModal(client)"
                                            class="mr-3 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            📝 Notes
                                        </button>
                                        <button
                                            @click="openEditModal(client)"
                                            class="mr-3 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            Modifier
                                        </button>
                                        <button
                                            @click="deleteClient(client)"
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="rounded-lg bg-white p-12 text-center shadow-sm dark:bg-gray-800">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucun client</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Commencez par ajouter votre premier client.
                    </p>
                    <div class="mt-6">
                        <PrimaryButton @click="openCreateModal">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter un Client
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Modal Create/Edit Client -->
                <div
                    v-if="showClientModal"
                    class="fixed inset-0 z-50 overflow-y-auto"
                    aria-labelledby="modal-title"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                        <div
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            aria-hidden="true"
                            @click="closeClientModal"
                        ></div>

                        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                            <form @submit.prevent="submitClient">
                                <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                        {{ editingClient ? 'Modifier le client' : 'Ajouter un nouveau client' }}
                                    </h3>

                                    <div class="space-y-4">
                                        <!-- First Name -->
                                        <div>
                                            <InputLabel for="first_name" value="Prénom *" />
                                            <TextInput
                                                id="first_name"
                                                v-model="clientForm.first_name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                            <InputError class="mt-2" :message="clientForm.errors.first_name" />
                                        </div>

                                        <!-- Last Name -->
                                        <div>
                                            <InputLabel for="last_name" value="Nom *" />
                                            <TextInput
                                                id="last_name"
                                                v-model="clientForm.last_name"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                            <InputError class="mt-2" :message="clientForm.errors.last_name" />
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <InputLabel for="email" value="Email" />
                                            <TextInput
                                                id="email"
                                                v-model="clientForm.email"
                                                type="email"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="clientForm.errors.email" />
                                        </div>

                                        <!-- Phone -->
                                        <div>
                                            <InputLabel for="phone" value="Téléphone" />
                                            <TextInput
                                                id="phone"
                                                v-model="clientForm.phone"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="clientForm.errors.phone" />
                                        </div>

                                        <!-- VAT Number -->
                                        <div>
                                            <InputLabel for="vat_number" value="Numéro de TVA" />
                                            <TextInput
                                                id="vat_number"
                                                v-model="clientForm.vat_number"
                                                type="text"
                                                class="mt-1 block w-full"
                                            />
                                            <InputError class="mt-2" :message="clientForm.errors.vat_number" />
                                        </div>

                                        <!-- Address -->
                                        <div>
                                            <InputLabel for="address" value="Adresse" />
                                            <textarea
                                                id="address"
                                                v-model="clientForm.address"
                                                rows="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                            ></textarea>
                                            <InputError class="mt-2" :message="clientForm.errors.address" />
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-4 py-3 dark:bg-gray-700 sm:flex sm:flex-row-reverse sm:px-6">
                                    <PrimaryButton
                                        type="submit"
                                        class="w-full justify-center sm:ml-3 sm:w-auto"
                                        :class="{ 'opacity-25': clientForm.processing }"
                                        :disabled="clientForm.processing"
                                    >
                                        {{ editingClient ? 'Mettre à jour' : 'Ajouter' }}
                                    </PrimaryButton>
                                    <button
                                        type="button"
                                        @click="closeClientModal"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-100 dark:ring-gray-500 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto"
                                    >
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Notes -->
                <div
                    v-if="showNotesModal && selectedClient"
                    class="fixed inset-0 z-50 overflow-y-auto"
                    aria-labelledby="notes-modal-title"
                    role="dialog"
                    aria-modal="true"
                >
                    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                        <div
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            aria-hidden="true"
                            @click="closeNotesModal"
                        ></div>

                        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
                            <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="notes-modal-title">
                                        Notes pour {{ selectedClient.first_name }} {{ selectedClient.last_name }}
                                    </h3>
                                    <button
                                        @click="closeNotesModal"
                                        class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                                    >
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Add/Edit Note Form -->
                                <form @submit.prevent="submitNote" class="mb-6">
                                    <InputLabel for="note_content" :value="editingNote ? 'Modifier la note' : 'Ajouter une note'" />
                                    <textarea
                                        id="note_content"
                                        v-model="noteForm.content"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                        placeholder="Écrivez votre note ici..."
                                        required
                                    ></textarea>
                                    <InputError class="mt-2" :message="noteForm.errors.content" />
                                    
                                    <div class="mt-3 flex gap-2">
                                        <PrimaryButton
                                            type="submit"
                                            :class="{ 'opacity-25': noteForm.processing }"
                                            :disabled="noteForm.processing"
                                        >
                                            {{ editingNote ? 'Mettre à jour' : 'Ajouter' }}
                                        </PrimaryButton>
                                        <button
                                            v-if="editingNote"
                                            type="button"
                                            @click="cancelEditNote"
                                            class="inline-flex justify-center rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500"
                                        >
                                            Annuler
                                        </button>
                                    </div>
                                </form>

                                <!-- Notes List -->
                                <div class="space-y-3">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Historique des notes ({{ selectedClient.notes.length }})
                                    </h4>
                                    
                                    <div v-if="selectedClient.notes.length === 0" class="rounded-lg bg-gray-50 p-6 text-center dark:bg-gray-700">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Aucune note pour ce client.
                                        </p>
                                    </div>

                                    <div
                                        v-for="note in selectedClient.notes"
                                        :key="note.id"
                                        class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700"
                                    >
                                        <div class="mb-2 flex items-start justify-between">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ formatDate(note.created_at) }}
                                            </p>
                                            <div class="flex gap-2">
                                                <button
                                                    @click="openEditNote(note)"
                                                    class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    Modifier
                                                </button>
                                                <button
                                                    @click="deleteNote(note)"
                                                    class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                >
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                        <p class="whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ note.content }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
