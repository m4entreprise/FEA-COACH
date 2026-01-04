<script setup>
import { Head, router } from '@inertiajs/vue3';
import { Search, UserPlus, TrendingUp, MessageSquare, Users, Calendar } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

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
      (c.email && c.email.toLowerCase().includes(q))
    );
  });
});

const getInitials = (f, l) => (f.charAt(0) + l.charAt(0)).toUpperCase();
const getAvatarColor = (f, l) => {
  const colors = [
    'from-purple-500 to-purple-600',
    'from-blue-500 to-blue-600',
    'from-green-500 to-green-600',
    'from-yellow-500 to-yellow-600',
    'from-pink-500 to-pink-600',
    'from-indigo-500 to-indigo-600',
    'from-red-500 to-red-600',
    'from-teal-500 to-teal-600',
  ];
  return colors[(f.charCodeAt(0) + l.charCodeAt(0)) % colors.length];
};

const dashboardBackUrl = computed(() => {
  if (typeof window === 'undefined') return route('dashboard');
  const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
  return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
  router.visit(dashboardBackUrl.value);
};

const openClientDashboard = (clientId) => {
  router.visit(route('dashboard.clients.manage', clientId));
};

const getUnreadMessagesCount = (client) => {
  return client.messages?.filter(m => m.sender_type === 'client' && !m.is_read).length || 0;
};

const getLatestWeight = (client) => {
  if (!client.measurements || client.measurements.length === 0) return null;
  const sorted = [...client.measurements].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  return sorted[0].weight;
};

const getBookingCount = (client) => client.bookings?.length || 0;

const hasRecentBooking = (client) => {
  if (!client.bookings || client.bookings.length === 0) return false;
  const latest = new Date(client.bookings[0].created_at);
  const diffHours = (Date.now() - latest.getTime()) / (1000 * 60 * 60);
  return diffHours <= 24;
};

// Client creation modal
const showClientModal = ref(false);
const clientForm = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  address: '',
  vat_number: '',
});

const openCreateModal = () => {
  clientForm.reset();
  showClientModal.value = true;
};

const closeClientModal = () => {
  showClientModal.value = false;
  clientForm.reset();
};

const submitClient = () => {
  clientForm.post(route('dashboard.clients.store'), {
    preserveScroll: true,
    onSuccess: () => {
      closeClientModal();
    },
  });
};

</script>

<template>
  <Head title="Mes Clients" />

  <div class="min-h-screen bg-slate-950 text-slate-50">
    <!-- Top bar -->
    <header
      class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl sticky top-0 z-30"
    >
      <div class="flex items-center gap-3">
        <div class="flex flex-col">
          <p class="text-xs uppercase tracking-wide text-slate-400">
            Panel coach
          </p>
          <h1 class="text-base md:text-lg font-semibold flex items-center gap-2">
            <Users class="h-5 w-5" />
            <span>Mes Clients</span>
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="goBack"
          class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs font-medium text-slate-100 hover:border-slate-500 hover:bg-slate-800"
        >
          <span class="text-xs">←</span>
          <span>Retour panel</span>
        </button>
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
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
              @click="openCreateModal"
            >
              <UserPlus class="h-3.5 w-3.5" />
              <span>Nouveau client</span>
            </button>
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
                <div class="mb-3 px-3 py-2 bg-indigo-500/20 border border-indigo-500/30 rounded-lg">
                  <p class="text-[10px] uppercase text-indigo-300 mb-1">Code élève</p>
                  <p class="text-lg font-bold text-indigo-100 tracking-wider">{{ client.share_code }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div class="rounded-lg bg-slate-800/50 p-3 border border-slate-700">
                    <p class="text-[10px] uppercase text-slate-400 mb-1">Poids actuel</p>
                    <p class="text-lg font-bold text-slate-50">
                      {{ getLatestWeight(client) ? getLatestWeight(client) + ' kg' : '—' }}
                    </p>
                  </div>
                  <div class="rounded-lg bg-slate-800/50 p-3 border border-slate-700">
                    <p class="text-[10px] uppercase text-slate-400 mb-1">Messages</p>
                    <p class="text-lg font-bold text-slate-50 flex items-center gap-1.5">
                      <MessageSquare class="h-4 w-4" />
                      {{ getUnreadMessagesCount(client) }}
                    </p>
                  </div>
                </div>

                <div class="rounded-lg bg-gradient-to-br from-slate-800/50 to-slate-800/30 p-3 border border-slate-700">
                  <p class="text-[10px] uppercase text-slate-400 mb-1">Progression</p>
                  <div class="flex items-center gap-2">
                    <TrendingUp class="h-4 w-4 text-emerald-400" />
                    <p class="text-sm font-semibold text-slate-200">
                      {{ client.measurements?.length || 0 }} relevés
                    </p>
                  </div>
                </div>
                <div class="pt-2">
                  <button
                    type="button"
                    class="w-full rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 transition-all"
                    @click="openClientDashboard(client.id)"
                  >
                    Gérer ce client →
                  </button>
                </div>
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
                  : 'Ajoutez vos premiers clients pour commencer le coaching personnalisé.'
              }}
            </p>
          </div>
        </section>
      </div>
    </main>

    <!-- Client Creation Modal -->
    <div
      v-if="showClientModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
      @click="closeClientModal"
    >
      <div
        class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl"
        @click.stop
      >
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-slate-100">Nouveau client</h2>
          <button
            type="button"
            class="text-slate-400 hover:text-slate-200 text-xl"
            @click="closeClientModal"
          >
            ✕
          </button>
        </div>

        <form @submit.prevent="submitClient" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="first_name" value="Prénom *" class="text-slate-200" />
              <TextInput
                id="first_name"
                v-model="clientForm.first_name"
                type="text"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
                required
              />
              <InputError class="mt-1" :message="clientForm.errors.first_name" />
            </div>

            <div>
              <InputLabel for="last_name" value="Nom *" class="text-slate-200" />
              <TextInput
                id="last_name"
                v-model="clientForm.last_name"
                type="text"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
                required
              />
              <InputError class="mt-1" :message="clientForm.errors.last_name" />
            </div>
          </div>

          <div>
            <InputLabel for="email" value="Email" class="text-slate-200" />
            <TextInput
              id="email"
              v-model="clientForm.email"
              type="email"
              class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
            />
            <InputError class="mt-1" :message="clientForm.errors.email" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <InputLabel for="phone" value="Téléphone" class="text-slate-200" />
              <TextInput
                id="phone"
                v-model="clientForm.phone"
                type="text"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
              />
              <InputError class="mt-1" :message="clientForm.errors.phone" />
            </div>

            <div>
              <InputLabel for="vat_number" value="N° TVA" class="text-slate-200" />
              <TextInput
                id="vat_number"
                v-model="clientForm.vat_number"
                type="text"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-100"
              />
              <InputError class="mt-1" :message="clientForm.errors.vat_number" />
            </div>
          </div>

          <div>
            <InputLabel for="address" value="Adresse" class="text-slate-200" />
            <textarea
              id="address"
              v-model="clientForm.address"
              rows="3"
              class="mt-1 block w-full rounded-lg border-slate-700 bg-slate-950 text-slate-100 placeholder-slate-500 focus:border-purple-500 focus:ring-purple-500"
            ></textarea>
            <InputError class="mt-1" :message="clientForm.errors.address" />
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              class="rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-200 hover:bg-slate-800 transition-colors"
              @click="closeClientModal"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-sm font-semibold text-white hover:from-purple-600 hover:to-pink-600 disabled:opacity-50 transition-all"
              :disabled="clientForm.processing"
            >
              {{ clientForm.processing ? 'Création...' : 'Créer le client' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
