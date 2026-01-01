<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  transformations: Array,
});

const showAddModal = ref(false);
const beforePreview = ref(null);
const afterPreview = ref(null);

const form = useForm({
  title: '',
  description: '',
  before: null,
  after: null,
});

const handleBeforeChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.before = file;
    beforePreview.value = URL.createObjectURL(file);
  }
};

const handleAfterChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.after = file;
    afterPreview.value = URL.createObjectURL(file);
  }
};

const submit = () => {
  form.post(route('dashboard.gallery.store', { beta: 1 }), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      showAddModal.value = false;
      beforePreview.value = null;
      afterPreview.value = null;
    },
  });
};

const deleteTransformation = (id) => {
  if (
    !confirm('Êtes-vous sûr de vouloir supprimer cette transformation ?')
  ) {
    return;
  }

  router.delete(
    route('dashboard.gallery.destroy', { transformation: id, beta: 1 }),
    {
      preserveScroll: true,
    },
  );
};
</script>

<template>
  <Head title="Galerie (beta)" />

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
            <span>Galerie de transformations</span>
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
        <!-- Header & add button -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h2 class="text-lg font-semibold">Avant / Après</h2>
            <p class="text-sm text-slate-400">
              Mettez en avant vos meilleures transformations pour rassurer vos
              prospects.
            </p>
          </div>
          <button
            type="button"
            class="inline-flex items-center rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
            @click="showAddModal = true"
          >
            <span class="mr-1">+</span>
            Ajouter une transformation
          </button>
        </section>

        <!-- Transformations grid -->
        <section class="space-y-4">
          <div
            v-if="transformations && transformations.length"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
          >
            <article
              v-for="transformation in transformations"
              :key="transformation.id"
              class="bg-slate-900/80 rounded-2xl shadow-xl overflow-hidden border border-slate-800"
            >
              <div class="grid grid-cols-2">
                <div class="relative">
                  <img
                    v-if="
                      transformation.media?.find(
                        (m) => m.collection_name === 'before',
                      )
                    "
                    :src="
                      transformation.media.find(
                        (m) => m.collection_name === 'before',
                      ).original_url
                    "
                    alt="Avant"
                    class="w-full h-40 object-cover"
                  />
                  <div
                    v-else
                    class="w-full h-40 bg-slate-800 flex items-center justify-center"
                  >
                    <span class="text-slate-400 text-xs">Avant</span>
                  </div>
                  <div
                    class="absolute top-2 left-2 bg-red-500/90 text-white text-[10px] font-semibold px-2 py-1 rounded-full shadow"
                  >
                    AVANT
                  </div>
                </div>
                <div class="relative">
                  <img
                    v-if="
                      transformation.media?.find(
                        (m) => m.collection_name === 'after',
                      )
                    "
                    :src="
                      transformation.media.find(
                        (m) => m.collection_name === 'after',
                      ).original_url
                    "
                    alt="Après"
                    class="w-full h-40 object-cover"
                  />
                  <div
                    v-else
                    class="w-full h-40 bg-slate-800 flex items-center justify-center"
                  >
                    <span class="text-slate-400 text-xs">Après</span>
                  </div>
                  <div
                    class="absolute top-2 right-2 bg-emerald-500/90 text-white text-[10px] font-semibold px-2 py-1 rounded-full shadow"
                  >
                    APRÈS
                  </div>
                </div>
              </div>

              <div class="p-4 space-y-3">
                <h3 class="text-sm font-semibold text-slate-50">
                  {{ transformation.title }}
                </h3>
                <p
                  v-if="transformation.description"
                  class="text-xs text-slate-300 line-clamp-3"
                >
                  {{ transformation.description }}
                </p>
                <button
                  type="button"
                  class="w-full rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-3 py-2 text-xs font-semibold text-white shadow-md hover:from-red-600 hover:to-red-700"
                  @click="deleteTransformation(transformation.id)"
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
                <span class="text-2xl">📸</span>
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">Aucune transformation</h3>
            <p class="text-xs text-slate-400 mb-4">
              Ajoutez vos premiers avant/après pour montrer les résultats de
              votre coaching.
            </p>
            <button
              type="button"
              class="inline-flex items-center rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
              @click="showAddModal = true"
            >
              <span class="mr-1">+</span>
              Ajouter une transformation
            </button>
          </div>
        </section>
      </div>

      <!-- Add transformation modal -->
      <div
        v-if="showAddModal"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/60 px-4"
      >
        <div
          class="w-full max-w-2xl rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl"
        >
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold">Ajouter une transformation</h2>
            <button
              type="button"
              class="text-slate-400 hover:text-slate-200 text-sm"
              @click="showAddModal = false"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="submit" class="space-y-5">
            <div>
              <label class="block text-xs font-medium text-slate-200 mb-1">
                Titre
              </label>
              <input
                v-model="form.title"
                type="text"
                required
                class="block w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                placeholder="Ex: Perte de 15kg en 3 mois"
              />
              <p v-if="form.errors.title" class="mt-1 text-[11px] text-rose-400">
                {{ form.errors.title }}
              </p>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-200 mb-1">
                Description (optionnelle)
              </label>
              <textarea
                v-model="form.description"
                rows="3"
                class="block w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                placeholder="Quelques mots sur cette transformation..."
              ></textarea>
              <p
                v-if="form.errors.description"
                class="mt-1 text-[11px] text-rose-400"
              >
                {{ form.errors.description }}
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="block text-xs font-medium text-slate-200 mb-1">
                  Photo avant
                </label>
                <div v-if="beforePreview" class="mb-2">
                  <img
                    :src="beforePreview"
                    alt="Avant"
                    class="w-full h-32 object-cover rounded-xl border border-red-400/40"
                  />
                </div>
                <input
                  type="file"
                  accept="image/*"
                  required
                  class="block w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-900 hover:file:bg-white"
                  @change="handleBeforeChange"
                />
                <p v-if="form.errors.before" class="mt-1 text-[11px] text-rose-400">
                  {{ form.errors.before }}
                </p>
              </div>

              <div class="space-y-2">
                <label class="block text-xs font-medium text-slate-200 mb-1">
                  Photo après
                </label>
                <div v-if="afterPreview" class="mb-2">
                  <img
                    :src="afterPreview"
                    alt="Après"
                    class="w-full h-32 object-cover rounded-xl border border-emerald-400/40"
                  />
                </div>
                <input
                  type="file"
                  accept="image/*"
                  required
                  class="block w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-900 hover:file:bg-white"
                  @change="handleAfterChange"
                />
                <p v-if="form.errors.after" class="mt-1 text-[11px] text-rose-400">
                  {{ form.errors.after }}
                </p>
              </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 text-xs">
              <button
                type="button"
                class="rounded-full border border-slate-700 px-3 py-1.5 text-slate-200 hover:bg-slate-800"
                @click="showAddModal = false"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-1.5 font-medium text-slate-50 hover:from-purple-600 hover:to-pink-600 disabled:opacity-60"
                :disabled="form.processing"
              >
                {{ form.processing ? 'Enregistrement...' : 'Ajouter' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>
