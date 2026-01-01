<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
  coach: Object,
  availableLayouts: Object,
  defaultLayout: String,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const form = useForm({
  color_primary: props.coach?.color_primary || '#3B82F6',
  color_secondary: props.coach?.color_secondary || '#10B981',
  site_layout: props.coach?.site_layout || props.defaultLayout || 'classic',
  logo: null,
  hero: null,
});

const logoPreview = ref(
  props.coach?.media?.find((m) => m.collection_name === 'logo')?.original_url || null,
);
const heroPreview = ref(
  props.coach?.media?.find((m) => m.collection_name === 'hero')?.original_url || null,
);

const coachSiteUrl = computed(() => {
  if (!props.coach) return null;
  const slug = props.coach.slug || props.coach.subdomain;
  if (!slug) return null;
  return route('coach.site', { coach_slug: slug });
});

const handleLogoChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.logo = file;
    logoPreview.value = URL.createObjectURL(file);
  }
};

const handleHeroChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.hero = file;
    heroPreview.value = URL.createObjectURL(file);
  }
};

const submit = () => {
  form.post(route('dashboard.branding.update', { beta: 1 }), {
    forceFormData: true,
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Apparence & logo (beta)" />

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
            <span>Apparence & logo</span>
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
        <a
          v-if="coachSiteUrl"
          :href="coachSiteUrl"
          target="_blank"
          class="hidden sm:inline-flex items-center gap-2 rounded-full bg-emerald-500/90 px-4 py-2 text-xs font-semibold text-emerald-950 shadow-lg hover:bg-emerald-400 transition-colors"
        >
          <span>Voir mon site</span>
        </a>
      </div>
    </header>

    <!-- Main content -->
    <main class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8">
      <div class="max-w-5xl mx-auto space-y-6">
        <!-- Intro card -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h2 class="text-lg md:text-xl font-semibold mb-1">
              Identite visuelle de votre site
            </h2>
            <p class="text-sm text-slate-400">
              Couleurs, logo, image hero et mise en page de votre site vitrine.
            </p>
          </div>
          <div class="text-xs text-slate-400 flex flex-col items-end gap-1">
            <p v-if="user">
              Connecte en tant que <span class="font-semibold text-slate-100">{{ user.name }}</span>
            </p>
            <p v-if="coachSiteUrl" class="hidden sm:block">
              Lien public :
              <a
                :href="coachSiteUrl"
                target="_blank"
                class="text-emerald-300 hover:text-emerald-200 underline underline-offset-2"
                >Ouvrir le site</a
              >
            </p>
          </div>
        </section>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Colors -->
          <section
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl space-y-4"
          >
            <header class="flex items-center gap-3 mb-2">
              <div
                class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg"
              >
                <span class="text-white text-lg">🎨</span>
              </div>
              <div>
                <h3 class="text-base md:text-lg font-semibold">Couleurs de la marque</h3>
                <p class="text-xs text-slate-400">
                  Couleurs principales appliquees a votre site public.
                </p>
              </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Primary -->
              <div class="rounded-xl border border-slate-700 bg-slate-900/80 p-4 space-y-3">
                <label class="block text-xs font-semibold text-slate-200 mb-1">
                  Couleur primaire
                </label>
                <div class="flex items-center gap-4">
                  <input
                    type="color"
                    v-model="form.color_primary"
                    class="h-12 w-12 rounded-lg border border-slate-600 bg-slate-900 cursor-pointer"
                  />
                  <input
                    type="text"
                    v-model="form.color_primary"
                    class="flex-1 rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono"
                    placeholder="#3B82F6"
                  />
                </div>
                <div
                  class="h-8 rounded-md border border-slate-700"
                  :style="{ backgroundColor: form.color_primary }"
                />
                <p class="text-[11px] text-slate-400">
                  Utilisee pour les boutons principaux et les appels a l'action.
                </p>
              </div>

              <!-- Secondary -->
              <div class="rounded-xl border border-slate-700 bg-slate-900/80 p-4 space-y-3">
                <label class="block text-xs font-semibold text-slate-200 mb-1">
                  Couleur secondaire
                </label>
                <div class="flex items-center gap-4">
                  <input
                    type="color"
                    v-model="form.color_secondary"
                    class="h-12 w-12 rounded-lg border border-slate-600 bg-slate-900 cursor-pointer"
                  />
                  <input
                    type="text"
                    v-model="form.color_secondary"
                    class="flex-1 rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono"
                    placeholder="#10B981"
                  />
                </div>
                <div
                  class="h-8 rounded-md border border-slate-700"
                  :style="{ backgroundColor: form.color_secondary }"
                />
                <p class="text-[11px] text-slate-400">
                  Utilisee pour les accents et elements secondaires.
                </p>
              </div>
            </div>
          </section>

          <!-- Layout choice -->
          <section
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl space-y-4"
          >
            <header class="flex items-center gap-3 mb-2">
              <div
                class="h-9 w-9 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg"
              >
                <span class="text-white text-lg">📐</span>
              </div>
              <div>
                <h3 class="text-base md:text-lg font-semibold">Mise en page du site</h3>
                <p class="text-xs text-slate-400">
                  Choisissez le layout general de votre site vitrine.
                </p>
              </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <button
                v-for="(layout, key) in availableLayouts"
                :key="key"
                type="button"
                @click="form.site_layout = key"
                class="text-left rounded-xl border px-4 py-3 text-sm transition-all duration-200"
                :class="
                  form.site_layout === key
                    ? 'border-purple-500 bg-slate-800 text-slate-50 shadow-md'
                    : 'border-slate-700 bg-slate-900 text-slate-300 hover:border-purple-500/60 hover:bg-slate-800'
                "
              >
                <p class="font-semibold mb-1">{{ layout.label }}</p>
                <p class="text-[11px] text-slate-400">
                  {{ layout.description }}
                </p>
              </button>
            </div>
          </section>

          <!-- Logo & hero -->
          <section
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl space-y-6"
          >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Logo -->
              <div class="space-y-3">
                <h3 class="text-sm font-semibold">Logo</h3>
                <div
                  v-if="logoPreview"
                  class="rounded-xl border border-slate-700 bg-slate-950/60 p-4 flex items-center justify-center"
                >
                  <img
                    :src="logoPreview"
                    alt="Logo"
                    class="max-h-20 object-contain"
                  />
                </div>
                <div
                  v-else
                  class="rounded-xl border border-dashed border-slate-700 bg-slate-950/40 p-6 flex items-center justify-center text-xs text-slate-400"
                >
                  Aucun logo pour le moment
                </div>
                <input
                  type="file"
                  accept="image/*"
                  @change="handleLogoChange"
                  class="block w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-900 hover:file:bg-white"
                />
                <p class="text-[11px] text-slate-500">
                  PNG ou SVG recommande, taille max 2MB.
                </p>
              </div>

              <!-- Hero -->
              <div class="space-y-3">
                <h3 class="text-sm font-semibold">Image hero</h3>
                <div
                  v-if="heroPreview"
                  class="rounded-xl border border-slate-700 bg-slate-950/60 overflow-hidden h-32"
                >
                  <img
                    :src="heroPreview"
                    alt="Hero"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div
                  v-else
                  class="rounded-xl border border-dashed border-slate-700 bg-slate-950/40 p-6 flex items-center justify-center text-xs text-slate-400"
                >
                  Aucune image hero pour le moment
                </div>
                <input
                  type="file"
                  accept="image/*"
                  @change="handleHeroChange"
                  class="block w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-900 hover:file:bg-white"
                />
                <p class="text-[11px] text-slate-500">
                  Grande image horizontale recommande (1920x1080), taille max 5MB.
                </p>
              </div>
            </div>
          </section>

          <!-- Actions -->
          <section class="flex justify-end">
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed"
            >
              <span v-if="form.processing" class="text-xs">Enregistrement...</span>
              <span v-else>Enregistrer les modifications</span>
            </button>
          </section>
        </form>
      </div>
    </main>
  </div>
</template>
