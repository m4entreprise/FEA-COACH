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

                <!-- Stats Cards -->
                <div class="mb-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 border border-purple-400/30 backdrop-blur-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-100 font-medium">👥 Total Clients</p>
                                <p class="mt-2 text-4xl font-bold">{{ clients.length }}</p>
                            </div>
                            <div class="bg-white/20 rounded-xl p-3">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 border border-blue-400/30 backdrop-blur-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-100 font-medium">📝 Total Notes</p>
                                <p class="mt-2 text-4xl font-bold">{{ clients.reduce((s, c) => s + c.notes.length, 0) }}</p>
                            </div>
                            <div class="bg-white/20 rounded-xl p-3">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 p-6 text-white shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 border border-green-400/30 backdrop-blur-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-100 font-medium">📧 Avec Email</p>
                                <p class="mt-2 text-4xl font-bold">{{ clients.filter(c => c.email).length }}</p>
                            </div>
                            <div class="bg-white/20 rounded-xl p-3">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-pink-500 to-pink-600 p-6 text-white shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 border border-pink-400/30 backdrop-blur-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-pink-100 font-medium">📱 Avec Téléphone</p>
                                <p class="mt-2 text-4xl font-bold">{{ clients.filter(c => c.phone).length }}</p>
                            </div>
                            <div class="bg-white/20 rounded-xl p-3">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="relative flex-1 sm:max-w-md">
                        <svg class="absolute left-4 top-4 h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="🔍 Rechercher un client..."
                            class="block w-full rounded-xl border-gray-300 py-3.5 pl-11 pr-11 shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800/50 dark:text-gray-100 backdrop-blur-sm transition-all duration-200"
                        />
                        <button v-if="searchQuery" @click="searchQuery = ''" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <button @click="openCreateModal" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-xl font-bold text-sm text-white shadow-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouveau Client
                    </button>
                </div>

                <div v-if="searchQuery" class="mb-4 px-2 text-sm font-medium text-purple-600 dark:text-purple-400">
                    📊 {{ filteredClients.length }} résultat{{ filteredClients.length > 1 ? 's' : '' }}
                </div>

                <!-- Clients Grid -->
                <div v-if="filteredClients.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="client in filteredClients"
                        :key="client.id"
                        class="overflow-hidden rounded-2xl bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 shadow-xl hover:shadow-2xl transform hover:scale-[1.02] transition-all duration-300 border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl"
                    >
                        <!-- Header avec Avatar -->
                        <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 dark:from-purple-500/20 dark:to-pink-500/20 p-6 border-b border-purple-200/30 dark:border-purple-500/20">
                            <div class="flex items-center gap-3">
                                <div
                                    :class="[getAvatarColor(client.first_name, client.last_name), 'flex h-12 w-12 items-center justify-center rounded-full text-lg font-bold text-white shadow-lg']"
                                >
                                    {{ getInitials(client.first_name, client.last_name) }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">
                                        {{ client.first_name }} {{ client.last_name }}
                                    </h3>
                                    <span class="inline-flex items-center text-xs bg-gradient-to-r from-purple-500 to-pink-600 text-white px-3 py-1 rounded-full font-semibold shadow-md">
                                        📝 {{ client.notes.length }} note{{ client.notes.length > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Infos Contact -->
                        <div class="p-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-md">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ client.email || '🚫 Pas d\'email' }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-md">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ client.phone || '🚫 Pas de téléphone' }}</p>
                            </div>
                            <div v-if="client.notes.length > 0" class="rounded-xl bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 p-4 border border-blue-200/30 dark:border-blue-500/20">
                                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 mb-2">📝 Dernière note</p>
                                <p class="line-clamp-2 text-xs text-gray-700 dark:text-gray-300">{{ client.notes[0].content }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-purple-200/30 dark:border-purple-500/20 px-6 py-4 flex gap-3">
                            <button
                                @click="openNotesModal(client)"
                                class="flex-1 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                📝 Notes
                            </button>
                            <button
                                @click="openEditModal(client)"
                                class="rounded-xl bg-gradient-to-r from-gray-500 to-gray-600 px-4 py-2.5 shadow-lg hover:from-gray-600 hover:to-gray-700 transform hover:scale-105 transition-all duration-200"
                            >
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                @click="deleteClient(client)"
                                class="rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-2.5 shadow-lg hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200"
                            >
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="rounded-2xl bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 shadow-xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl p-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-4 shadow-lg">
                            <svg class="h-16 w-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        {{ searchQuery ? '🔍 Aucun client trouvé' : '👥 Aucun client' }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        {{ searchQuery ? 'Essayez une autre recherche' : 'Commencez par ajouter votre premier client.' }}
                    </p>
                    <button v-if="!searchQuery" @click="openCreateModal" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl shadow-lg hover:from-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajouter un Client
                    </button>
                </div>

                <!-- Modal Create/Edit Client -->
                <div v-if="showClientModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gradient-to-br from-slate-900/80 via-purple-900/80 to-slate-900/80 backdrop-blur-sm transition-opacity" @click="closeClientModal"></div>
                        <div class="inline-block transform overflow-hidden rounded-2xl bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl">
                            <form @submit.prevent="submitClient">
                                <div class="px-6 pt-6 pb-4 sm:p-8 sm:pb-6">
                                    <div class="flex items-center mb-6">
                                        <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 shadow-lg">
                                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                            {{ editingClient ? '✏️ Modifier le client' : '✨ Ajouter un client' }}
                                        </h3>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <InputLabel for="first_name" value="👤 Prénom *" />
                                            <TextInput id="first_name" v-model="clientForm.first_name" type="text" class="mt-1 block w-full rounded-xl shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition-all duration-200" required />
                                            <InputError :message="clientForm.errors.first_name" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="last_name" value="👤 Nom *" />
                                            <TextInput id="last_name" v-model="clientForm.last_name" type="text" class="mt-1 block w-full rounded-xl shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition-all duration-200" required />
                                            <InputError :message="clientForm.errors.last_name" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="email" value="📧 Email" />
                                            <TextInput id="email" v-model="clientForm.email" type="email" class="mt-1 block w-full rounded-xl shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition-all duration-200" />
                                            <InputError :message="clientForm.errors.email" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="phone" value="📱 Téléphone" />
                                            <TextInput id="phone" v-model="clientForm.phone" type="text" class="mt-1 block w-full rounded-xl shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition-all duration-200" />
                                            <InputError :message="clientForm.errors.phone" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="vat_number" value="💼 N° TVA" />
                                            <TextInput id="vat_number" v-model="clientForm.vat_number" type="text" class="mt-1 block w-full rounded-xl shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 transition-all duration-200" />
                                            <InputError :message="clientForm.errors.vat_number" class="mt-2" />
                                        </div>
                                        <div>
                                            <InputLabel for="address" value="🏠 Adresse" />
                                            <textarea
                                                id="address"
                                                v-model="clientForm.address"
                                                rows="2"
                                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-md focus:border-purple-500 focus:ring-2 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-700/50 dark:text-gray-300 backdrop-blur-sm transition-all duration-200"
                                            ></textarea>
                                            <InputError :message="clientForm.errors.address" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-r from-purple-50/50 to-pink-50/50 dark:from-purple-900/20 dark:to-pink-900/20 px-6 py-4 sm:px-8 sm:flex sm:flex-row-reverse gap-3 border-t border-purple-200/30 dark:border-purple-500/20">
                                    <button type="submit" :disabled="clientForm.processing" class="w-full inline-flex justify-center items-center rounded-xl border border-transparent shadow-lg px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-base font-bold text-white hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200">
                                        <svg v-if="clientForm.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ clientForm.processing ? 'Enregistrement...' : (editingClient ? '✨ Mettre à jour' : '✨ Ajouter') }}
                                    </button>
                                    <button type="button" @click="closeClientModal" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-md px-6 py-3 bg-white dark:bg-gray-700 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto transition-all duration-200">
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
                        <div class="fixed inset-0 bg-gradient-to-br from-slate-900/80 via-blue-900/80 to-slate-900/80 backdrop-blur-sm transition-opacity" @click="closeNotesModal"></div>
                        <div class="inline-block transform overflow-hidden rounded-2xl bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl">
                            <div class="px-6 pt-6 pb-4 sm:p-8">
                                <div class="mb-6 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 shadow-lg">
                                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                                            📝 Notes pour {{ selectedClient.first_name }} {{ selectedClient.last_name }}
                                        </h3>
                                    </div>
                                    <button @click="closeNotesModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <form @submit.prevent="submitNote" class="mb-6 rounded-2xl bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-blue-900/20 dark:to-purple-900/20 p-6 border border-blue-200/30 dark:border-blue-500/20">
                                    <InputLabel for="note_content" :value="editingNote ? '✏️ Modifier la note' : '✨ Ajouter une note'" class="text-base font-semibold" />
                                    <textarea
                                        id="note_content"
                                        v-model="noteForm.content"
                                        rows="4"
                                        class="mt-2 block w-full rounded-xl border-gray-300 shadow-md focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-700/50 dark:text-gray-300 backdrop-blur-sm transition-all duration-200"
                                        placeholder="📝 Écrivez votre note ici..."
                                        required
                                    ></textarea>
                                    <InputError :message="noteForm.errors.content" class="mt-2" />
                                    <div class="mt-4 flex gap-3">
                                        <button type="submit" :disabled="noteForm.processing" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 border border-transparent rounded-xl font-bold text-sm text-white shadow-lg hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg v-if="noteForm.processing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <svg v-else class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ noteForm.processing ? 'Enregistrement...' : (editingNote ? '✨ Mettre à jour' : '✨ Ajouter') }}
                                        </button>
                                        <button v-if="editingNote" type="button" @click="cancelEditNote" class="inline-flex items-center px-6 py-3 rounded-xl border border-gray-300 dark:border-gray-600 shadow-md bg-white dark:bg-gray-700 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            📚 Historique ({{ selectedClient.notes.length }})
                                        </h4>
                                    </div>
                                    <div v-if="selectedClient.notes.length === 0" class="rounded-2xl bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-700 dark:to-blue-900/20 p-8 text-center border border-blue-200/30 dark:border-blue-500/20">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">💭 Aucune note pour ce client.</p>
                                    </div>
                                    <div v-for="note in selectedClient.notes" :key="note.id" class="rounded-2xl border border-blue-200/50 dark:border-blue-500/30 bg-gradient-to-br from-white to-blue-50/30 dark:from-gray-700 dark:to-blue-900/10 p-5 shadow-md hover:shadow-lg transition-all duration-200 backdrop-blur-sm">
                                        <div class="mb-3 flex items-start justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                    📅 {{ formatDate(note.created_at) }}
                                                </span>
                                            </div>
                                            <div class="flex gap-2">
                                                <button @click="openEditNote(note)" class="px-3 py-1 rounded-lg text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">✏️ Modifier</button>
                                                <button @click="deleteNote(note)" class="px-3 py-1 rounded-lg text-xs font-semibold text-red-600 hover:text-red-800 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">🗑️ Supprimer</button>
                                            </div>
                                        </div>
                                        <p class="whitespace-pre-wrap text-sm text-gray-900 dark:text-gray-100 leading-relaxed">{{ note.content }}</p>
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
