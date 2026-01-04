<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { FileText, User, HelpCircle, Share2, MonitorPlay } from 'lucide-vue-next';
import axios from 'axios';
import { Toaster, toast } from 'vue-sonner';

const props = defineProps({
  coach: Object,
  profilePhotoUrl: String,
  siteLayouts: {
    type: Array,
    default: () => [],
  },
  defaultLayout: {
    type: String,
    default: 'classic',
  },
});

const form = useForm({
  hero_title: props.coach?.hero_title || '',
  hero_subtitle: props.coach?.hero_subtitle || '',
  about_text: props.coach?.about_text || '',
  method_text: props.coach?.method_text || '',
  method_title: props.coach?.method_title || '',
  method_subtitle: props.coach?.method_subtitle || '',
  method_step1_title: props.coach?.method_step1_title || '',
  method_step1_description: props.coach?.method_step1_description || '',
  method_step2_title: props.coach?.method_step2_title || '',
  method_step2_description: props.coach?.method_step2_description || '',
  method_step3_title: props.coach?.method_step3_title || '',
  method_step3_description: props.coach?.method_step3_description || '',
  pricing_title: props.coach?.pricing_title || '',
  pricing_subtitle: props.coach?.pricing_subtitle || '',
  transformations_title: props.coach?.transformations_title || '',
  transformations_subtitle: props.coach?.transformations_subtitle || '',
  final_cta_title: props.coach?.final_cta_title || '',
  final_cta_subtitle: props.coach?.final_cta_subtitle || '',
  cta_text: props.coach?.cta_text || 'Réserver une séance',
  intermediate_cta_title: props.coach?.intermediate_cta_title || '',
  intermediate_cta_subtitle: props.coach?.intermediate_cta_subtitle || '',
  satisfaction_rate: props.coach?.satisfaction_rate || 100,
  average_rating: props.coach?.average_rating || 5.0,
  linkedin_url: props.coach?.linkedin_url || '',
  youtube_url: props.coach?.youtube_url || '',
  tiktok_url: props.coach?.tiktok_url || '',
  site_layout: props.coach?.site_layout || props.defaultLayout || 'classic',
});

const dashboardBackUrl = computed(() => {
  if (typeof window === 'undefined') return route('dashboard');
  const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
  return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
  router.visit(dashboardBackUrl.value);
};

const heroTitleCount = computed(() => form.hero_title.length);
const heroSubtitleCount = computed(() => form.hero_subtitle.length);
const aboutTextCount = computed(() => form.about_text.length);
const methodTextCount = computed(() => form.method_text.length);
const ctaTextCount = computed(() => form.cta_text.length);

const completionPercentage = computed(() => {
  let filled = 0;
  let total = 5;

  if (form.hero_title.trim()) filled++;
  if (form.hero_subtitle.trim()) filled++;
  if (form.about_text.trim()) filled++;
  if (form.method_text.trim()) filled++;
  if (form.cta_text.trim()) filled++;

  return Math.round((filled / total) * 100);
});

const scrollToSection = (id) => {
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

const submit = () => {
  form.post(route('dashboard.content.update'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.success('Contenu mis à jour', {
        description: 'Vos textes sont désormais en ligne.',
      });
    },
    onError: () => {
      toast.error('Impossible de sauvegarder', {
        description: 'Corrigez les champs requis puis réessayez.',
      });
    },
  });
};

// Profile Photo Management
const photoInput = ref(null);
const photoPreview = ref(props.profilePhotoUrl);

const selectPhoto = () => {
  photoInput.value?.click();
};

const updatePhotoPreview = () => {
  const photo = photoInput.value?.files[0];

  if (!photo) return;

  const reader = new FileReader();

  reader.onload = (e) => {
    photoPreview.value = e.target.result;
  };

  reader.readAsDataURL(photo);
};

const uploadPhoto = () => {
  if (!photoInput.value?.files[0]) return;

  router.post(
    route('dashboard.content.profile-photo.upload'),
    {
      profile_photo: photoInput.value.files[0],
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['profilePhotoUrl'] });
        toast.success('Photo envoyée');
      },
      onError: () => {
        toast.error('Échec de l’envoi de la photo');
      },
    },
  );
};

const deletePhoto = () => {
  if (
    !confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')
  ) {
    return;
  }

  router.delete(
    route('dashboard.content.profile-photo.delete'),
    {
      preserveScroll: true,
      onSuccess: () => {
        photoPreview.value = null;
        router.reload({ only: ['profilePhotoUrl'] });
        toast.success('Photo supprimée');
      },
      onError: () => {
        toast.error('Impossible de supprimer la photo');
      },
    },
  );
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
  return Boolean(form.hero_title?.trim() && form.cta_text?.trim());
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
      route('dashboard.content.preview'),
      form.data(),
      {
        headers: { Accept: 'application/json' },
        withCredentials: true,
      },
    );

    previewHtml.value = data.html;
  } catch (error) {
    previewError.value =
      error.response?.data?.message || 'Impossible de générer l’aperçu pour le moment.';
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
  form,
  () => {
    schedulePreview();
  },
  { deep: true, immediate: true },
);

onBeforeUnmount(() => {
  if (previewTimeoutId) {
    clearTimeout(previewTimeoutId);
  }
});
</script>

<template>
  <Head title="Contenu du site " />

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
            <span>Contenu du site</span>
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
      class="flex-1 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8"
    >
      <div class="max-w-6xl mx-auto space-y-6">
        <!-- Completion / summary card -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h2 class="text-lg md:text-xl font-semibold mb-1">
              Complétion du contenu
            </h2>
            <p class="text-sm text-slate-400">
              {{ completionPercentage }}% des sections principales sont
              remplies.
            </p>
          </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.7fr)_minmax(320px,1fr)] lg:items-start">
          <!-- Main content form -->
          <section class="space-y-8">
            <header class="space-y-1">
              <h2 class="text-lg font-semibold">Texte et structure du site</h2>
              <p class="text-sm text-slate-400">
                Personnalisez les textes affichés sur votre site public :
                section hero, à propos, méthode, CTA et statistiques.
              </p>
            </header>

            <nav class="flex flex-wrap gap-2 text-[11px] text-slate-300 pt-1">
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('content-hero')"
              >
                <FileText class="h-3 w-3" />
                <span>Bannière</span>
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('content-about')"
              >
                <User class="h-3 w-3" />
                <span>À propos</span>
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('content-method')"
              >
                <HelpCircle class="h-3 w-3" />
                <span>Méthode</span>
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('content-stats-cta')"
              >
                <FileText class="h-3 w-3" />
                <span>Stats & CTA</span>
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('content-sections')"
              >
                <FileText class="h-3 w-3" />
                <span>Sections</span>
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-slate-700 bg-slate-900 px-3 py-1 hover:border-purple-500 hover:text-purple-200"
                @click="scrollToSection('content-social')"
              >
                <User class="h-3 w-3" />
                <span>Réseaux</span>
              </button>
            </nav>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Hero section -->
              <div
                id="content-hero"
                class="space-y-4 rounded-2xl border border-slate-800 bg-slate-950/60 p-5"
              >
                <div class="flex items-center justify-between gap-3">
                  <h3 class="text-sm font-semibold flex items-center gap-2">
                    <FileText class="h-4 w-4 text-purple-300" />
                    <span>Bannière d'accueil</span>
                  </h3>
                  <span class="text-[11px] text-slate-400">
                    {{ heroTitleCount }}/255 · {{ heroSubtitleCount }}/500
                  </span>
                </div>

                <div class="space-y-3">
                  <div>
                    <InputLabel
                      for="hero_title"
                      value="Titre principal de la bannière *"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="hero_title"
                      v-model="form.hero_title"
                      type="text"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                      maxlength="255"
                      required
                    />
                    <p class="mt-1 text-[11px] text-slate-500">
                      S'affiche en gros tout en haut de votre site vitrine.
                    </p>
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.hero_title"
                    />
                  </div>

                  <div>
                    <InputLabel
                      for="hero_subtitle"
                      value="Texte sous le titre principal"
                      class="text-xs text-slate-200"
                    />
                    <textarea
                      id="hero_subtitle"
                      v-model="form.hero_subtitle"
                      rows="2"
                      class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-sm text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                      maxlength="500"
                    />
                    <p class="mt-1 text-[11px] text-slate-500">
                      S'affiche sous le titre principal sur la bannière d'accueil.
                    </p>
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.hero_subtitle"
                    />
                  </div>
                </div>
              </div>

              <!-- About section -->
              <div
                id="content-about"
                class="space-y-4 rounded-2xl border border-slate-800 bg-slate-950/60 p-5"
              >
                <div class="flex items-center justify-between gap-3">
                  <h3 class="text-sm font-semibold flex items-center gap-2">
                    <User class="h-4 w-4 text-emerald-300" />
                    <span>Section "À propos"</span>
                  </h3>
                  <span class="text-[11px] text-slate-400">
                    {{ aboutTextCount }}/5000
                  </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                  <div class="space-y-3 lg:col-span-3">
                    <textarea
                      id="about_text"
                      v-model="form.about_text"
                      rows="6"
                      class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-sm text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                      maxlength="5000"
                    />
                    <p class="text-[11px] text-slate-500">
                      S'affiche dans la section "À propos" de votre site vitrine.
                    </p>
                    <InputError
                      class="text-xs"
                      :message="form.errors.about_text"
                    />
                  </div>

                  <div class="lg:col-span-2">
                    <div
                      class="h-full rounded-2xl border border-slate-800 bg-slate-950 p-5 space-y-5"
                    >
                      <div class="flex items-center gap-3">
                        <div>
                          <p class="text-sm font-semibold">Photo de profil</p>
                          <p class="text-[11px] text-slate-400">
                            Image affichée dans la section À propos et sur votre site.
                          </p>
                        </div>
                      </div>

                      <div class="space-y-4">
                        <div class="flex justify-center">
                          <div v-if="photoPreview" class="relative">
                            <img
                              :src="photoPreview"
                              alt="Photo de profil"
                              class="h-28 w-28 rounded-full object-cover shadow-lg ring-2 ring-slate-700"
                            />
                            <button
                              type="button"
                              class="absolute -right-2 -top-2 rounded-full bg-red-600 p-1.5 text-white text-xs shadow"
                              @click="deletePhoto"
                            >
                              ✕
                            </button>
                          </div>
                          <div
                            v-else
                            class="flex h-28 w-28 items-center justify-center rounded-full bg-slate-800 text-slate-500 text-2xl"
                          >
                            ?
                          </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                          <input
                            ref="photoInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="updatePhotoPreview"
                          />
                          <button
                            type="button"
                            class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-900 hover:bg-white"
                            @click="selectPhoto"
                          >
                            Choisir une photo
                          </button>
                          <button
                            v-if="photoInput?.files?.length"
                            type="button"
                            class="inline-flex items-center rounded-full bg-emerald-500 px-3 py-1.5 text-xs font-medium text-emerald-950 hover:bg-emerald-400"
                            @click="uploadPhoto"
                          >
                            Enregistrer
                          </button>
                        </div>

                        <p class="text-[11px] text-slate-500">
                          Formats JPG, PNG, WEBP · max 2MB.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Method section -->
              <div
                id="content-method"
                class="space-y-4 rounded-2xl border border-slate-800 bg-slate-950/60 p-5"
              >
                <h3 class="text-sm font-semibold flex items-center gap-2">
                  <HelpCircle class="h-4 w-4 text-sky-300" />
                  <span>Section "Ma méthode"</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <InputLabel
                      for="method_title"
                      value="Titre de la section"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="method_title"
                      v-model="form.method_title"
                      type="text"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.method_title"
                    />
                  </div>
                  <div>
                    <InputLabel
                      for="method_subtitle"
                      value="Sous-titre"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="method_subtitle"
                      v-model="form.method_subtitle"
                      type="text"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.method_subtitle"
                    />
                  </div>
                </div>

                <div class="space-y-3">
                  <InputLabel
                    for="method_text"
                    value="Texte introductif"
                    class="text-xs text-slate-200"
                  />
                  <textarea
                    id="method_text"
                    v-model="form.method_text"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-sm text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <p class="mt-1 text-[11px] text-slate-500">
                    Texte d'introduction de votre approche, affiché avant les 3 étapes.
                  </p>
                  <InputError
                    class="mt-1 text-xs"
                    :message="form.errors.method_text"
                  />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                  <div class="space-y-2">
                    <InputLabel
                      for="method_step1_title"
                      value="Étape 1 - Titre"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="method_step1_title"
                      v-model="form.method_step1_title"
                      type="text"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <textarea
                      id="method_step1_description"
                      v-model="form.method_step1_description"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <p class="mt-1 text-[11px] text-slate-500">
                      S'affiche dans la première colonne de la section "Ma méthode".
                    </p>
                  </div>

                  <div class="space-y-2">
                    <InputLabel
                      for="method_step2_title"
                      value="Étape 2 - Titre"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="method_step2_title"
                      v-model="form.method_step2_title"
                      type="text"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <textarea
                      id="method_step2_description"
                      v-model="form.method_step2_description"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>

                  <div class="space-y-2">
                    <InputLabel
                      for="method_step3_title"
                      value="Étape 3 - Titre"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="method_step3_title"
                      v-model="form.method_step3_title"
                      type="text"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <textarea
                      id="method_step3_description"
                      v-model="form.method_step3_description"
                      rows="3"
                      class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <p class="mt-1 text-[11px] text-slate-500">
                      S'affiche dans la troisième colonne de la section "Ma méthode".
                    </p>
                  </div>
                </div>
              </div>

              <!-- Stats and CTA -->
              <div
                id="content-stats-cta"
                class="space-y-4 rounded-2xl border border-slate-800 bg-slate-950/60 p-5"
              >
                <h3 class="text-sm font-semibold flex items-center gap-2">
                  <FileText class="h-4 w-4 text-emerald-300" />
                  <span>Statistiques & appels à l'action</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-3">
                    <h3 class="text-sm font-semibold">Statistiques</h3>
                    <div>
                      <InputLabel
                        for="satisfaction_rate"
                        value="Taux de satisfaction (%) *"
                        class="text-xs text-slate-200"
                      />
                      <div class="mt-1 flex items-center gap-3">
                        <input
                          id="satisfaction_rate"
                          v-model.number="form.satisfaction_rate"
                          type="number"
                          min="0"
                          max="100"
                          class="block w-full rounded-md border-slate-700 bg-slate-950 text-sm text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                          required
                        />
                        <span class="text-lg font-semibold text-emerald-400">
                          {{ form.satisfaction_rate }}%
                        </span>
                      </div>
                      <InputError
                        class="mt-1 text-xs"
                        :message="form.errors.satisfaction_rate"
                      />
                    </div>

                    <div>
                      <InputLabel
                        for="average_rating"
                        value="Note moyenne (étoiles) *"
                        class="text-xs text-slate-200"
                      />
                      <div class="mt-1 flex items-center gap-3">
                        <input
                          id="average_rating"
                          v-model.number="form.average_rating"
                          type="number"
                          min="0"
                          max="5"
                          step="0.1"
                          class="block w-full rounded-md border-slate-700 bg-slate-950 text-sm text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                          required
                        />
                        <span class="text-lg font-semibold text-amber-400">
                          {{ form.average_rating }}★
                        </span>
                      </div>
                      <InputError
                        class="mt-1 text-xs"
                        :message="form.errors.average_rating"
                      />
                    </div>
                  </div>

                  <div class="space-y-3">
                    <h3 class="text-sm font-semibold">Appels à l'action</h3>

                    <div>
                      <InputLabel
                        for="cta_text"
                        value="Texte du bouton principal *"
                        class="text-xs text-slate-200"
                      />
                      <TextInput
                        id="cta_text"
                        v-model="form.cta_text"
                        type="text"
                        class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                        maxlength="100"
                        required
                      />
                      <p class="mt-1 text-[11px] text-slate-400">
                        {{ ctaTextCount }}/100 caractères
                      </p>
                      <InputError
                        class="mt-1 text-xs"
                        :message="form.errors.cta_text"
                      />
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                      <div>
                        <InputLabel
                          for="intermediate_cta_title"
                          value="Titre CTA intermédiaire"
                          class="text-xs text-slate-200"
                        />
                        <TextInput
                          id="intermediate_cta_title"
                          v-model="form.intermediate_cta_title"
                          type="text"
                          class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                        />
                      </div>
                      <div>
                        <InputLabel
                          for="intermediate_cta_subtitle"
                          value="Sous-titre CTA intermédiaire"
                          class="text-xs text-slate-200"
                        />
                        <textarea
                          id="intermediate_cta_subtitle"
                          v-model="form.intermediate_cta_subtitle"
                          rows="2"
                          class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Sections titres supplémentaires -->
              <div
                id="content-sections"
                class="space-y-4 rounded-2xl border border-slate-800 bg-slate-950/60 p-5 mt-4"
              >
                <h3 class="text-sm font-semibold flex items-center gap-2">
                  <FileText class="h-4 w-4 text-purple-300" />
                  <span>Sections de page</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="space-y-3">
                    <InputLabel
                      for="pricing_title"
                      value="Titre section tarifs"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="pricing_title"
                      v-model="form.pricing_title"
                      type="text"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <textarea
                      id="pricing_subtitle"
                      v-model="form.pricing_subtitle"
                      rows="2"
                      class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>

                  <div class="space-y-3">
                    <InputLabel
                      for="transformations_title"
                      value="Titre section transformations"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="transformations_title"
                      v-model="form.transformations_title"
                      type="text"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <textarea
                      id="transformations_subtitle"
                      v-model="form.transformations_subtitle"
                      rows="2"
                      class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                    />
                  </div>
                </div>

                <div class="space-y-3">
                  <InputLabel
                    for="final_cta_title"
                    value="Titre section finale"
                    class="text-xs text-slate-200"
                  />
                  <TextInput
                    id="final_cta_title"
                    v-model="form.final_cta_title"
                    type="text"
                    class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                  />
                  <textarea
                    id="final_cta_subtitle"
                    v-model="form.final_cta_subtitle"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                  />
                </div>
              </div>

              <!-- Social links -->
              <div
                id="content-social"
                class="space-y-4 rounded-2xl border border-slate-800 bg-slate-950/60 p-5 mt-4"
              >
                <div class="flex items-center justify-between gap-3">
                  <div>
                    <h3 class="text-sm font-semibold flex items-center gap-2">
                      <Share2 class="h-4 w-4 text-sky-300" />
                      <span>Réseaux sociaux</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">
                      Liens optionnels affichés dans les sections sociales de votre
                      site.
                    </p>
                  </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <InputLabel
                      for="facebook_url"
                      value="Facebook"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="facebook_url"
                      v-model="form.facebook_url"
                      type="url"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.facebook_url"
                    />
                  </div>
                  <div>
                    <InputLabel
                      for="instagram_url"
                      value="Instagram"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="instagram_url"
                      v-model="form.instagram_url"
                      type="url"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.instagram_url"
                    />
                  </div>
                  <div>
                    <InputLabel
                      for="twitter_url"
                      value="Twitter / X"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="twitter_url"
                      v-model="form.twitter_url"
                      type="url"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.twitter_url"
                    />
                  </div>
                  <div>
                    <InputLabel
                      for="linkedin_url"
                      value="LinkedIn"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="linkedin_url"
                      v-model="form.linkedin_url"
                      type="url"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.linkedin_url"
                    />
                  </div>
                  <div>
                    <InputLabel
                      for="youtube_url"
                      value="YouTube"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="youtube_url"
                      v-model="form.youtube_url"
                      type="url"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.youtube_url"
                    />
                  </div>
                  <div>
                    <InputLabel
                      for="tiktok_url"
                      value="TikTok"
                      class="text-xs text-slate-200"
                    />
                    <TextInput
                      id="tiktok_url"
                      v-model="form.tiktok_url"
                      type="url"
                      class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                    />
                    <InputError
                      class="mt-1 text-xs"
                      :message="form.errors.tiktok_url"
                    />
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
                    Les modifications se reflètent automatiquement après quelques secondes.
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

              <div class="mt-4">
                <InputLabel
                  for="site_layout"
                  value="Layout affiché"
                  class="text-xs text-slate-200"
                />
                <div class="mt-2 flex flex-wrap gap-2">
                  <button
                    v-for="layout in siteLayouts"
                    :key="layout.key"
                    type="button"
                    class="flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs transition-all"
                    :class="[
                      form.site_layout === layout.key
                        ? 'border-indigo-500 bg-indigo-500/10 text-indigo-100'
                        : 'border-slate-700 bg-slate-900 text-slate-300 hover:border-slate-500',
                    ]"
                    @click="form.site_layout = layout.key"
                  >
                    <span class="font-semibold">{{ layout.label }}</span>
                  </button>
                </div>
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
                    <p>Complétez au minimum le titre hero et le texte du CTA pour démarrer l’aperçu.</p>
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
                Complétez le titre hero et le CTA pour générer un aperçu.
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
