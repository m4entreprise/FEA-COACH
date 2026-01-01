<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  tickets: Array,
});

const mode = ref('form');
const selectedTicketId = ref(props.tickets.length ? props.tickets[0].id : null);

const hasTickets = computed(() => props.tickets.length > 0);

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
  mode.value = 'conversation';
};

const submitNewTicket = () => {
  createForm.post(route('dashboard.support.store'), {
    preserveScroll: true,
    onSuccess: () => {
      createForm.reset('subject', 'category', 'message');
      mode.value = 'conversation';
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
        <a
          :href="route('dashboard')"
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
        <!-- Flash success -->
        <section v-if="$page.props.flash?.success">
          <div
            class="rounded-2xl bg-gradient-to-r from-emerald-500 to-green-600 p-4 text-xs text-white flex items-center gap-2 shadow-xl"
          >
            <span>✔</span>
            <span>{{ $page.props.flash.success }}</span>
          </div>
        </section>

        <!-- Choice cards -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <button
            type="button"
            class="group rounded-2xl border border-slate-800 bg-slate-900/80 p-4 text-left shadow-xl hover:border-purple-500/60"
            :class="mode === 'form' ? 'ring-2 ring-purple-500/60' : ''"
            @click="mode = 'form'"
          >
            <div class="flex items-start gap-3">
              <div
                class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 text-white shadow-lg"
              >
                <span class="text-lg">+</span>
              </div>
              <div>
                <h2 class="text-sm font-semibold mb-1">Nouvelle demande</h2>
                <p class="text-xs text-slate-400">
                  Expliquez votre problème, vos questions ou vos idées
                  d'amélioration.
                </p>
              </div>
            </div>
          </button>

          <button
            v-if="hasTickets"
            type="button"
            class="group rounded-2xl border border-slate-800 bg-slate-900/80 p-4 text-left shadow-xl hover:border-sky-500/60"
            :class="mode === 'conversation' ? 'ring-2 ring-sky-500/60' : ''"
            @click="mode = 'conversation'"
          >
            <div class="flex items-start gap-3">
              <div
                class="flex-shrink-0 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-xl p-3 text-white shadow-lg"
              >
                <span class="text-lg">💬</span>
              </div>
              <div>
                <h2 class="text-sm font-semibold mb-1">
                  Continuer une conversation
                </h2>
                <p class="text-xs text-slate-400">
                  Consultez vos échanges précédents avec l'équipe FEA.
                </p>
              </div>
            </div>
          </button>
        </section>

        <section
          class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start"
        >
          <!-- New ticket form -->
          <div class="lg:col-span-1">
            <div
              class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl space-y-4"
            >
              <div class="flex items-center gap-3">
                <div
                  class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 text-white shadow-lg"
                >
                  <span class="text-lg">📝</span>
                </div>
                <div>
                  <h2 class="text-sm font-semibold">
                    Décrire votre besoin
                  </h2>
                  <p class="text-[11px] text-slate-400">
                    Plus vous êtes précis, plus nous pourrons vous aider.
                  </p>
                </div>
              </div>

              <form
                class="space-y-3 text-xs"
                @submit.prevent="submitNewTicket"
              >
                <div>
                  <label
                    class="block text-[11px] font-semibold text-slate-200 mb-1"
                    for="support_subject"
                  >
                    Sujet
                  </label>
                  <input
                    id="support_subject"
                    v-model="createForm.subject"
                    type="text"
                    required
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Ex: Problème d'accès à mon site"
                  />
                </div>

                <div>
                  <label
                    class="block text-[11px] font-semibold text-slate-200 mb-1"
                    for="support_category"
                  >
                    Catégorie (optionnel)
                  </label>
                  <select
                    id="support_category"
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
                    class="block text-[11px] font-semibold text-slate-200 mb-1"
                    for="support_message"
                  >
                    Message
                  </label>
                  <textarea
                    id="support_message"
                    v-model="createForm.message"
                    rows="4"
                    required
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Décrivez le contexte, les étapes pour reproduire le problème, ou votre question."
                  ></textarea>
                </div>

                <button
                  type="submit"
                  class="w-full rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 font-semibold text-slate-50 shadow-lg hover:from-purple-600 hover:to-pink-600 disabled:opacity-60"
                  :disabled="createForm.processing"
                >
                  {{
                    createForm.processing
                      ? 'Envoi en cours...'
                      : 'Envoyer ma demande'
                  }}
                </button>
              </form>
            </div>
          </div>

          <!-- Conversations -->
          <div class="lg:col-span-2 space-y-4">
            <div
              class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col min-h-[360px]"
            >
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h2 class="text-sm font-semibold flex items-center gap-2">
                    <span>Conversations</span>
                  </h2>
                  <p class="text-[11px] text-slate-400" v-if="hasTickets">
                    Sélectionnez un ticket pour voir l'historique.
                  </p>
                  <p class="text-[11px] text-slate-400" v-else>
                    Aucun ticket pour le moment.
                  </p>
                </div>
              </div>

              <!-- Ticket list -->
              <div
                v-if="hasTickets"
                class="flex gap-3 mb-3 overflow-x-auto pb-1"
              >
                <button
                  v-for="ticket in props.tickets"
                  :key="ticket.id"
                  type="button"
                  class="flex-shrink-0 rounded-full border px-3 py-2 text-left text-[11px]"
                  :class="[
                    selectedTicketId === ticket.id
                      ? 'bg-sky-600 text-white border-sky-500 shadow-lg'
                      : 'bg-slate-900 text-slate-100 border-slate-700 hover:bg-slate-800',
                  ]"
                  @click="selectTicket(ticket.id)"
                >
                  <div class="font-semibold truncate max-w-[160px]">
                    {{ ticket.subject }}
                  </div>
                  <div class="mt-1 flex items-center justify-between gap-2">
                    <span
                      class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                      :class="
                        ticket.status === 'open'
                          ? 'bg-emerald-500/20 text-emerald-200 border border-emerald-500/40'
                          : 'bg-slate-700 text-slate-100 border border-slate-600'
                      "
                    >
                      {{ ticket.status === 'open' ? 'Ouvert' : 'Clôturé' }}
                    </span>
                    <span class="text-[10px] text-slate-300 whitespace-nowrap">
                      {{ ticket.last_message_at || ticket.created_at }}
                    </span>
                  </div>
                </button>
              </div>

              <!-- Conversation body -->
              <div
                v-if="selectedTicket"
                class="flex-1 flex flex-col rounded-2xl bg-slate-950/70 border border-slate-800 p-4"
              >
                <div class="flex items-start justify-between mb-3">
                  <div>
                    <h3 class="text-sm font-semibold text-slate-50">
                      {{ selectedTicket.subject }}
                    </h3>
                    <p
                      v-if="selectedTicket.category"
                      class="text-[11px] text-slate-400"
                    >
                      Catégorie : {{ selectedTicket.category }}
                    </p>
                  </div>
                  <div class="flex flex-col items-end gap-2">
                    <span
                      class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold"
                      :class="
                        selectedTicket.status === 'open'
                          ? 'bg-emerald-500/20 text-emerald-200 border border-emerald-500/40'
                          : 'bg-slate-700 text-slate-100 border border-slate-600'
                      "
                    >
                      {{ selectedTicket.status === 'open' ? 'Ouvert' : 'Clôturé' }}
                    </span>
                    <button
                      v-if="selectedTicket.status === 'open'"
                      type="button"
                      class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900 px-3 py-1 text-[11px] text-slate-100 hover:bg-slate-800"
                      @click="closeTicket"
                    >
                      Clôturer le ticket
                    </button>
                  </div>
                </div>

                <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                  <div
                    v-for="message in selectedTicket.messages"
                    :key="message.id"
                    class="flex"
                    :class="
                      message.is_from_admin ? 'justify-start' : 'justify-end'
                    "
                  >
                    <div
                      class="max-w-[80%] rounded-2xl px-3 py-2.5 text-xs shadow-md"
                      :class="
                        message.is_from_admin
                          ? 'bg-slate-900/90 text-slate-50 border border-sky-500/40'
                          : 'bg-gradient-to-r from-purple-500 to-pink-500 text-white'
                      "
                    >
                      <div class="text-[10px] font-semibold mb-1 opacity-80">
                        {{ message.is_from_admin ? 'Support FEA' : 'Vous' }}
                      </div>
                      <p class="whitespace-pre-line leading-relaxed">
                        {{ message.message }}
                      </p>
                      <div
                        class="mt-1 text-[9px] opacity-70 text-right"
                      >
                        {{ message.created_at }}
                      </div>
                    </div>
                  </div>

                  <p
                    v-if="!selectedTicket.messages.length"
                    class="text-[11px] text-slate-400 mt-4 text-center"
                  >
                    Aucun message pour le moment.
                  </p>
                </div>

                <div
                  class="mt-3 border-t border-slate-800 pt-3 flex items-end gap-2"
                >
                  <textarea
                    v-model="replyForm.message"
                    rows="2"
                    class="flex-1 rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-sky-500 focus:ring-sky-500"
                    :placeholder="
                      selectedTicket.status === 'open'
                        ? 'Écrivez votre réponse au support...'
                        : 'Ticket clôturé - vous pouvez encore envoyer un message pour le rouvrir.'
                    "
                  ></textarea>
                  <button
                    type="submit"
                    class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 to-cyan-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-sky-600 hover:to-cyan-600 disabled:opacity-60"
                    :disabled="replyForm.processing || !replyForm.message"
                    @click="submitReply"
                  >
                    {{ replyForm.processing ? 'Envoi...' : 'Envoyer' }}
                  </button>
                </div>
              </div>

              <div
                v-else
                class="flex-1 flex items-center justify-center text-center text-[11px] text-slate-400"
              >
                <p>
                  Aucune conversation sélectionnée. Créez une demande ou
                  cliquez sur un ticket.
                </p>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>
