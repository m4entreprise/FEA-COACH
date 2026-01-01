<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
  messages: Array,
});

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
      <div class="max-w-5xl mx-auto space-y-6">
        <!-- Intro / stats -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl"
        >
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start gap-3">
              <div
                class="flex-shrink-0 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-3 shadow-lg"
              >
                <span class="text-xl">📧</span>
              </div>
              <div>
                <h2 class="text-lg font-semibold">Contacts depuis votre site</h2>
                <p class="text-sm text-slate-400">
                  Gérez les messages envoyés via le formulaire de contact de
                  votre site.
                </p>
              </div>
            </div>
            <div class="text-xs text-slate-400 flex flex-col items-end">
              <span
                class="inline-flex items-center rounded-full bg-slate-800 px-3 py-1 text-[11px] border border-slate-700"
              >
                {{ messages.length }} message{{ messages.length > 1 ? 's' : ''
                }} reçu{{ messages.length > 1 ? 's' : '' }}
              </span>
            </div>
          </div>
        </section>

        <!-- Messages list -->
        <section>
          <div v-if="messages.length" class="space-y-4">
            <article
              v-for="message in messages"
              :key="message.id"
              class="rounded-2xl bg-slate-900/80 border border-slate-800 p-5 shadow-xl"
              :class="{ 'opacity-60': message.is_read }"
            >
              <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-3">
                <div class="flex items-start gap-3 flex-1">
                  <div
                    class="flex-shrink-0 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-3 shadow-md"
                  >
                    <span class="text-lg">👤</span>
                  </div>
                  <div class="flex-1">
                    <h3 class="text-sm font-semibold text-slate-50 mb-1">
                      {{ message.name }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-2 text-[11px]">
                      <a
                        :href="`mailto:${message.email}`"
                        class="inline-flex items-center rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 px-3 py-1 text-white shadow-md hover:from-blue-600 hover:to-indigo-600"
                      >
                        {{ message.email }}
                      </a>
                      <span
                        v-if="message.phone"
                        class="inline-flex items-center rounded-full bg-gradient-to-r from-emerald-500 to-green-500 px-3 py-1 text-white shadow-md"
                      >
                        {{ message.phone }}
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex flex-col items-end gap-2 text-[11px]">
                  <span
                    class="inline-flex items-center rounded-full px-3 py-1 font-semibold shadow-md"
                    :class="
                      message.is_read
                        ? 'bg-slate-700 text-slate-100'
                        : 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white'
                    "
                  >
                    {{ message.is_read ? 'Lu' : 'Nouveau' }}
                  </span>
                  <span
                    class="inline-flex items-center rounded-full bg-slate-800 px-3 py-1 text-slate-300 border border-slate-700"
                  >
                    {{ message.created_at }}
                  </span>
                </div>
              </div>

              <div
                class="rounded-xl bg-slate-950/70 border border-slate-800 p-4 mb-4 text-xs text-slate-100 whitespace-pre-line"
              >
                {{ message.message }}
              </div>

              <div class="flex justify-end gap-2 text-[11px]">
                <button
                  v-if="!message.is_read"
                  type="button"
                  class="inline-flex items-center rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 px-4 py-1.5 text-white font-medium shadow-md hover:from-blue-600 hover:to-cyan-600"
                  @click="markAsRead(message)"
                >
                  Marquer comme lu
                </button>
                <button
                  type="button"
                  class="inline-flex items-center rounded-full bg-gradient-to-r from-rose-500 to-rose-600 px-4 py-1.5 text-white font-medium shadow-md hover:from-rose-600 hover:to-rose-700"
                  @click="deleteMessage(message)"
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
                class="h-14 w-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg"
              >
                <span class="text-2xl">📭</span>
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
