<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { LifeBuoy, Plus, MessageSquare, CheckCircle2, XCircle, Send, Trash2 } from 'lucide-vue-next';

const props = defineProps({
  tickets: Array,
});

const showNewTicketModal = ref(false);
const selectedTicketId = ref(null);

const hasTickets = computed(() => props.tickets.length > 0);

const openTickets = computed(() => 
  props.tickets.filter(t => t.status === 'open')
);

const closedTickets = computed(() => 
  props.tickets.filter(t => t.status === 'closed')
);

const dashboardBackUrl = computed(() => {
  if (typeof window === 'undefined') return route('dashboard');
  const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
  return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
  router.visit(dashboardBackUrl.value);
};

const selectedTicket = computed(() => {
  return props.tickets.find((t) => t.id === selectedTicketId.value) || null;
});

const createForm = useForm({
  subject: '',
  category: '',
  message: '',
});

const replyForm = useForm({
  message: '',
});

const selectTicket = (ticketId) => {
  selectedTicketId.value = ticketId;
};

const openNewTicketModal = () => {
  showNewTicketModal.value = true;
  createForm.reset();
  createForm.clearErrors();
};

const closeNewTicketModal = () => {
  showNewTicketModal.value = false;
};

const submitNewTicket = () => {
  createForm.post(route('dashboard.support.store'), {
    preserveScroll: true,
    onSuccess: () => {
      createForm.reset('subject', 'category', 'message');
      closeNewTicketModal();
    },
  });
};

const submitReply = () => {
  if (!selectedTicket.value) return;

  replyForm.post(
    route('dashboard.support.reply', {
      supportTicket: selectedTicket.value.id,
      beta: 1,
    }),
    {
      preserveScroll: true,
      onSuccess: () => {
        replyForm.reset('message');
      },
    },
  );
};

const closeTicket = () => {
  if (!selectedTicket.value) return;

  router.post(
    route('dashboard.support.close', {
      supportTicket: selectedTicket.value.id,
      beta: 1,
    }),
    {},
    {
      preserveScroll: true,
    },
  );
};
</script>

<template>
  <Head title="Support " />

  <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
    <!-- Top bar -->
    <header
      class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl"
    >
      <div class="flex items-center gap-3">
        <div class="flex flex-col">
          <p class="text-xs uppercase tracking-wide text-slate-400">
            Panel coach
          </p>
          <h1 class="text-base md:text-lg font-semibold flex items-center gap-2">
            <span>Support & assistance</span>
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
        <!-- Header -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h2 class="text-lg font-semibold">Assistance UNICOACH</h2>
            <p class="text-sm text-slate-400">
              Ouvrez un ticket ou consultez vos conversations avec l'équipe support.
            </p>
          </div>
          <button
            type="button"
            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
            @click="openNewTicketModal"
          >
            <Plus class="h-3.5 w-3.5" />
            <span>Nouveau ticket</span>
          </button>
        </section>

        <section
          class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl space-y-2"
        >
          <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-300">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-breathe"></span>
              <span>{{ tickets.length }} ticket{{ tickets.length > 1 ? 's' : '' }} au total</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-slate-400">{{ openTickets.length }} ouvert{{ openTickets.length > 1 ? 's' : '' }}</span>
              <span class="text-slate-500">•</span>
              <span class="text-slate-400">{{ closedTickets.length }} clôturé{{ closedTickets.length > 1 ? 's' : '' }}</span>
            </div>
          </div>
        </section>

        <!-- Tickets list -->
        <section class="space-y-4">
          <div v-if="hasTickets" class="space-y-3">
            <article
              v-for="ticket in tickets"
              :key="ticket.id"
              class="rounded-2xl border bg-slate-900/80 p-5 shadow-md transition"
              :class="[
                ticket.status === 'closed'
                  ? 'border-slate-800 hover:border-slate-700 opacity-70'
                  : 'border-slate-800 hover:border-slate-700'
              ]"
            >
              <div class="flex items-start gap-4">
                <div 
                  class="h-10 w-10 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
                  :class="ticket.status === 'open' ? 'bg-gradient-to-br from-sky-500 to-sky-400' : 'bg-gradient-to-br from-slate-600 to-slate-500'"
                >
                  <MessageSquare class="h-4 w-4" />
                </div>
                <div class="flex-1 space-y-3">
                  <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-1">
                      <p class="text-xs uppercase tracking-wide text-slate-500">
                        Ticket #{{ ticket.id }}
                      </p>
                      <h3 class="text-sm md:text-base font-semibold text-slate-50">
                        {{ ticket.subject }}
                      </h3>
                      <p v-if="ticket.category" class="text-xs text-slate-400">
                        Catégorie : {{ ticket.category }}
                      </p>
                    </div>
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold"
                        :class="
                          ticket.status === 'open'
                            ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                            : 'border-slate-700 bg-slate-800 text-slate-300'
                        "
                      >
                        {{ ticket.status === 'open' ? 'Ouvert' : 'Clôturé' }}
                      </span>
                      <span class="text-[11px] text-slate-500">
                        {{ ticket.last_message_at || ticket.created_at }}
                      </span>
                    </div>
                  </div>
                  
                  <!-- Messages preview -->
                  <div v-if="ticket.messages && ticket.messages.length" class="space-y-2">
                    <div
                      v-for="message in ticket.messages.slice(-2)"
                      :key="message.id"
                      class="rounded-xl border border-slate-800 bg-slate-950/70 p-3 text-xs"
                    >
                      <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-slate-200">
                          {{ message.is_from_admin ? 'Support UNICOACH' : 'Vous' }}
                        </span>
                        <span class="text-[10px] text-slate-500">
                          {{ message.created_at }}
                        </span>
                      </div>
                      <p class="text-slate-300 whitespace-pre-line">
                        {{ message.message }}
                      </p>
                    </div>
                  </div>

                  <!-- Reply section -->
                  <div v-if="selectedTicketId === ticket.id" class="space-y-3 pt-3 border-t border-slate-800">
                    <div class="flex items-end gap-2">
                      <textarea
                        v-model="replyForm.message"
                        rows="2"
                        class="flex-1 rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-sky-500 focus:ring-sky-500"
                        :placeholder="
                          ticket.status === 'open'
                            ? 'Écrivez votre réponse...'
                            : 'Ticket clôturé - votre message le rouvrira automatiquement.'
                        "
                      ></textarea>
                      <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-sky-600 hover:to-cyan-600 disabled:opacity-60"
                        :disabled="replyForm.processing || !replyForm.message"
                        @click="submitReply"
                      >
                        <Send class="h-3 w-3" />
                        {{ replyForm.processing ? 'Envoi...' : 'Envoyer' }}
                      </button>
                    </div>
                  </div>

                  <div class="flex flex-wrap justify-end gap-2 text-[11px]">
                    <button
                      v-if="selectedTicketId !== ticket.id"
                      type="button"
                      class="inline-flex items-center gap-1 rounded-full border border-slate-700 px-4 py-1.5 text-slate-200 hover:bg-slate-800"
                      @click="selectTicket(ticket.id)"
                    >
                      <MessageSquare class="h-3 w-3" />
                      Répondre
                    </button>
                    <button
                      v-else
                      type="button"
                      class="inline-flex items-center gap-1 rounded-full border border-slate-700 px-4 py-1.5 text-slate-200 hover:bg-slate-800"
                      @click="selectedTicketId = null"
                    >
                      Réduire
                    </button>
                    <button
                      v-if="ticket.status === 'open'"
                      type="button"
                      class="inline-flex items-center gap-1 rounded-full border border-amber-500/50 bg-amber-500/10 px-4 py-1.5 text-amber-200 hover:bg-amber-500/20"
                      @click="selectedTicketId = ticket.id; closeTicket();"
                    >
                      <CheckCircle2 class="h-3 w-3" />
                      Clôturer
                    </button>
                  </div>
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
                class="h-14 w-14 rounded-2xl bg-gradient-to-br from-sky-500 to-sky-400 flex items-center justify-center shadow-lg"
              >
                <LifeBuoy class="h-7 w-7 text-white" />
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">
              Aucun ticket pour le moment
            </h3>
            <p class="text-xs text-slate-400 mb-4">
              Besoin d'aide ? Créez votre premier ticket et notre équipe vous répondra rapidement.
            </p>
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
              @click="openNewTicketModal"
            >
              <Plus class="h-3.5 w-3.5" />
              <span>Créer un ticket</span>
            </button>
          </div>
        </section>
      </div>

      <!-- Modal Create ticket -->
      <div
        v-if="showNewTicketModal"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
      >
        <div
          class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl"
        >
          <div class="flex items-center justify-between mb-4">
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-500">
                Nouveau ticket
              </p>
              <h2 class="text-sm font-semibold">
                Contactez le support
              </h2>
            </div>
            <button
              type="button"
              class="text-slate-400 hover:text-slate-200 text-sm"
              @click="closeNewTicketModal"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="submitNewTicket" class="space-y-4">
            <div>
              <label
                class="block text-xs font-semibold text-slate-200 mb-1"
                for="modal_subject"
              >
                Sujet *
              </label>
              <input
                id="modal_subject"
                v-model="createForm.subject"
                type="text"
                required
                class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                placeholder="Ex: Problème d'accès à mon site"
              />
            </div>

            <div>
              <label
                class="block text-xs font-semibold text-slate-200 mb-1"
                for="modal_category"
              >
                Catégorie
              </label>
              <select
                id="modal_category"
                v-model="createForm.category"
                class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
              >
                <option value="">Sélectionner...</option>
                <option value="bug">Bug / Problème technique</option>
                <option value="billing">Facturation / abonnement</option>
                <option value="coaching">Questions sur l'utilisation</option>
                <option value="idea">Idée d'amélioration</option>
              </select>
            </div>

            <div>
              <label
                class="block text-xs font-semibold text-slate-200 mb-1"
                for="modal_message"
              >
                Message *
              </label>
              <textarea
                id="modal_message"
                v-model="createForm.message"
                rows="4"
                required
                class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                placeholder="Décrivez votre problème, question ou suggestion de manière détaillée."
              ></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 text-xs">
              <button
                type="button"
                class="rounded-full border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800"
                @click="closeNewTicketModal"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1.5 font-medium text-slate-50 hover:from-purple-600 hover:to-pink-600 disabled:opacity-60"
                :disabled="createForm.processing"
              >
                {{ createForm.processing ? 'Envoi...' : 'Envoyer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
@keyframes breathe {
  0% {
    transform: scale(0.9);
    opacity: 0.8;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.45);
  }
  70% {
    transform: scale(1.4);
    opacity: 0.2;
    box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
  }
  100% {
    transform: scale(0.9);
    opacity: 0.8;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
  }
}

.animate-breathe {
  animation: breathe 2.2s ease-in-out infinite;
}
</style>
