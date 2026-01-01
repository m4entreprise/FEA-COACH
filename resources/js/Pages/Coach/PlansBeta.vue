<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  plans: Array,
});

const showModal = ref(false);
const editingPlan = ref(null);

const form = useForm({
  name: '',
  description: '',
  price: '',
  cta_url: '',
  is_active: true,
});

const openCreateModal = () => {
  editingPlan.value = null;
  form.reset();
  form.clearErrors();
  form.is_active = true;
  showModal.value = true;
};

const openEditModal = (plan) => {
  editingPlan.value = plan;
  form.name = plan.name;
  form.description = plan.description || '';
  form.price = plan.price || '';
  form.cta_url = plan.cta_url || '';
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
    form.post(route('dashboard.plans.store', { beta: 1 }), {
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
</script>

<template>
  <Head title="Plans tarifaires (beta)" />

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
            <span>Plans tarifaires</span>
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
          <PrimaryButton type="button" class="text-xs" @click="openCreateModal">
            <span class="mr-1">+</span>
            Nouveau plan
          </PrimaryButton>
        </section>

        <!-- Plans grid / empty state -->
        <section class="space-y-4">
          <div
            v-if="plans && plans.length"
            class="grid gap-5 md:grid-cols-2 lg:grid-cols-3"
          >
            <article
              v-for="plan in plans"
              :key="plan.id"
              class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col gap-3"
            >
              <div class="flex items-start justify-between gap-3">
                <div>
                  <h3 class="text-sm font-semibold text-slate-50">
                    {{ plan.name }}
                  </h3>
                  <p v-if="plan.price" class="mt-1 text-lg font-bold text-emerald-400">
                    {{ parseFloat(plan.price).toFixed(2) }}€
                  </p>
                  <p v-else class="mt-1 text-xs text-slate-400 italic">
                    Prix sur demande
                  </p>
                </div>
                <span
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                  :class="
                    plan.is_active
                      ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/40'
                      : 'bg-slate-800 text-slate-300 border border-slate-700'
                  "
                >
                  {{ plan.is_active ? 'Actif' : 'Inactif' }}
                </span>
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
                  class="flex-1 rounded-full bg-gradient-to-r from-sky-500 to-indigo-500 px-3 py-1.5 font-medium text-slate-50 hover:from-sky-600 hover:to-indigo-600"
                  @click="openEditModal(plan)"
                >
                  Modifier
                </button>
                <button
                  type="button"
                  class="rounded-full bg-gradient-to-r from-rose-500 to-rose-600 px-3 py-1.5 text-slate-50 hover:from-rose-600 hover:to-rose-700"
                  @click="deletePlan(plan)"
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
                class="h-14 w-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-500 flex items-center justify-center shadow-lg"
              >
                <span class="text-2xl">💰</span>
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">Aucun plan</h3>
            <p class="text-xs text-slate-400 mb-4">
              Créez vos premières offres pour les afficher sur votre site.
            </p>
            <PrimaryButton type="button" class="text-xs" @click="openCreateModal">
              <span class="mr-1">+</span>
              Créer un plan
            </PrimaryButton>
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
            <h2 class="text-sm font-semibold">
              {{ editingPlan ? 'Modifier le plan' : 'Nouveau plan' }}
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

            <div>
              <InputLabel
                for="plan_cta_url"
                value="URL de réservation (optionnel)"
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

            <div class="flex items-center gap-2 rounded-xl bg-slate-900/80 border border-slate-700 px-3 py-2">
              <input
                id="plan_is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-emerald-500 focus:ring-emerald-500"
              />
              <label
                for="plan_is_active"
                class="text-xs text-slate-200"
              >
                Plan actif (visible sur le site)
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
