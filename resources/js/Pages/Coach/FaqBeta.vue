<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import { GripVertical, Plus, HelpCircle, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { vAutoAnimate } from '@formkit/auto-animate/vue';
import { Toaster, toast } from 'vue-sonner';
import { VueDraggable } from 'vue-draggable-plus';

const props = defineProps({
  faqs: Array,
});

const showModal = ref(false);
const editingFaq = ref(null);
const draggingId = ref(null);
const reorderSaving = ref(false);
const reorderError = ref(null);
const previewHtml = ref('');
const previewLoading = ref(false);
const previewError = ref(null);
const isPreviewFullscreen = ref(false);

const dashboardBackUrl = computed(() => {
  if (typeof window === 'undefined') return route('dashboard');
  const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
  return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
  router.visit(dashboardBackUrl.value);
};

const faqsList = ref([]);

watch(
  () => props.faqs,
  (value) => {
    faqsList.value = [...(value || [])].sort(
      (a, b) => (a.order ?? 0) - (b.order ?? 0),
    );
  },
  { immediate: true },
);

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
  form.order = faqsList.value.length;
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
        onSuccess: () => {
          closeModal();
          toast.success('Question mise à jour', {
            description: 'La FAQ publique reflète vos dernières modifications.',
          });
        },
        onError: () => {
          toast.error('Impossible de mettre à jour', {
            description: 'Vérifiez les champs requis puis réessayez.',
          });
        },
      },
    );
  } else {
    form.order = faqsList.value.length;
    form.post(route('dashboard.faq.store'), {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        toast.success('Question ajoutée', {
          description: 'Votre nouvelle entrée est prête à apparaître sur le site.',
        });
      },
      onError: () => {
        toast.error('Impossible de créer la question', {
          description: 'Corrigez les erreurs de formulaire puis réessayez.',
        });
      },
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
    onSuccess: () => {
      toast.success('Question supprimée');
    },
    onError: () => {
      toast.error('Suppression impossible', {
        description: 'Réessayez dans un instant.',
      });
    },
  });
};

const onDragStart = (event) => {
  const id = Number(event?.item?.dataset?.id);
  draggingId.value = Number.isNaN(id) ? null : id;
};

const onDragFinish = () => {
  draggingId.value = null;
  handleReorder();
};

const handleReorder = () => {
  faqsList.value = faqsList.value.map((faq, index) => ({
    ...faq,
    order: index,
  }));
  saveOrder();
};

const saveOrder = async () => {
  reorderSaving.value = true;
  reorderError.value = null;

  try {
    await axios.post(
      route('dashboard.faq.reorder'),
      {
        order: faqsList.value.map((faq, index) => ({
          id: faq.id,
          order: index,
        })),
      },
      {
        headers: { Accept: 'application/json' },
      },
    );
    toast.success('Ordre mis à jour', {
      description: 'La nouvelle hiérarchie a été enregistrée.',
    });
  } catch (error) {
    const message =
      error.response?.data?.message || 'Impossible d’enregistrer le nouvel ordre.';
    reorderError.value = message;
    toast.error('Échec de la synchronisation', {
      description: message,
    });
  } finally {
    reorderSaving.value = false;
  }
};

const fetchPreview = async () => {
  previewLoading.value = true;
  previewError.value = null;

  try {
    const { data } = await axios.post(
      route('dashboard.faq.preview'),
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
  <Head title="Gestion de la FAQ " />

  <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
    <Toaster rich-colors theme="dark" position="top-right" close-button />
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
            <span>FAQ du site</span>
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
              <Plus class="h-3.5 w-3.5" />
              <span>Nouvelle question</span>
            </button>
          </div>
        </section>

        <section
          class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl space-y-2"
        >
          <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-300">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-breathe"></span>
              <span>{{ faqsList.length }} question(s) affichées</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-slate-400">Glissez-déposez pour réordonner les entrées.</span>
              <span
                class="inline-flex items-center rounded-full px-3 py-1 text-[11px]"
                :class="[
                  reorderSaving
                    ? 'border-yellow-400/40 text-yellow-200 bg-yellow-400/10'
                    : reorderError
                      ? 'border-rose-500/40 text-rose-200 bg-rose-500/10'
                      : 'border-slate-700 text-slate-300 bg-slate-800/60',
                ]"
              >
                <span v-if="reorderSaving">Enregistrement…</span>
                <span v-else-if="reorderError">{{ reorderError }}</span>
                <span v-else>Ordre synchronisé</span>
              </span>
            </div>
          </div>
        </section>

        <!-- FAQ list -->
        <section class="space-y-4">
          <VueDraggable
            v-if="faqsList.length"
            v-model="faqsList"
            item-key="id"
            handle=".faq-drag-handle"
            class="space-y-3"
            v-auto-animate
            ghost-class="drag-ghost"
            chosen-class="drag-chosen"
            :animation="220"
            @start="onDragStart"
            @end="onDragFinish"
          >
            <template #item="{ element: faq }">
              <article
                :key="faq.id"
                :data-id="faq.id"
                class="rounded-2xl border bg-slate-900/80 p-5 shadow-md transition"
                :class="[
                  draggingId === faq.id
                    ? 'border-indigo-500/70 bg-slate-900'
                    : 'border-slate-800 hover:border-slate-700',
                ]"
              >
                <div class="flex items-start gap-4">
                  <button
                    type="button"
                    class="faq-drag-handle h-10 w-10 rounded-xl border border-slate-800 bg-slate-950 flex items-center justify-center text-slate-400 hover:text-slate-100"
                  >
                    <GripVertical class="h-4 w-4" />
                  </button>
                  <div class="flex-1 space-y-3">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                      <div class="space-y-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">
                          Question
                        </p>
                        <h3 class="text-sm md:text-base font-semibold text-slate-50">
                          {{ faq.question }}
                        </h3>
                      </div>
                      <div class="flex items-center gap-2">
                        <span
                          class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold"
                          :class="
                            faq.is_active
                              ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                              : 'border-slate-700 bg-slate-800 text-slate-300'
                          "
                        >
                          {{ faq.is_active ? 'Active' : 'Masquée' }}
                        </span>
                        <span class="text-[11px] text-slate-500">
                          Position : {{ faq.order + 1 }}
                        </span>
                      </div>
                    </div>
                    <p class="text-xs md:text-sm text-slate-300 whitespace-pre-line">
                      {{ faq.answer }}
                    </p>
                    <div class="flex flex-wrap gap-2 pt-2 text-[11px]">
                      <button
                        type="button"
                        class="rounded-full border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800"
                        @click="openEditModal(faq)"
                      >
                        Modifier
                      </button>
                      <button
                        type="button"
                        class="rounded-full border border-rose-500/40 bg-rose-500/10 px-3 py-1.5 text-rose-200 hover:bg-rose-500/20"
                        @click="deleteFaq(faq)"
                      >
                        Supprimer
                      </button>
                    </div>
                  </div>
                </div>
              </article>
            </template>
          </VueDraggable>

          <div
            v-else
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-10 text-center text-slate-100 shadow-xl"
          >
            <div class="flex justify-center mb-4">
              <div
                class="h-14 w-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-400 flex items-center justify-center shadow-lg"
              >
                <HelpCircle class="h-7 w-7 text-white" />
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">Aucune question</h3>
            <p class="text-xs text-slate-400 mb-4">
              Créez vos premières FAQ pour répondre aux objections de vos
              prospects.
            </p>
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
              @click="openCreateModal"
            >
              <Plus class="h-3.5 w-3.5" />
              <span>Créer une question</span>
            </button>
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
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-500">
                {{ editingFaq ? 'Modifier' : 'Nouvelle entrée' }}
              </p>
              <h2 class="text-sm font-semibold">
                {{ editingFaq ? 'Modifier la question' : 'Nouvelle question' }}
              </h2>
            </div>
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

            <label class="flex items-center gap-2 text-xs text-slate-200">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="rounded border-slate-600 bg-slate-900 text-indigo-500 focus:ring-indigo-500"
              />
              Question active (visible sur le site)
            </label>

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

  <teleport to="body">
    <div
      v-if="isPreviewFullscreen"
      class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl flex flex-col"
    >
      <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-slate-800 text-slate-200">
        <div>
          <p class="text-xs uppercase tracking-wide text-indigo-300">
            Aperçu FAQ
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
