<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  faqs: Array,
});

const showModal = ref(false);
const editingFaq = ref(null);

const form = useForm({
  question: '',
  answer: '',
  order: 0,
  is_active: true,
});

const openCreateModal = () => {
  editingFaq.value = null;
  form.reset();
  form.clearErrors();
  form.is_active = true;
  form.order = 0;
  showModal.value = true;
};

const openEditModal = (faq) => {
  editingFaq.value = faq;
  form.question = faq.question;
  form.answer = faq.answer;
  form.order = faq.order;
  form.is_active = faq.is_active;
  form.clearErrors();
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingFaq.value = null;
  form.reset();
  form.clearErrors();
};

const submit = () => {
  if (editingFaq.value) {
    form.patch(
      route('dashboard.faq.update', { faq: editingFaq.value.id, beta: 1 }),
      {
        preserveScroll: true,
        onSuccess: () => closeModal(),
      },
    );
  } else {
    form.post(route('dashboard.faq.store', { beta: 1 }), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  }
};

const deleteFaq = (faq) => {
  if (
    !confirm(
      `Êtes-vous sûr de vouloir supprimer cette question ?\n"${faq.question}"`,
    )
  ) {
    return;
  }

  router.delete(route('dashboard.faq.destroy', { faq: faq.id, beta: 1 }), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Gestion de la FAQ (beta)" />

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
            <span>FAQ du site</span>
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
      <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header & button -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h2 class="text-lg font-semibold">Questions fréquentes</h2>
            <p class="text-sm text-slate-400">
              Gérez les questions/réponses affichées sur votre site public.
            </p>
          </div>
          <PrimaryButton
            type="button"
            class="text-xs"
            @click="openCreateModal"
          >
            <span class="mr-1">+</span>
            Nouvelle question
          </PrimaryButton>
        </section>

        <!-- FAQ list -->
        <section class="space-y-4">
          <div v-if="faqs && faqs.length" class="space-y-3">
            <article
              v-for="faq in faqs"
              :key="faq.id"
              class="rounded-2xl border border-slate-800 bg-slate-900/80 p-4 shadow-md"
            >
              <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex-1">
                  <h3 class="text-sm font-semibold text-slate-50">
                    {{ faq.question }}
                  </h3>
                </div>
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                    :class="
                      faq.is_active
                        ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/40'
                        : 'bg-slate-800 text-slate-300 border border-slate-700'
                    "
                  >
                    {{ faq.is_active ? 'Active' : 'Masquée' }}
                  </span>
                  <span class="text-[10px] text-slate-500">
                    Ordre: {{ faq.order }}
                  </span>
                </div>
              </div>
              <p class="text-xs text-slate-300 whitespace-pre-line mb-3">
                {{ faq.answer }}
              </p>
              <div class="flex justify-end gap-2 text-[11px]">
                <button
                  type="button"
                  class="rounded-full border border-slate-700 px-3 py-1 text-slate-200 hover:bg-slate-800"
                  @click="openEditModal(faq)"
                >
                  Modifier
                </button>
                <button
                  type="button"
                  class="rounded-full border border-rose-600/60 bg-rose-600/10 px-3 py-1 text-rose-200 hover:bg-rose-600/20"
                  @click="deleteFaq(faq)"
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
                class="h-12 w-12 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-400 flex items-center justify-center shadow-lg"
              >
                <span class="text-xl">❓</span>
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">Aucune question</h3>
            <p class="text-xs text-slate-400 mb-4">
              Créez vos premières FAQ pour répondre aux objections de vos
              prospects.
            </p>
            <PrimaryButton type="button" class="text-xs" @click="openCreateModal">
              <span class="mr-1">+</span>
              Créer une question
            </PrimaryButton>
          </div>
        </section>
      </div>

      <!-- Modal Create/Edit -->
      <div
        v-if="showModal"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
      >
        <div
          class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl"
        >
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold">
              {{ editingFaq ? 'Modifier la question' : 'Nouvelle question' }}
            </h2>
            <button
              type="button"
              class="text-slate-400 hover:text-slate-200 text-sm"
              @click="closeModal"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <InputLabel
                for="faq_question"
                value="Question *"
                class="text-xs text-slate-200"
              />
              <TextInput
                id="faq_question"
                v-model="form.question"
                type="text"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                required
              />
              <InputError
                class="mt-1 text-[11px]"
                :message="form.errors.question"
              />
            </div>

            <div>
              <InputLabel
                for="faq_answer"
                value="Réponse *"
                class="text-xs text-slate-200"
              />
              <textarea
                id="faq_answer"
                v-model="form.answer"
                rows="4"
                class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                required
              ></textarea>
              <InputError
                class="mt-1 text-[11px]"
                :message="form.errors.answer"
              />
            </div>

            <div class="grid grid-cols-[1fr_auto] gap-3 items-center">
              <div>
                <InputLabel
                  for="faq_order"
                  value="Ordre d'affichage"
                  class="text-xs text-slate-200"
                />
                <TextInput
                  id="faq_order"
                  v-model.number="form.order"
                  type="number"
                  min="0"
                  class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                />
                <InputError
                  class="mt-1 text-[11px]"
                  :message="form.errors.order"
                />
              </div>

              <label class="flex items-center gap-2 text-xs text-slate-200">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="rounded border-slate-600 bg-slate-900 text-indigo-500 focus:ring-indigo-500"
                />
                Active
              </label>
            </div>

            <div class="flex justify-end gap-2 pt-2 text-xs">
              <button
                type="button"
                class="rounded-full border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800"
                @click="closeModal"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1.5 font-medium text-slate-50 hover:from-purple-600 hover:to-pink-600 disabled:opacity-60"
                :disabled="form.processing"
              >
                {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>
