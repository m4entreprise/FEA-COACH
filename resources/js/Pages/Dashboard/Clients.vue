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

const searchQuery = ref('');
const filteredClients = computed(() => {
    if (!searchQuery.value) return props.clients;
    const q = searchQuery.value.toLowerCase();
    return props.clients.filter(c => 
        c.first_name.toLowerCase().includes(q) ||
        c.last_name.toLowerCase().includes(q) ||
        (c.email && c.email.toLowerCase().includes(q)) ||
        (c.phone && c.phone.includes(q))
    );
});
const getInitials = (f, l) => (f.charAt(0) + l.charAt(0)).toUpperCase();
const getAvatarColor = (f, l) => {
    const colors = ['bg-purple-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-pink-500', 'bg-indigo-500', 'bg-red-500', 'bg-teal-500'];
    return colors[(f.charCodeAt(0) + l.charCodeAt(0)) % colors.length];
};

const showClientModal = ref(false);
const showNotesModal = ref(false);
const editingClient = ref(null);
const selectedClient = ref(null);
const editingNote = ref(null);

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
    if (confirm(`Supprimer ${client.first_name} ${client.last_name} ?`)) {
        router.delete(route('dashboard.clients.destroy', client.id), { preserveScroll: true });
    }
};

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
};

const openEditNote = (note) => {
    editingNote.value = note;
    noteForm.content = note.content;
};

const cancelEditNote = () => {
    editingNote.value = null;
    noteForm.reset();
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
    if (confirm('Supprimer cette note ?')) {
        router.delete(route('dashboard.clients.notes.destroy', note.id), {
            preserveScroll: true,
            onSuccess: () => router.reload({ only: ['clients'] }),
        });
    }
};

const formatDate = (d) => new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
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

                <!-- Stats Cards -->
                <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white shadow-lg">
                        <p class="text-sm text-purple-100">Total Clients</p>
                        <p class="mt-2 text-3xl font-bold">{{ clients.length }}</p>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white shadow-lg">
                        <p class="text-sm text-blue-100">Total Notes</p>
                        <p class="mt-2 text-3xl font-bold">{{ clients.reduce((s, c) => s + c.notes.length, 0) }}</p>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-green-500 to-green-600 p-6 text-white shadow-lg">
                        <p class="text-sm text-green-100">Avec Email</p>
                        <p class="mt-2 text-3xl font-bold">{{ clients.filter(c => c.email).length }}</p>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-pink-500 to-pink-600 p-6 text-white shadow-lg">
                        <p class="text-sm text-pink-100">Avec Téléphone</p>
                        <p class="mt-2 text-3xl font-bold">{{ clients.filter(c => c.phone).length }}</p>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative flex-1 sm:max-w-md">
                        <svg class="absolute left-3 top-4 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher un client..."
                            class="block w-full rounded-lg border py-3 pl-10 pr-10 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                        />
                        <button v-if="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-4 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <PrimaryButton @click="openCreateModal">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouveau Client
                    </PrimaryButton>
                </div>

                <div v-if="searchQuery" class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ filteredClients.length }} résultat{{ filteredClients.length > 1 ? 's' : '' }}
                </div>

                <!-- Clients Grid -->
                <div v-if="filteredClients.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="client in filteredClients"
                        :key="client.id"
                        class="overflow-hidden rounded-xl bg-white shadow-md transition-all hover:shadow-xl dark:bg-gray-800"
                    >
                        <!-- Header avec Avatar -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6 dark:from-gray-700 dark:to-gray-800">
                            <div class="flex items-center gap-3">
                                <div
                                    :class="[getAvatarColor(client.first_name, client.last_name), 'flex h-12 w-12 items-center justify-center rounded-full text-lg font-bold text-white shadow-lg']"
                                >
                                    {{ getInitials(client.first_name, client.last_name) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        {{ client.first_name }} {{ client.last_name }}
                                    </h3>
                                    <span class="text-xs bg-purple-100 px-2 py-0.5 rounded-full dark:bg-purple-900 dark:text-purple-200">
                                        {{ client.notes.length }} note{{ client.notes.length > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Infos Contact -->
                        <div class="p-6 space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900">
                                    <svg class="h-4 w-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="truncate text-sm text-gray-900 dark:text-gray-100">{{ client.email || 'Pas d\'email' }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-green-100 dark:bg-green-900">
                                    <svg class="h-4 w-4 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <p class="truncate text-sm text-gray-900 dark:text-gray-100">{{ client.phone || 'Pas de téléphone' }}</p>
                            </div>
                            <div v-if="client.notes.length > 0" class="rounded-lg bg-gray-50 p-3 dark:bg-gray-700">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Dernière note</p>
                                <p class="line-clamp-2 text-xs text-gray-600 dark:text-gray-300">{{ client.notes[0].content }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="border-t px-6 py-4 flex gap-2 dark:border-gray-700">
                            <button
                                @click="openNotesModal(client)"
                                class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                            >
                                Notes
                            </button>
                            <button
                                @click="openEditModal(client)"
                                class="rounded-lg bg-gray-200 px-4 py-2.5 hover:bg-gray-300 dark:bg-gray-600"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                @click="deleteClient(client)"
                                class="rounded-lg bg-red-600 px-4 py-2.5 hover:bg-red-700"
                            >
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="rounded-lg bg-white p-12 text-center shadow-sm dark:bg-gray-800">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ searchQuery ? 'Aucun client trouvé' : 'Aucun client' }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ searchQuery ? 'Essayez une autre recherche' : 'Commencez par ajouter votre premier client.' }}
                    </p>
                    <div v-if="!searchQuery" class="mt-6">
                        <PrimaryButton @click="openCreateModal">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter un Client
                        </PrimaryButton>
                    </div>
                </div>

                <!-- Modal Create/Edit Client -->
                <div v-if="showClientModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeClientModal"></div>
                        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                            <form @submit.prevent="submitClient">
                                <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6">
                                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ editingClient ? 'Modifier le client' : 'Ajouter un client' }}
                                    </h3>
                                    <div class="space-y-4">
                                        <div>
                                            <InputLabel for="first_name" value="Prénom *" />
                                            <TextInput id="first_name" v-model="clientForm.first_name" type="text" class="mt-1 block w-full" required />
                                            <InputError :message="clientForm.errors.first_name" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="last_name" value="Nom *" />
                                            <TextInput id="last_name" v-model="clientForm.last_name" type="text" class="mt-1 block w-full" required />
                                            <InputError :message="clientForm.errors.last_name" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="email" value="Email" />
                                            <TextInput id="email" v-model="clientForm.email" type="email" class="mt-1 block w-full" />
                                            <InputError :message="clientForm.errors.email" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="phone" value="Téléphone" />
                                            <TextInput id="phone" v-model="clientForm.phone" type="text" class="mt-1 block w-full" />
                                            <InputError :message="clientForm.errors.phone" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="vat_number" value="N° TVA" />
                                            <TextInput id="vat_number" v-model="clientForm.vat_number" type="text" class="mt-1 block w-full" />
                                            <InputError :message="clientForm.errors.vat_number" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="address" value="Adresse" />
                                            <textarea
                                                id="address"
                                                v-model="clientForm.address"
                                                rows="2"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                            ></textarea>
                                            <InputError :message="clientForm.errors.address" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 dark:bg-gray-700 sm:flex sm:flex-row-reverse sm:px-6">
                                    <PrimaryButton type="submit" class="w-full justify-center sm:ml-3 sm:w-auto" :class="{ 'opacity-25': clientForm.processing }" :disabled="clientForm.processing">
                                        {{ editingClient ? 'Mettre à jour' : 'Ajouter' }}
                                    </PrimaryButton>
                                    <button type="button" @click="closeClientModal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-100 sm:mt-0 sm:w-auto">
                                        Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Notes -->
                <div v-if="showNotesModal && selectedClient" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="closeNotesModal"></div>
                        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
                            <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6">
                                <div class="mb-4 flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Notes pour {{ selectedClient.first_name }} {{ selectedClient.last_name }}
                                    </h3>
                                    <button @click="closeNotesModal" class="rounded-md text-gray-400 hover:text-gray-500">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <form @submit.prevent="submitNote" class="mb-6">
                                    <InputLabel for="note_content" :value="editingNote ? 'Modifier la note' : 'Ajouter une note'" />
                                    <textarea
                                        id="note_content"
                                        v-model="noteForm.content"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        placeholder="Écrivez votre note ici..."
                                        required
                                    ></textarea>
                                    <InputError :message="noteForm.errors.content" class="mt-2" />
                                    <div class="mt-3 flex gap-2">
                                        <PrimaryButton type="submit" :class="{ 'opacity-25': noteForm.processing }" :disabled="noteForm.processing">
                                            {{ editingNote ? 'Mettre à jour' : 'Ajouter' }}
                                        </PrimaryButton>
                                        <button v-if="editingNote" type="button" @click="cancelEditNote" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-100">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                                <div class="space-y-3">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Historique ({{ selectedClient.notes.length }})
                                    </h4>
                                    <div v-if="selectedClient.notes.length === 0" class="rounded-lg bg-gray-50 p-6 text-center dark:bg-gray-700">
                                        <p class="text-sm text-gray-500">Aucune note pour ce client.</p>
                                    </div>
                                    <div v-for="note in selectedClient.notes" :key="note.id" class="rounded-lg border bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                                        <div class="mb-2 flex items-start justify-between">
                                            <p class="text-xs text-gray-500">{{ formatDate(note.created_at) }}</p>
                                            <div class="flex gap-2">
                                                <button @click="openEditNote(note)" class="text-xs text-blue-600 hover:text-blue-800">Modifier</button>
                                                <button @click="deleteNote(note)" class="text-xs text-red-600 hover:text-red-800">Supprimer</button>
                                            </div>
                                        </div>
                                        <p class="whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100">{{ note.content }}</p>
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
