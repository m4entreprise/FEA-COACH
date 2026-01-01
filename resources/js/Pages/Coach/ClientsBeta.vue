<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Search, UserPlus, Users, FileText, Mail, Phone } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

const props = defineProps({
  clients: Array,
});

const searchQuery = ref('');
const filteredClients = computed(() => {
  if (!searchQuery.value) return props.clients;
  const q = searchQuery.value.toLowerCase();
  return props.clients.filter((c) => {
    return (
      c.first_name.toLowerCase().includes(q) ||
      c.last_name.toLowerCase().includes(q) ||
      (c.email && c.email.toLowerCase().includes(q)) ||
      (c.phone && c.phone.includes(q))
    );
  });
});

const getInitials = (f, l) => (f.charAt(0) + l.charAt(0)).toUpperCase();
const getAvatarColor = (f, l) => {
  const colors = [
    'bg-purple-500',
    'bg-blue-500',
    'bg-green-500',
    'bg-yellow-500',
    'bg-pink-500',
    'bg-indigo-500',
    'bg-red-500',
    'bg-teal-500',
  ];
  return colors[(f.charCodeAt(0) + l.charCodeAt(0)) % colors.length];
};

const showClientModal = ref(false);
const showNotesModal = ref(false);
const editingClient = ref(null);
const selectedClient = ref(null);
const editingNote = ref(null);
const previewHtml = ref('');
const previewLoading = ref(false);
const previewError = ref(null);
const isPreviewFullscreen = ref(false);

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
    clientForm.patch(
      route('dashboard.clients.update', {
        client: editingClient.value.id,
        beta: 1,
      }),
      {
        preserveScroll: true,
        onSuccess: () => closeClientModal(),
      },
    );
  } else {
    clientForm.post(route('dashboard.clients.store', { beta: 1 }), {
      preserveScroll: true,
      onSuccess: () => closeClientModal(),
    });
  }
};

const deleteClient = (client) => {
  if (!confirm(`Supprimer ${client.first_name} ${client.last_name} ?`)) {
    return;
  }

  router.delete(
    route('dashboard.clients.destroy', { client: client.id, beta: 1 }),
    {
      preserveScroll: true,
    },
  );
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
  if (!selectedClient.value) return;

  if (editingNote.value) {
    noteForm.patch(
      route('dashboard.clients.notes.update', {
        note: editingNote.value.id,
        beta: 1,
      }),
      {
        preserveScroll: true,
        onSuccess: () => {
          editingNote.value = null;
          noteForm.reset();
          router.reload({ only: ['clients'] });
        },
      },
    );
  } else {
    noteForm.post(
      route('dashboard.clients.notes.store', {
        client: selectedClient.value.id,
        beta: 1,
      }),
      {
        preserveScroll: true,
        onSuccess: () => {
          noteForm.reset();
          router.reload({ only: ['clients'] });
        },
      },
    );
  }
};

const deleteNote = (note) => {
  if (!confirm('Supprimer cette note ?')) {
    return;
  }

  router.delete(
    route('dashboard.clients.notes.destroy', { note: note.id, beta: 1 }),
    {
      preserveScroll: true,
      onSuccess: () => router.reload({ only: ['clients'] }),
    },
  );
};

const formatDate = (d) =>
  new Date(d).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });

const fetchPreview = async () => {
  previewLoading.value = true;
  previewError.value = null;

  try {
    const { data } = await axios.post(
      route('dashboard.clients.preview', { beta: 1 }),
      {},
      {
        headers: { Accept: 'application/json' },
        withCredentials: true,
      },
    );

    previewHtml.value = data.html;
  } catch (error) {
    previewError.value =
      error.response?.data?.message || "Impossible de générer l’aperçu pour le moment.";
  } finally {
    previewLoading.value = false;
  }
};

const openPreview = () => {
  isPreviewFullscreen.value = true;
  fetchPreview();
};

const closePreview = () => {
  isPreviewFullscreen.value = false;
};

watch(isPreviewFullscreen, (active) => {
  document.body.classList.toggle('overflow-hidden', active);
});
</script>

<template>
  <Head title="Clients (beta)" />

  <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
    <!-- Top bar -->
    <header
      class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl"
    >
      <div class="flex items-center gap-3">
        <div class="flex flex-col">
          <p class="text-xs uppercase tracking-wide text-slate-400">
            Panel coach beta
          </p>
          <h1 class="text-base md:text-lg font-semibold flex items-center gap-2">
            <span>Clients</span>
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <a
          :href="route('dashboard.coach.beta')"
          class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs font-medium text-slate-100 hover:border-slate-500 hover:bg-slate-800"
        >
          <span class="text-xs">←</span>
          <span>Retour panel</span>
        </a>
      </div>
    </header>

    <!-- Main content -->
    <main
      class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8"
    >
      <div class="max-w-6xl mx-auto space-y-6">
        <!-- Stats & search -->
        <section class="space-y-4">
          <div
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
          >
            <div>
              <h2 class="text-lg font-semibold">Gestion des clients</h2>
              <p class="text-sm text-slate-400">
                Centralisez prospects, clients et notes pour suivre vos séances.
              </p>
            </div>
            <div class="flex flex-wrap gap-2">
              <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-4 py-2 text-xs font-semibold text-slate-50 hover:border-indigo-400 hover:bg-slate-800"
                @click="openPreview"
              >
                <Search class="h-3.5 w-3.5" />
                <span>Aperçu plein écran</span>
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
                @click="openCreateModal"
              >
                <UserPlus class="h-3.5 w-3.5" />
                <span>Nouveau client</span>
              </button>
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
              class="rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 p-4 text-white shadow-xl border border-purple-400/40"
            >
              <p class="text-xs text-purple-100 font-medium">Total clients</p>
              <p class="mt-2 text-2xl font-bold">{{ clients.length }}</p>
            </div>
            <div
              class="rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-4 text-white shadow-xl border border-blue-400/40"
            >
              <p class="text-xs text-blue-100 font-medium">Total notes</p>
              <p class="mt-2 text-2xl font-bold">
                {{ clients.reduce((s, c) => s + c.notes.length, 0) }}
              </p>
            </div>
            <div
              class="rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 p-4 text-white shadow-xl border border-emerald-400/40"
            >
              <p class="text-xs text-emerald-100 font-medium">Avec email</p>
              <p class="mt-2 text-2xl font-bold">
                {{ clients.filter((c) => c.email).length }}
              </p>
            </div>
            <div
              class="rounded-2xl bg-gradient-to-br from-pink-500 to-pink-600 p-4 text-white shadow-xl border border-pink-400/40"
            >
              <p class="text-xs text-pink-100 font-medium">Avec téléphone</p>
              <p class="mt-2 text-2xl font-bold">
                {{ clients.filter((c) => c.phone).length }}
              </p>
            </div>
          </div>

          <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
          >
            <div class="relative flex-1 sm:max-w-md">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher un client..."
                class="block w-full rounded-full border border-slate-700 bg-slate-950 py-2.5 pl-10 pr-10 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
              />
              <Search
                class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-500"
              />
              <button
                v-if="searchQuery"
                type="button"
                class="absolute right-3 top-2.5 text-slate-500 hover:text-slate-200 text-xs"
                @click="searchQuery = ''"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 self-start"
              @click="openCreateModal"
            >
              <UserPlus class="h-3.5 w-3.5" />
              <span>Nouveau client</span>
            </button>
          </div>
        </section>

        <!-- Clients grid -->
        <section>
          <div
            v-if="filteredClients && filteredClients.length"
            class="grid gap-5 md:grid-cols-2 lg:grid-cols-3"
          >
            <article
              v-for="client in filteredClients"
              :key="client.id"
              class="overflow-hidden rounded-2xl bg-slate-900/80 shadow-xl border border-slate-800"
            >
              <div
                class="bg-slate-900/90 border-b border-slate-800 p-4 flex items-center gap-3"
              >
                <div
                  :class="[
                    getAvatarColor(client.first_name, client.last_name),
                    'flex h-10 w-10 items-center justify-center rounded-full text-xs font-bold text-white shadow-lg',
                  ]"
                >
                  {{ getInitials(client.first_name, client.last_name) }}
                </div>
                <div class="flex-1">
                  <h3 class="text-sm font-semibold text-slate-50">
                    {{ client.first_name }} {{ client.last_name }}
                  </h3>
                  <p class="text-[11px] text-slate-400">
                    {{ client.email || 'Pas d\'email' }}
                  </p>
                </div>
              </div>

              <div class="p-4 space-y-3 text-xs">
                <p class="flex items-center gap-2 text-slate-300">
                  <Phone class="h-4 w-4 text-slate-500" />
                  <span>{{ client.phone || 'Pas de téléphone' }}</span>
                </p>
                <p v-if="client.vat_number" class="text-slate-400">
                  <span class="font-semibold">TVA :</span>
                  <span class="ml-1">{{ client.vat_number }}</span>
                </p>
                <p v-if="client.address" class="text-slate-400">
                  <span class="font-semibold">Adresse :</span>
                  <span class="ml-1">{{ client.address }}</span>
                </p>
                <p
                  v-if="client.notes.length"
                  class="rounded-xl bg-slate-800/80 border border-slate-700 p-3 text-[11px] text-slate-200"
                >
                  <span class="font-semibold">Dernière note :</span>
                  <br />
                  {{ client.notes[0].content }}
                </p>
              </div>

              <div
                class="border-t border-slate-800 px-4 py-3 flex gap-2 text-[11px]"
              >
                <button
                  type="button"
                  class="flex-1 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 px-3 py-1.5 text-slate-50 hover:from-blue-600 hover:to-cyan-600"
                  @click="openNotesModal(client)"
                >
                  Notes
                </button>
                <button
                  type="button"
                  class="rounded-full bg-slate-800 px-3 py-1.5 text-slate-100 hover:bg-slate-700"
                  @click="openEditModal(client)"
                >
                  Modifier
                </button>
                <button
                  type="button"
                  class="rounded-full bg-gradient-to-r from-rose-500 to-rose-600 px-3 py-1.5 text-slate-50 hover:from-rose-600 hover:to-rose-700"
                  @click="deleteClient(client)"
                >
                  Supprimer
                </button>
              </div>
            </article>
          </div>

          <div
            v-else
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-10 text-center text-slate-100 shadow-xl"
          >
            <div class="flex justify-center mb-4">
              <div
                class="h-14 w-14 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg"
              >
                <Users class="h-7 w-7 text-white" />
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">
              {{ searchQuery ? 'Aucun client trouvé' : 'Aucun client' }}
            </h3>
            <p class="text-xs text-slate-400 mb-4">
              {{
                searchQuery
                  ? 'Essayez une autre recherche.'
                  : 'Ajoutez vos premiers clients pour suivre vos prospects et clients actuels.'
              }}
            </p>
            <PrimaryButton
              v-if="!searchQuery"
              type="button"
              class="text-xs"
              @click="openCreateModal"
            >
              <span class="mr-1">+</span>
              Ajouter un client
            </PrimaryButton>
          </div>
        </section>
      </div>

      <!-- Modal client create/edit -->
      <div
        v-if="showClientModal"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
      >
        <div
          class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl"
        >
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold">
              {{ editingClient ? 'Modifier le client' : 'Nouveau client' }}
            </h2>
            <button
              type="button"
              class="text-slate-400 hover:text-slate-200 text-sm"
              @click="closeClientModal"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="submitClient" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <InputLabel
                  for="client_first_name"
                  value="Prénom *"
                  class="text-xs text-slate-200"
                />
                <TextInput
                  id="client_first_name"
                  v-model="clientForm.first_name"
                  type="text"
                  class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                  required
                />
                <InputError
                  class="mt-1 text-[11px]"
                  :message="clientForm.errors.first_name"
                />
              </div>
              <div>
                <InputLabel
                  for="client_last_name"
                  value="Nom *"
                  class="text-xs text-slate-200"
                />
                <TextInput
                  id="client_last_name"
                  v-model="clientForm.last_name"
                  type="text"
                  class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                  required
                />
                <InputError
                  class="mt-1 text-[11px]"
                  :message="clientForm.errors.last_name"
                />
              </div>
            </div>

            <div>
              <InputLabel
                for="client_email"
                value="Email"
                class="text-xs text-slate-200"
              />
              <TextInput
                id="client_email"
                v-model="clientForm.email"
                type="email"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
              />
              <InputError
                class="mt-1 text-[11px]"
                :message="clientForm.errors.email"
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <InputLabel
                  for="client_phone"
                  value="Téléphone"
                  class="text-xs text-slate-200"
                />
                <TextInput
                  id="client_phone"
                  v-model="clientForm.phone"
                  type="text"
                  class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                />
                <InputError
                  class="mt-1 text-[11px]"
                  :message="clientForm.errors.phone"
                />
              </div>
              <div>
                <InputLabel
                  for="client_vat"
                  value="N° TVA"
                  class="text-xs text-slate-200"
                />
                <TextInput
                  id="client_vat"
                  v-model="clientForm.vat_number"
                  type="text"
                  class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                />
                <InputError
                  class="mt-1 text-[11px]"
                  :message="clientForm.errors.vat_number"
                />
              </div>
            </div>

            <div>
              <InputLabel
                for="client_address"
                value="Adresse"
                class="text-xs text-slate-200"
              />
              <textarea
                id="client_address"
                v-model="clientForm.address"
                rows="2"
                class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
              ></textarea>
              <InputError
                class="mt-1 text-[11px]"
                :message="clientForm.errors.address"
              />
            </div>

            <div class="flex justify-end gap-2 pt-2 text-xs">
              <button
                type="button"
                class="rounded-full border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800"
                @click="closeClientModal"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1.5 font-medium text-slate-50 hover:from-purple-600 hover:to-pink-600 disabled:opacity-60"
                :disabled="clientForm.processing"
              >
                {{ clientForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Modal notes -->
      <div
        v-if="showNotesModal && selectedClient"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
      >
        <div
          class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl"
        >
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold">
              Notes pour {{ selectedClient.first_name }}
              {{ selectedClient.last_name }}
            </h2>
            <button
              type="button"
              class="text-slate-400 hover:text-slate-200 text-sm"
              @click="closeNotesModal"
            >
              ✕
            </button>
          </div>

          <div class="grid gap-4 md:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]">
            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
              <p
                v-if="!selectedClient.notes.length"
                class="text-[11px] text-slate-400"
              >
                Aucune note pour le moment.
              </p>
              <article
                v-for="note in selectedClient.notes"
                :key="note.id"
                class="rounded-xl border border-slate-800 bg-slate-900/80 p-3 text-xs text-slate-100 space-y-1"
              >
                <p class="whitespace-pre-line">
                  {{ note.content }}
                </p>
                <p class="text-[10px] text-slate-500 flex justify-between">
                  <span>{{ formatDate(note.created_at) }}</span>
                </p>
                <div class="flex gap-2 pt-1">
                  <button
                    type="button"
                    class="rounded-full border border-slate-700 px-3 py-1 text-[11px] text-slate-200 hover:bg-slate-800"
                    @click="openEditNote(note)"
                  >
                    Modifier
                  </button>
                  <button
                    type="button"
                    class="rounded-full border border-rose-600/60 bg-rose-600/10 px-3 py-1 text-[11px] text-rose-200 hover:bg-rose-600/20"
                    @click="deleteNote(note)"
                  >
                    Supprimer
                  </button>
                </div>
              </article>
            </div>

            <form
              class="space-y-3 text-xs"
              @submit.prevent="submitNote"
            >
              <InputLabel
                for="note_content"
                :value="
                  editingNote
                    ? 'Modifier la note'
                    : 'Ajouter une note'
                "
                class="text-xs text-slate-200"
              />
              <textarea
                id="note_content"
                v-model="noteForm.content"
                rows="4"
                class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-blue-500 focus:ring-blue-500"
                required
              ></textarea>
              <InputError
                class="mt-1 text-[11px]"
                :message="noteForm.errors.content"
              />

              <div class="flex gap-2 pt-2">
                <button
                  v-if="editingNote"
                  type="button"
                  class="rounded-full border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800"
                  @click="cancelEditNote"
                >
                  Annuler la modification
                </button>
                <button
                  type="submit"
                  class="ml-auto rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 px-4 py-1.5 font-medium text-slate-50 hover:from-blue-600 hover:to-cyan-600 disabled:opacity-60"
                  :disabled="noteForm.processing"
                >
                  {{ noteForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <teleport to="body">
    <div
      v-if="isPreviewFullscreen"
      class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col"
    >
      <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-slate-800 text-slate-200">
        <div>
          <p class="text-xs uppercase tracking-wide text-indigo-300">
            Aperçu clients
          </p>
          <h3 class="text-lg font-semibold">Site public (mise à jour en direct)</h3>
        </div>
        <div class="flex items-center gap-3 text-xs">
          <button
            type="button"
            class="inline-flex items-center gap-1 rounded-full border border-slate-600 px-3 py-1.5 hover:border-slate-400 hover:text-white"
            @click="fetchPreview"
            :disabled="previewLoading"
          >
            <span v-if="previewLoading" class="animate-pulse text-yellow-300">Actualisation…</span>
            <span v-else>Rafraîchir</span>
          </button>
          <button
            type="button"
            class="inline-flex items-center gap-1 rounded-full border border-slate-600 px-3 py-1.5 hover:border-slate-400 hover:text-white"
            @click="closePreview"
          >
            Fermer
          </button>
        </div>
      </div>

      <div class="flex-1 p-4">
        <div
          class="relative h-full rounded-2xl border border-slate-800 bg-slate-950/80 shadow-2xl overflow-hidden"
        >
          <div
            v-if="previewLoading && !previewHtml"
            class="absolute inset-0 flex flex-col items-center justify-center text-slate-200 text-sm gap-3"
          >
            <svg class="h-8 w-8 animate-spin text-indigo-300" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <p>Chargement de l’aperçu...</p>
          </div>
          <div
            v-else-if="previewError"
            class="absolute inset-0 flex flex-col items-center justify-center text-center text-red-300 text-sm px-8 gap-3"
          >
            <p>{{ previewError }}</p>
            <button
              type="button"
              class="text-xs underline decoration-dotted"
              @click="fetchPreview"
            >
              Réessayer
            </button>
          </div>
          <iframe
            v-show="previewHtml"
            class="w-full h-full bg-white"
            sandbox="allow-same-origin allow-forms"
            :srcdoc="previewHtml"
          ></iframe>
        </div>
      </div>
    </div>
  </teleport>
</template>
