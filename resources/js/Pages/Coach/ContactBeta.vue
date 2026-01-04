<script setup>
import { Head, router } from '@inertiajs/vue3';
import { Mail, MailOpen, User, Phone, Trash2, Inbox } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps({
  messages: Array,
});

const dashboardBackUrl = computed(() => {
  if (typeof window === 'undefined') return route('dashboard');
  const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
  return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
  router.visit(dashboardBackUrl.value);
};

const markAsRead = (message) => {
  if (message.is_read) return;

  router.patch(
    route('dashboard.contact.read', {
      contactMessage: message.id,
      beta: 1,
    }),
    {
      preserveScroll: true,
    },
  );
};

const deleteMessage = (message) => {
  if (!confirm(`Supprimer ce message de ${message.name} ?`)) return;

  router.delete(
    route('dashboard.contact.destroy', {
      contactMessage: message.id,
      beta: 1,
    }),
    {
      preserveScroll: true,
    },
  );
};
</script>

<template>
  <Head title="Messages de contact " />

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
            <span>Messages reçus</span>
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
            <h2 class="text-lg font-semibold">Messages reçus</h2>
            <p class="text-sm text-slate-400">
              Gérez les messages envoyés via le formulaire de contact de votre site.
            </p>
          </div>
        </section>

        <section
          class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl space-y-2"
        >
          <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-300">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-breathe"></span>
              <span>{{ messages.length }} message{{ messages.length > 1 ? 's' : '' }} reçu{{ messages.length > 1 ? 's' : '' }}</span>
            </div>
          </div>
        </section>

        <!-- Messages list -->
        <section class="space-y-4">
          <div v-if="messages.length" class="space-y-3">
            <article
              v-for="message in messages"
              :key="message.id"
              class="rounded-2xl border bg-slate-900/80 p-5 shadow-md transition"
              :class="[
                message.is_read
                  ? 'border-slate-800 hover:border-slate-700 opacity-70'
                  : 'border-slate-800 hover:border-slate-700'
              ]"
            >
              <div class="flex items-start gap-4">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-rose-500 to-rose-400 flex items-center justify-center shadow-lg flex-shrink-0">
                  <Mail class="h-4 w-4" />
                </div>
                <div class="flex-1 space-y-3">
                  <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-1">
                      <p class="text-xs uppercase tracking-wide text-slate-500">
                        Expéditeur
                      </p>
                      <h3 class="text-sm md:text-base font-semibold text-slate-50">
                        {{ message.name }}
                      </h3>
                    </div>
                    <div class="flex items-center gap-2">
                      <span
                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold"
                        :class="
                          message.is_read
                            ? 'border-slate-700 bg-slate-800 text-slate-300'
                            : 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                        "
                      >
                        {{ message.is_read ? 'Lu' : 'Nouveau' }}
                      </span>
                      <span class="text-[11px] text-slate-500">
                        {{ message.created_at }}
                      </span>
                    </div>
                  </div>
                  <div class="flex flex-wrap items-center gap-2 text-xs">
                    <a
                      :href="`mailto:${message.email}`"
                      class="inline-flex items-center gap-1 rounded-full bg-slate-800 px-3 py-1 text-slate-200 border border-slate-700 hover:bg-slate-700"
                    >
                      <Mail class="h-3 w-3" />
                      {{ message.email }}
                    </a>
                    <span
                      v-if="message.phone"
                      class="inline-flex items-center gap-1 rounded-full bg-slate-800 px-3 py-1 text-slate-200 border border-slate-700"
                    >
                      <Phone class="h-3 w-3" />
                      {{ message.phone }}
                    </span>
                  </div>
                  <div
                    class="rounded-xl bg-slate-950/70 border border-slate-800 p-4 text-xs md:text-sm text-slate-300 whitespace-pre-line"
                  >
                    {{ message.message }}
                  </div>
                  <div class="flex flex-wrap justify-end gap-2 text-[11px]">
                    <button
                      v-if="!message.is_read"
                      type="button"
                      class="inline-flex items-center gap-1 rounded-full border border-slate-700 px-4 py-1.5 text-slate-200 hover:bg-slate-800"
                      @click="markAsRead(message)"
                    >
                      <MailOpen class="h-3 w-3" />
                      Marquer comme lu
                    </button>
                    <button
                      type="button"
                      class="inline-flex items-center gap-1 rounded-full border border-rose-500/50 bg-rose-500/10 px-4 py-1.5 text-rose-200 hover:bg-rose-500/20"
                      @click="deleteMessage(message)"
                    >
                      <Trash2 class="h-3 w-3" />
                      Supprimer
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
                class="h-14 w-14 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-400 flex items-center justify-center shadow-lg"
              >
                <Inbox class="h-7 w-7 text-white" />
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">
              Aucun message pour le moment
            </h3>
            <p class="text-xs text-slate-400">
              Dès qu'une personne remplira le formulaire de contact de votre
              site, son message apparaîtra ici.
            </p>
          </div>
        </section>
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
