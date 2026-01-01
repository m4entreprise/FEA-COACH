<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Palette, Image as ImageIcon, MonitorPlay, LayoutPanelLeft } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
  coach: Object,
  availableLayouts: Object,
  defaultLayout: String,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const siteLayouts = computed(() =>
  Object.entries(props.availableLayouts || {}).map(([key, layout]) => ({
    key,
    ...layout,
  })),
);

const colorRegex = /^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/;

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

const hasLogo = computed(() => Boolean(logoPreview.value));
const hasHero = computed(() => Boolean(heroPreview.value));

const coachSiteUrl = computed(() => {
  if (!props.coach) return null;
  const slug = props.coach.slug || props.coach.subdomain;
  if (!slug) return null;
  return route('coach.site', { coach_slug: slug });
});

const brandingProgress = computed(() => {
  let filled = 0;
  const total = 5;
  if (colorRegex.test(form.color_primary)) filled++;
  if (colorRegex.test(form.color_secondary)) filled++;
  if (form.site_layout) filled++;
  if (hasLogo.value) filled++;
  if (hasHero.value) filled++;
  return Math.round((filled / total) * 100);
});

const scrollToSection = (id) => {
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

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
  form.post(route('dashboard.branding.update'), {
    forceFormData: true,
    preserveScroll: true,
  });
};

// Live preview handling
const previewHtml = ref('');
const previewLoading = ref(false);
const previewError = ref(null);
let previewTimeoutId = null;
const isPreviewFullscreen = ref(false);

const togglePreviewFullscreen = () => {
  isPreviewFullscreen.value = !isPreviewFullscreen.value;
};

watch(isPreviewFullscreen, (active) => {
  document.body.classList.toggle('overflow-hidden', active);
});

const hasPreviewRequirements = computed(() => {
  return colorRegex.test(form.color_primary) && colorRegex.test(form.color_secondary) && Boolean(form.site_layout);
});

const fetchPreview = async () => {
  if (!hasPreviewRequirements.value) {
    previewHtml.value = '';
    previewError.value = null;
    previewLoading.value = false;
    return;
  }

  previewLoading.value = true;
  previewError.value = null;

  try {
    const { data } = await axios.post(
      route('dashboard.branding.preview'),
      {
        color_primary: form.color_primary,
        color_secondary: form.color_secondary,
        site_layout: form.site_layout,
      },
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

const schedulePreview = () => {
  if (previewTimeoutId) {
    clearTimeout(previewTimeoutId);
  }

  previewTimeoutId = window.setTimeout(() => {
    fetchPreview();
  }, 600);
};

watch(
  () => [form.color_primary, form.color_secondary, form.site_layout],
  () => {
    schedulePreview();
  },
  { immediate: true },
);

onBeforeUnmount(() => {
  if (previewTimeoutId) {
    clearTimeout(previewTimeoutId);
  }
});
</script>

<template>
  <Head title="Apparence & logo " />

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
            <span>Apparence & logo</span>
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
    <main
      class="flex-1 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8"
    >
      <div class="max-w-6xl mx-auto space-y-6">
        <!-- Summary card -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h2 class="text-lg md:text-xl font-semibold mb-1">
              Identité visuelle du site
            </h2>
            <p class="text-sm text-slate-400">
              Couleurs, logo, image hero et mise en page appliqués à votre site vitrine.
            </p>
          </div>
          <div class="text-right space-y-1">
            <p class="text-xs uppercase tracking-wide text-slate-400">
              Progression de configuration
            </p>
            <p class="text-2xl font-semibold text-emerald-300">
              {{ brandingProgress }}%
            </p>
          </div>
        </section>

        <div
          class="grid gap-6 lg:grid-cols-[minmax(0,1.7fr)_minmax(320px,1fr)] lg:items-start"
        >
          <!-- Main form -->
          <section class="space-y-8">
            <header class="space-y-1">
              <h2 class="text-lg font-semibold">Personnalisation visuelle</h2>
              <p class="text-sm text-slate-400">
                Définissez l'identité de votre site : palette, layout, logo et visuel principal.
              </p>
            </header>

            <nav class="flex flex-wrap gap-2 text-[11px] text-slate-300 pt-1">
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('branding-colors')"
              >
                <Palette class="h-3 w-3" />
                <span>Couleurs</span>
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('branding-layout')"
              >
                <LayoutPanelLeft class="h-3 w-3" />
                <span>Layouts</span>
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('branding-assets')"
              >
                <ImageIcon class="h-3 w-3" />
                <span>Logo & hero</span>
              </button>
            </nav>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Colors -->
              <div
                id="branding-colors"
                class="space-y-4 rounded-2xl border border-slate-800 bg-slate-950/60 p-5"
              >
                <div class="flex items-center justify-between gap-3">
                  <h3 class="text-sm font-semibold flex items-center gap-2">
                    <Palette class="h-4 w-4 text-indigo-300" />
                    <span>Palette principale</span>
                  </h3>
                  <span class="text-[11px] text-slate-400">Hexa requis</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <InputLabel
                      for="color_primary"
                      value="Couleur primaire"
                      class="text-xs text-slate-200"
                    />
                    <div class="flex items-center gap-3">
                      <input
                        id="color_primary_picker"
                        type="color"
                        v-model="form.color_primary"
                        class="h-12 w-12 rounded-lg border border-slate-700 bg-slate-900 cursor-pointer"
                      />
                      <TextInput
                        id="color_primary"
                        v-model="form.color_primary"
                        type="text"
                        class="flex-1 bg-slate-950 border-slate-700 text-slate-50 font-mono text-xs"
                        placeholder="#3B82F6"
                      />
                    </div>
                    <div
                      class="h-8 rounded-lg border border-slate-800"
                      :style="{ backgroundColor: form.color_primary }"
                    ></div>
                    <InputError
                      class="text-xs"
                      :message="form.errors.color_primary"
                    />
                  </div>

                  <div class="space-y-2">
                    <InputLabel
                      for="color_secondary"
                      value="Couleur secondaire"
                      class="text-xs text-slate-200"
                    />
                    <div class="flex items-center gap-3">
                      <input
                        id="color_secondary_picker"
                        type="color"
                        v-model="form.color_secondary"
                        class="h-12 w-12 rounded-lg border border-slate-700 bg-slate-900 cursor-pointer"
                      />
                      <TextInput
                        id="color_secondary"
                        v-model="form.color_secondary"
                        type="text"
                        class="flex-1 bg-slate-950 border-slate-700 text-slate-50 font-mono text-xs"
                        placeholder="#10B981"
                      />
                    </div>
                    <div
                      class="h-8 rounded-lg border border-slate-800"
                      :style="{ backgroundColor: form.color_secondary }"
                    ></div>
                    <InputError
                      class="text-xs"
                      :message="form.errors.color_secondary"
                    />
                  </div>
                </div>
              </div>

              <!-- Layout selection -->
              <div
                id="branding-layout"
                class="space-y-4 rounded-2xl border border-slate-800 bg-slate-950/60 p-5"
              >
                <div class="flex items-center justify-between gap-3">
                  <h3 class="text-sm font-semibold flex items-center gap-2">
                    <LayoutPanelLeft class="h-4 w-4 text-purple-300" />
                    <span>Mise en page du site</span>
                  </h3>
                  <span class="text-[11px] text-slate-400">
                    {{ siteLayouts.length }} layouts
                  </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                  <button
                    v-for="layout in siteLayouts"
                    :key="layout.key"
                    type="button"
                    class="text-left rounded-xl border px-4 py-3 text-sm transition-all duration-200"
                    :class="[
                      form.site_layout === layout.key
                        ? 'border-purple-500 bg-slate-900 text-slate-50 shadow-md'
                        : 'border-slate-800 bg-slate-950 text-slate-300 hover:border-purple-500/60 hover:text-slate-50',
                    ]"
                    @click="form.site_layout = layout.key"
                  >
                    <p class="font-semibold mb-1">{{ layout.label }}</p>
                    <p class="text-[11px] text-slate-400">
                      {{ layout.description }}
                    </p>
                  </button>
                </div>
                <InputError
                  class="text-xs"
                  :message="form.errors.site_layout"
                />
              </div>

              <!-- Assets -->
              <div
                id="branding-assets"
                class="space-y-6 rounded-2xl border border-slate-800 bg-slate-950/60 p-5"
              >
                <h3 class="text-sm font-semibold flex items-center gap-2">
                  <ImageIcon class="h-4 w-4 text-emerald-300" />
                  <span>Logo & image hero</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-3">
                    <InputLabel
                      for="logo_upload"
                      value="Logo principal"
                      class="text-xs text-slate-200"
                    />
                    <div
                      v-if="logoPreview"
                      class="rounded-xl border border-slate-800 bg-slate-950/80 p-4 flex items-center justify-center"
                    >
                      <img
                        :src="logoPreview"
                        alt="Logo"
                        class="max-h-20 object-contain"
                      />
                    </div>
                    <div
                      v-else
                      class="rounded-xl border border-dashed border-slate-800 bg-slate-950/40 p-6 flex items-center justify-center text-xs text-slate-500"
                    >
                      Aucun logo pour le moment
                    </div>
                    <input
                      id="logo_upload"
                      type="file"
                      accept="image/*"
                      @change="handleLogoChange"
                      class="block w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-900 hover:file:bg-white"
                    />
                    <p class="text-[11px] text-slate-500">
                      PNG ou SVG recommandé, taille max 2MB.
                    </p>
                  </div>

                  <div class="space-y-3">
                    <InputLabel
                      for="hero_upload"
                      value="Image hero"
                      class="text-xs text-slate-200"
                    />
                    <div
                      v-if="heroPreview"
                      class="rounded-xl border border-slate-800 bg-slate-950/60 overflow-hidden h-32"
                    >
                      <img
                        :src="heroPreview"
                        alt="Hero"
                        class="w-full h-full object-cover"
                      />
                    </div>
                    <div
                      v-else
                      class="rounded-xl border border-dashed border-slate-800 bg-slate-950/40 p-6 flex items-center justify-center text-xs text-slate-500"
                    >
                      Aucune image hero pour le moment
                    </div>
                    <input
                      id="hero_upload"
                      type="file"
                      accept="image/*"
                      @change="handleHeroChange"
                      class="block w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-900 hover:file:bg-white"
                    />
                    <p class="text-[11px] text-slate-500">
                      Recommandé : 1920x1080, taille max 5MB.
                    </p>
                  </div>
                </div>
              </div>

              <div class="flex justify-end pt-2">
                <PrimaryButton :disabled="form.processing">
                  <span v-if="form.processing" class="text-xs">
                    Enregistrement...
                  </span>
                  <span v-else>Enregistrer les modifications</span>
                </PrimaryButton>
              </div>
            </form>
          </section>

          <!-- Live preview -->
          <aside class="space-y-4 lg:sticky lg:top-20 lg:self-start">
            <div
              class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl flex flex-col h-full"
            >
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-xs uppercase tracking-wide text-indigo-300 flex items-center gap-2">
                    <MonitorPlay class="h-4 w-4 text-indigo-300" />
                    Aperçu live
                  </p>
                  <h3 class="text-lg font-semibold text-slate-50">
                    Site public en temps réel
                  </h3>
                  <p class="text-xs text-slate-400">
                    Palette et layout se mettent à jour automatiquement.
                  </p>
                </div>
                <button
                  type="button"
                  class="text-xs uppercase tracking-wide text-slate-400 hover:text-white transition"
                  @click="togglePreviewFullscreen"
                >
                  {{ isPreviewFullscreen ? 'Fermer' : 'Plein écran' }}
                </button>
              </div>

              <div class="mt-4 flex-1">
                <div class="h-12 flex items-center gap-2 text-xs text-slate-400">
                  <span
                    class="w-2 h-2 rounded-full"
                    :class="
                      previewLoading
                        ? 'bg-yellow-400 animate-pulse'
                        : previewError
                          ? 'bg-red-400'
                          : 'bg-emerald-400 animate-breathe'
                    "
                  ></span>
                  <span v-if="previewLoading">Génération de l’aperçu...</span>
                  <span v-else-if="previewError">{{ previewError }}</span>
                  <span v-else>Preview synchronisé</span>
                </div>
                <div
                  class="relative overflow-hidden rounded-2xl border border-slate-800 bg-slate-950/50 shadow-inner min-h-[26rem]"
                >
                  <div
                    v-if="!hasPreviewRequirements"
                    class="absolute inset-0 flex flex-col items-center justify-center text-center px-6 text-slate-400 text-sm gap-2"
                  >
                    <p>Saisissez deux couleurs hexadécimales et choisissez un layout pour voir l’aperçu.</p>
                  </div>
                  <div
                    v-else-if="previewError"
                    class="absolute inset-0 flex flex-col items-center justify-center text-center px-6 text-red-300 text-sm gap-2"
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
                  <div
                    v-else-if="previewLoading && !previewHtml"
                    class="absolute inset-0 flex flex-col items-center justify-center text-center px-6 text-slate-300 text-sm gap-2"
                  >
                    <svg
                      class="h-8 w-8 animate-spin text-indigo-400"
                      fill="none"
                      viewBox="0 0 24 24"
                    >
                      <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                      />
                      <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                      />
                    </svg>
                    <p>Chargement de l’aperçu...</p>
                  </div>
                  <iframe
                    v-show="hasPreviewRequirements && previewHtml"
                    :key="form.site_layout + previewHtml"
                    class="w-full h-[34rem] bg-white overflow-x-hidden"
                    sandbox="allow-same-origin allow-forms"
                    :srcdoc="previewHtml"
                  ></iframe>
                </div>
              </div>
            </div>
          </aside>
        </div>

        <teleport to="body">
          <div
            v-if="isPreviewFullscreen"
            class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-xl flex flex-col"
          >
            <div class="flex flex-wrap items-start justify-between gap-4 px-6 py-4 border-b border-slate-800 text-slate-200">
              <div class="flex items-center gap-3">
                <MonitorPlay class="h-5 w-5 text-indigo-300" />
                <div>
                  <p class="text-sm font-semibold">Aperçu plein écran</p>
                  <p class="text-xs text-slate-400">Affichage {{ form.site_layout }}</p>
                </div>
              </div>
              <div class="flex flex-wrap items-center gap-3 text-xs text-slate-300">
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="layout in siteLayouts"
                    :key="`fullscreen-${layout.key}`"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 transition-all"
                    :class="[
                      form.site_layout === layout.key
                        ? 'border-indigo-400 bg-indigo-500/20 text-white'
                        : 'border-slate-600 hover:border-slate-400',
                    ]"
                    @click="form.site_layout = layout.key"
                  >
                    <span class="font-semibold">{{ layout.label }}</span>
                  </button>
                </div>
                <span v-if="previewLoading" class="animate-pulse text-yellow-300">Mise à jour…</span>
                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-full border border-slate-600 px-3 py-1 hover:text-white hover:border-slate-400"
                  @click="togglePreviewFullscreen"
                >
                  Fermer
                </button>
              </div>
            </div>
            <div class="flex-1 overflow-hidden p-4">
              <iframe
                v-show="hasPreviewRequirements && previewHtml"
                class="w-full h-full rounded-2xl bg-white shadow-2xl overflow-x-hidden"
                sandbox="allow-same-origin allow-forms"
                :srcdoc="previewHtml"
              ></iframe>
              <div
                v-if="!hasPreviewRequirements"
                class="flex h-full items-center justify-center text-center text-slate-300 text-sm px-10"
              >
                Complétez les deux couleurs et le layout pour générer un aperçu.
              </div>
              <div
                v-else-if="previewError"
                class="flex h-full items-center justify-center text-center text-red-300 text-sm px-10"
              >
                {{ previewError }}
              </div>
            </div>
          </div>
        </teleport>
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
