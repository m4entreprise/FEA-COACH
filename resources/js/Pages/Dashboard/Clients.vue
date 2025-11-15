<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    clients: Array,
});

const showClientModal = ref(false);
const showNoteModal = ref(false);
const selectedClient = ref(null);
const isEditing = ref(false);
const selectedNote = ref(null);
const showClientDetails = ref(null);

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

const openCreateClientModal = () => {
    clientForm.reset();
    isEditing.value = false;
    showClientModal.value = true;
};

const openEditClientModal = (client) => {
    Object.assign(clientForm, client);
    selectedClient.value = client;
    isEditing.value = true;
    showClientModal.value = true;
};

const submitClient = () => {
    const method = isEditing.value ? 'patch' : 'post';
    const url = isEditing.value 
        ? route('dashboard.clients.update', selectedClient.value.id)
        : route('dashboard.clients.store');
    
    clientForm[method](url, {
        onSuccess: () => {
            showClientModal.value = false;
            clientForm.reset();
        },
    });
};

const deleteClient = (client) => {
    if (confirm(`Supprimer ${client.first_name} ${client.last_name} et toutes ses notes ?`)) {
        router.delete(route('dashboard.clients.destroy', client.id));
    }
};

const toggleClientDetails = (clientId) => {
    showClientDetails.value = showClientDetails.value === clientId ? null : clientId;
};

const openAddNoteModal = (client) => {
    noteForm.reset();
    selectedClient.value = client;
    selectedNote.value = null;
    showNoteModal.value = true;
};

const openEditNoteModal = (client, note) => {
    noteForm.content = note.content;
    selectedClient.value = client;
    selectedNote.value = note;
    showNoteModal.value = true;
};

const submitNote = () => {
    if (selectedNote.value) {
        noteForm.patch(route('dashboard.clients.notes.update', selectedNote.value.id), {
            onSuccess: () => {
                showNoteModal.value = false;
                noteForm.reset();
            },
        });
    } else {
        noteForm.post(route('dashboard.clients.notes.store', selectedClient.value.id), {
            onSuccess: () => {
                showNoteModal.value = false;
                noteForm.reset();
            },
        });
    }
};

const deleteNote = (note) => {
    if (confirm('Supprimer cette note ?')) {
        router.delete(route('dashboard.clients.notes.destroy', note.id));
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Mes Clients" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">👥 Mes Clients</h2>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">Gérez vos clients et leurs informations</p>
                        </div>
                        <button @click="openCreateClientModal" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg">
                            + Nouveau Client
                        </button>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total clients</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ clients.length }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total notes</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ clients.reduce((sum, c) => sum + c.notes.length, 0) }}
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Avec email</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ clients.filter(c => c.email).length }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="clients.length > 0" class="space-y-4">
                    <div v-for="client in clients" :key="client.id" class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <button @click="toggleClientDetails(client.id)" class="text-left flex-1 group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                            {{ client.first_name[0] }}{{ client.last_name[0] }}
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-purple-600 transition">
                                                {{ client.first_name }} {{ client.last_name }}
                                            </h3>
                                            <div class="flex gap-4 mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                <span v-if="client.email">📧 {{ client.email }}</span>
                                                <span v-if="client.phone">📞 {{ client.phone }}</span>
                                                <span>💬 {{ client.notes.length }} note{{ client.notes.length > 1 ? 's' : '' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </button>

                                <div class="flex gap-2 ml-4">
                                    <button @click="openAddNoteModal(client)" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg" title="Ajouter note">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="openEditClientModal(client)" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteClient(client)" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-show="showClientDetails === client.id" class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 p-6 space-y-6">
                            <div v-if="client.address || client.vat_number" class="grid md:grid-cols-2 gap-4">
                                <div v-if="client.address">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Adresse</label>
                                    <p class="mt-1 text-gray-900 dark:text-white whitespace-pre-line">{{ client.address }}</p>
                                </div>
                                <div v-if="client.vat_number">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">N° TVA</label>
                                    <p class="mt-1 text-gray-900 dark:text-white">{{ client.vat_number }}</p>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">📝 Notes ({{ client.notes.length }})</h4>
                                    <button @click="openAddNoteModal(client)" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        + Ajouter
                                    </button>
                                </div>

                                <div v-if="client.notes.length > 0" class="space-y-3">
                                    <div v-for="note in client.notes" :key="note.id" class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                        <div class="flex justify-between gap-4">
                                            <div class="flex-1">
                                                <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ note.content }}</p>
                                                <p class="mt-2 text-xs text-gray-500">{{ formatDate(note.created_at) }}</p>
                                            </div>
                                            <div class="flex gap-2">
                                                <button @click="openEditNoteModal(client, note)" class="p-1 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteNote(note)" class="p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8 text-gray-500">Aucune note</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <div class="text-6xl mb-4">👥</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aucun client</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Ajoutez vos clients pour centraliser leurs infos</p>
                    <button @click="openCreateClientModal" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700">
                        Ajouter mon premier client
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Client -->
        <div v-if="showClientModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="showClientModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ isEditing ? 'Modifier le client' : 'Nouveau client' }}
                    </h3>

                    <form @submit.prevent="submitClient" class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prénom *</label>
                                <input v-model="clientForm.first_name" type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500" required />
                                <InputError :message="clientForm.errors.first_name" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom *</label>
                                <input v-model="clientForm.last_name" type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500" required />
                                <InputError :message="clientForm.errors.last_name" class="mt-1" />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input v-model="clientForm.email" type="email" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500" />
                                <InputError :message="clientForm.errors.email" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone</label>
                                <input v-model="clientForm.phone" type="tel" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500" />
                                <InputError :message="clientForm.errors.phone" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse</label>
                            <textarea v-model="clientForm.address" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500"></textarea>
                            <InputError :message="clientForm.errors.address" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">N° TVA</label>
                            <input v-model="clientForm.vat_number" type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500" />
                            <InputError :message="clientForm.errors.vat_number" class="mt-1" />
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showClientModal = false" class="px-6 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Annuler
                            </button>
                            <button type="submit" :disabled="clientForm.processing" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 disabled:opacity-50">
                                {{ isEditing ? 'Enregistrer' : 'Créer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Note -->
        <div v-if="showNoteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="showNoteModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ selectedNote ? 'Modifier la note' : 'Nouvelle note' }}
                    </h3>

                    <form @submit.prevent="submitNote">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note *</label>
                            <textarea v-model="noteForm.content" rows="6" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500" required></textarea>
                            <InputError :message="noteForm.errors.content" class="mt-1" />
                            <div class="mt-1 text-xs text-gray-500">{{ noteForm.content.length }} / 5000 caractères</div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showNoteModal = false" class="px-6 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Annuler
                            </button>
                            <button type="submit" :disabled="noteForm.processing" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 disabled:opacity-50">
                                {{ selectedNote ? 'Modifier' : 'Ajouter' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
