<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import { GripVertical, Plus, Search, CircleDollarSign } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
  plans: Array,
});

const showModal = ref(false);
const editingPlan = ref(null);
const draggingId = ref(null);
const reorderSaving = ref(false);
const reorderError = ref(null);
const previewHtml = ref('');
const previewLoading = ref(false);
const previewError = ref(null);
const isPreviewFullscreen = ref(false);

const plansList = ref([]);

watch(
  () => props.plans,
  (value) => {
    plansList.value = [...(value || [])].sort(
      (a, b) => (a.order ?? 0) - (b.order ?? 0),
    );
  },
  { immediate: true },
);

const form = useForm({
  name: '',
  description: '',
  price: '',
  cta_url: '',
  order: 0,
  is_active: true,
});

const openCreateModal = () => {
  editingPlan.value = null;
  form.reset();
  form.clearErrors();
  form.is_active = true;
  form.order = plansList.value.length;
  showModal.value = true;
};

const openEditModal = (plan) => {
  editingPlan.value = plan;
  form.name = plan.name;
  form.description = plan.description || '';
  form.price = plan.price || '';
  form.cta_url = plan.cta_url || '';
  form.order = plan.order ?? 0;
  form.is_active = plan.is_active;
  form.clearErrors();
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  editingPlan.value = null;
  form.reset();
  form.clearErrors();
};

const submit = () => {
  if (editingPlan.value) {
    form.patch(
      route('dashboard.plans.update', {
        plan: editingPlan.value.id,
        beta: 1,
      }),
      {
        preserveScroll: true,
        onSuccess: () => closeModal(),
      },
    );
  } else {
    form.order = plansList.value.length;
    form.post(route('dashboard.plans.store'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
    });
  }
};

const deletePlan = (plan) => {
  if (
    !confirm(
      `Êtes-vous sûr de vouloir supprimer le plan "${plan.name}" ?`,
    )
  ) {
    return;
  }

  router.delete(
    route('dashboard.plans.destroy', { plan: plan.id, beta: 1 }),
    {
      preserveScroll: true,
    },
  );
};

const onDragStart = (event, plan) => {
  draggingId.value = plan.id;
  if (event.dataTransfer) {
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', String(plan.id));
  }
};

const onDragEnd = () => {
  draggingId.value = null;
};

const onDropCard = (event, targetPlan) => {
  event.preventDefault();
  reorderList(draggingId.value, targetPlan?.id ?? null);
};

const onDropAfterList = (event) => {
  event.preventDefault();
  reorderList(draggingId.value, null);
};

const reorderList = (draggedId, targetId) => {
  if (!draggedId || draggedId === targetId) return;
  const updated = [...plansList.value];
  const fromIndex = updated.findIndex((plan) => plan.id === draggedId);
  if (fromIndex === -1) return;
  const [moved] = updated.splice(fromIndex, 1);
  let toIndex =
    targetId === null ? updated.length : updated.findIndex((plan) => plan.id === targetId);
  if (toIndex === -1) {
    updated.splice(fromIndex, 0, moved);
    return;
  }
  updated.splice(toIndex, 0, moved);
  plansList.value = updated.map((plan, index) => ({
    ...plan,
    order: index,
  }));
  draggingId.value = null;
  saveOrder();
};

const saveOrder = async () => {
  reorderSaving.value = true;
  reorderError.value = null;

  try {
    await axios.post(
      route('dashboard.plans.reorder'),
      {
        order: plansList.value.map((plan, index) => ({
          id: plan.id,
          order: index,
        })),
      },
      {
        headers: { Accept: 'application/json' },
      },
    );
  } catch (error) {
    reorderError.value =
      error.response?.data?.message || 'Impossible d’enregistrer le nouvel ordre.';
  } finally {
    reorderSaving.value = false;
  }
};

const fetchPreview = async () => {
  previewLoading.value = true;
  previewError.value = null;

  try {
    const { data } = await axios.post(
      route('dashboard.plans.preview'),
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
  <Head title="Plans tarifaires " />

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
            <span>Plans tarifaires</span>
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
        <!-- Header & CTA -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h2 class="text-lg font-semibold">Vos offres</h2>
            <p class="text-sm text-slate-400">
              Créez et ajustez les plans visibles sur votre site public.
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
              <span>Nouveau plan</span>
            </button>
          </div>
        </section>

        <section
          class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl space-y-2"
        >
          <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-300">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-breathe"></span>
              <span>{{ plansList.length }} plan(s) affichés</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-slate-400">Glissez-déposez pour réordonner les offres.</span>
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

        <!-- Plans grid / empty state -->
        <section class="space-y-4">
          <div
            v-if="plansList.length"
            class="grid gap-5 md:grid-cols-2 lg:grid-cols-3"
          >
            <article
              v-for="plan in plansList"
              :key="plan.id"
              class="rounded-2xl border bg-slate-900/80 p-5 shadow-xl flex flex-col gap-3 transition"
              :class="[
                draggingId === plan.id
                  ? 'border-indigo-500/70 bg-slate-900'
                  : 'border-slate-800 hover:border-slate-700',
              ]"
              draggable="true"
              @dragstart="onDragStart($event, plan)"
              @dragend="onDragEnd"
              @dragover.prevent
              @drop="onDropCard($event, plan)"
            >
              <div class="flex items-start gap-4">
                <button
                  type="button"
                  class="h-10 w-10 rounded-xl border border-slate-800 bg-slate-950 flex items-center justify-center text-slate-400 hover:text-slate-100"
                >
                  <GripVertical class="h-4 w-4" />
                </button>
                <div class="flex-1 space-y-3">
                  <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                      <p class="text-xs uppercase tracking-wide text-slate-500">Plan</p>
                      <h3 class="text-sm font-semibold text-slate-50">
                        {{ plan.name }}
                      </h3>
                    </div>
                    <div class="flex flex-col items-end gap-1 text-right">
                      <span
                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold"
                        :class="
                          plan.is_active
                            ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
                            : 'border-slate-700 bg-slate-800 text-slate-300'
                        "
                      >
                        {{ plan.is_active ? 'Actif' : 'Masqué' }}
                      </span>
                      <span class="text-[11px] text-slate-500">
                        Position : {{ plan.order + 1 }}
                      </span>
                    </div>
                  </div>
                  <div class="flex flex-wrap items-baseline gap-2">
                    <p
                      v-if="plan.price"
                      class="text-lg font-bold text-emerald-400"
                    >
                      {{ parseFloat(plan.price).toFixed(2) }}€
                    </p>
                    <p v-else class="text-xs text-slate-400 italic">Prix sur demande</p>
                  </div>
                  <p
                    v-if="plan.description"
                    class="text-xs text-slate-300 line-clamp-3"
                  >
                    {{ plan.description }}
                  </p>
                  <p
                    v-if="plan.cta_url"
                    class="text-[11px] text-slate-400 truncate"
                  >
                    <span class="font-semibold">Lien :</span>
                    <a
                      :href="plan.cta_url"
                      target="_blank"
                      class="ml-1 text-sky-400 hover:text-sky-300"
                    >
                      {{ plan.cta_url }}
                    </a>
                  </p>
                  <div class="mt-auto flex gap-2 pt-2 text-[11px]">
                    <button
                      type="button"
                      class="flex-1 rounded-full border border-slate-700 px-4 py-1.5 text-slate-200 hover:bg-slate-800"
                      @click="openEditModal(plan)"
                    >
                      Modifier
                    </button>
                    <button
                      type="button"
                      class="rounded-full border border-rose-500/50 bg-rose-500/10 px-4 py-1.5 text-rose-200 hover:bg-rose-500/20"
                      @click="deletePlan(plan)"
                    >
                      Supprimer
                    </button>
                  </div>
                </div>
              </div>
            </article>

            <div
              class="h-10 rounded-2xl border border-dashed border-slate-700 text-center text-xs text-slate-500 flex items-center justify-center"
              @dragover.prevent
              @drop="onDropAfterList"
            >
              Déposez ici pour placer le plan à la fin
            </div>
          </div>

          <div
            v-else
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-10 text-center text-slate-100 shadow-xl"
          >
            <div class="flex justify-center mb-4">
              <div
                class="h-14 w-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center shadow-lg"
              >
                <CircleDollarSign class="h-7 w-7 text-white" />
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">Aucun plan</h3>
            <p class="text-xs text-slate-400 mb-4">
              Créez vos premières offres pour les afficher sur votre site.
            </p>
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
              @click="openCreateModal"
            >
              <Plus class="h-3.5 w-3.5" />
              <span>Créer un plan</span>
            </button>
          </div>
        </section>
      </div>

      <!-- Modal create/edit -->
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
                {{ editingPlan ? 'Modifier' : 'Nouveau plan' }}
              </p>
              <h2 class="text-sm font-semibold">
                {{ editingPlan ? 'Modifier le plan' : 'Nouveau plan' }}
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
                for="plan_name"
                value="Nom du plan *"
                class="text-xs text-slate-200"
              />
              <TextInput
                id="plan_name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                required
              />
              <InputError
                class="mt-1 text-[11px]"
                :message="form.errors.name"
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <InputLabel
                  for="plan_price"
                  value="Prix (€)"
                  class="text-xs text-slate-200"
                />
                <TextInput
                  id="plan_price"
                  v-model="form.price"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                />
                <InputError
                  class="mt-1 text-[11px]"
                  :message="form.errors.price"
                />
              </div>
              <div>
                <InputLabel
                  for="plan_cta_url"
                  value="URL de réservation"
                  class="text-xs text-slate-200"
                />
                <TextInput
                  id="plan_cta_url"
                  v-model="form.cta_url"
                  type="url"
                  class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                  placeholder="https://..."
                />
                <InputError
                  class="mt-1 text-[11px]"
                  :message="form.errors.cta_url"
                />
              </div>
            </div>

            <div>
              <InputLabel
                for="plan_description"
                value="Description"
                class="text-xs text-slate-200"
              />
              <textarea
                id="plan_description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-emerald-500 focus:ring-emerald-500"
              ></textarea>
              <InputError
                class="mt-1 text-[11px]"
                :message="form.errors.description"
              />
            </div>

            <label class="flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-xs text-slate-200">
              <input
                id="plan_is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-emerald-500"
              />
              Plan actif (visible sur le site)
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
            Aperçu plans
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
