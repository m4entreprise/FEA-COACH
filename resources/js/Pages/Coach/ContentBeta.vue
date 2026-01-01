<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { FileText, User, HelpCircle, Share2 } from 'lucide-vue-next';

const props = defineProps({
  coach: Object,
  faqs: Array,
  faqsCount: Number,
  faqsActiveCount: Number,
  profilePhotoUrl: String,
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
  facebook_url: props.coach?.facebook_url || '',
  instagram_url: props.coach?.instagram_url || '',
  twitter_url: props.coach?.twitter_url || '',
  linkedin_url: props.coach?.linkedin_url || '',
  youtube_url: props.coach?.youtube_url || '',
  tiktok_url: props.coach?.tiktok_url || '',
});

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
  form.post(route('dashboard.content.update', { beta: 1 }), {
    preserveScroll: true,
  });
};

// FAQ Management
const showFaqModal = ref(false);
const editingFaq = ref(null);

const faqForm = useForm({
  question: '',
  answer: '',
  order: 0,
  is_active: true,
});

const openCreateFaqModal = () => {
  editingFaq.value = null;
  faqForm.reset();
  faqForm.clearErrors();
  faqForm.is_active = true;
  faqForm.order = 0;
  showFaqModal.value = true;
};

const openEditFaqModal = (faq) => {
  editingFaq.value = faq;
  faqForm.question = faq.question;
  faqForm.answer = faq.answer;
  faqForm.order = faq.order;
  faqForm.is_active = faq.is_active;
  faqForm.clearErrors();
  showFaqModal.value = true;
};

const closeFaqModal = () => {
  showFaqModal.value = false;
  editingFaq.value = null;
  faqForm.reset();
  faqForm.clearErrors();
};

const submitFaq = () => {
  if (editingFaq.value) {
    faqForm.patch(
      route('dashboard.faq.update', { faq: editingFaq.value.id, beta: 1 }),
      {
        preserveScroll: true,
        onSuccess: () => {
          closeFaqModal();
          router.reload({ only: ['faqs', 'faqsCount', 'faqsActiveCount'] });
        },
      },
    );
  } else {
    faqForm.post(route('dashboard.faq.store', { beta: 1 }), {
      preserveScroll: true,
      onSuccess: () => {
        closeFaqModal();
        router.reload({ only: ['faqs', 'faqsCount', 'faqsActiveCount'] });
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
      router.reload({ only: ['faqs', 'faqsCount', 'faqsActiveCount'] });
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
    route('dashboard.content.profile-photo.upload', { beta: 1 }),
    {
      profile_photo: photoInput.value.files[0],
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['profilePhotoUrl'] });
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
    route('dashboard.content.profile-photo.delete', { beta: 1 }),
    {
      preserveScroll: true,
      onSuccess: () => {
        photoPreview.value = null;
        router.reload({ only: ['profilePhotoUrl'] });
      },
    },
  );
};
</script>

<template>
  <Head title="Contenu du site (beta)" />

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
            <span>Contenu du site</span>
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
          <div class="flex flex-col items-end gap-2 text-xs text-slate-400">
            <p>
              FAQ actives :
              <span class="font-semibold text-slate-100">
                {{ faqsActiveCount }}/{{ faqsCount }}
              </span>
            </p>
          </div>
        </section>

        <div class="space-y-6">
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

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
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
                      class="h-full rounded-2xl border border-slate-800 bg-slate-950/60 p-4 space-y-4"
                    >
                      <div class="flex items-center gap-3">
                        <div
                          class="h-9 w-9 rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 flex items-center justify-center text-sm"
                        >
                          📸
                        </div>
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

          <!-- Side column: photo + FAQ -->
          <div class="space-y-6">
            <section
              class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl space-y-4"
            >
              <header class="flex items-center justify-between gap-3">
                <div class="space-y-1">
                  <h3 class="text-sm font-semibold">Aperçu de votre site</h3>
                  <p class="text-xs text-slate-400">
                    Visualisation simplifiée de ce que verront vos visiteurs.
                  </p>
                </div>
              </header>

              <div
                class="rounded-xl bg-slate-950/80 border border-slate-800 p-4 space-y-4 text-xs"
              >
                <div class="space-y-2">
                  <p
                    class="text-[10px] uppercase tracking-wide text-slate-500"
                  >
                    Bannière d'accueil
                  </p>
                  <p class="text-sm font-semibold text-slate-50">
                    {{ form.hero_title || 'Titre principal de votre page' }}
                  </p>
                  <p class="text-[11px] text-slate-300">
                    {{
                      form.hero_subtitle ||
                      "Sous-titre d'accroche qui explique votre promesse."
                    }}
                  </p>
                  <button
                    type="button"
                    class="mt-2 inline-flex items-center rounded-full bg-emerald-500 px-3 py-1.5 text-[11px] font-semibold text-emerald-950"
                  >
                    {{ form.cta_text || 'Réserver une séance' }}
                  </button>
                </div>

                <div
                  class="pt-3 mt-2 border-t border-slate-800 space-y-2"
                >
                  <p
                    class="text-[10px] uppercase tracking-wide text-slate-500"
                  >
                    Section "À propos"
                  </p>
                  <p class="text-[11px] text-slate-300 whitespace-pre-line">
                    {{
                      form.about_text ||
                      'Texte de présentation qui raconte votre parcours et votre manière de travailler.'
                    }}
                  </p>
                </div>

                <div
                  class="pt-3 mt-2 border-t border-slate-800 space-y-2"
                >
                  <p
                    class="text-[10px] uppercase tracking-wide text-slate-500"
                  >
                    Section "Ma méthode"
                  </p>
                  <p class="text-[11px] font-semibold text-slate-100">
                    {{ form.method_title || 'Ma méthode en 3 étapes' }}
                  </p>
                  <ul class="text-[11px] text-slate-300 space-y-1 list-disc ml-4">
                    <li v-if="form.method_step1_title">
                      {{ form.method_step1_title }}
                    </li>
                    <li v-if="form.method_step2_title">
                      {{ form.method_step2_title }}
                    </li>
                    <li v-if="form.method_step3_title">
                      {{ form.method_step3_title }}
                    </li>
                    <li v-if="!form.method_step1_title && !form.method_step2_title && !form.method_step3_title">
                      Exemple : Bilan, Programme, Suivi.
                    </li>
                  </ul>
                </div>

                <div
                  class="pt-3 mt-2 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-300"
                >
                  <div>
                    <p class="font-semibold text-emerald-300">
                      {{ form.satisfaction_rate }}% de clients satisfaits
                    </p>
                  </div>
                  <div>
                    <p class="font-semibold text-amber-300">
                      {{ form.average_rating }}★ sur 5
                    </p>
                  </div>
                </div>
              </div>

              <p class="text-[10px] text-slate-500">
                Cet aperçu est indicatif : le design final de votre site peut
                être différent.
              </p>
            </section>

            <!-- FAQ card -->
            <section
              class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl space-y-4"
            >
              <header class="flex items-center justify-between gap-3">
                <div class="space-y-1">
                  <h3 class="text-sm font-semibold">FAQ du site</h3>
                  <p class="text-xs text-slate-400">
                    Gérez les questions fréquentes affichées sur votre site.
                  </p>
                </div>
                <button
                  type="button"
                  class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-900 hover:bg-white"
                  @click="openCreateFaqModal"
                >
                  + Nouvelle question
                </button>
              </header>

              <div v-if="faqs && faqs.length" class="space-y-3 text-xs">
                <div
                  v-for="faq in faqs"
                  :key="faq.id"
                  class="rounded-xl border border-slate-800 bg-slate-950/60 p-3 space-y-1"
                >
                  <div class="flex items-start justify-between gap-2">
                    <p class="font-medium text-slate-100">
                      {{ faq.question }}
                    </p>
                    <span
                      class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium"
                      :class="
                        faq.is_active
                          ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'
                          : 'bg-slate-800 text-slate-300 border border-slate-700'
                      "
                    >
                      {{ faq.is_active ? 'Active' : 'Masquée' }}
                    </span>
                  </div>
                  <p class="text-slate-400">
                    {{ faq.answer }}
                  </p>
                  <div class="flex justify-end gap-2 pt-1 text-[11px]">
                    <button
                      type="button"
                      class="text-slate-300 hover:text-white"
                      @click="openEditFaqModal(faq)"
                    >
                      Modifier
                    </button>
                    <button
                      type="button"
                      class="text-rose-300 hover:text-rose-200"
                      @click="deleteFaq(faq)"
                    >
                      Supprimer
                    </button>
                  </div>
                </div>
              </div>
              <p v-else class="text-xs text-slate-500">
                Aucune question pour le moment. Créez votre première FAQ pour
                répondre aux objections de vos prospects.
              </p>
            </section>
          </div>
        </div>
      </div>

      <!-- FAQ Modal -->
      <div
        v-if="showFaqModal"
        class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 px-4"
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
              @click="closeFaqModal"
            >
              ✕
            </button>
          </div>

          <form @submit.prevent="submitFaq" class="space-y-4">
            <div>
              <InputLabel
                for="faq_question"
                value="Question"
                class="text-xs text-slate-200"
              />
              <TextInput
                id="faq_question"
                v-model="faqForm.question"
                type="text"
                class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                required
              />
              <InputError
                class="mt-1 text-xs"
                :message="faqForm.errors.question"
              />
            </div>

            <div>
              <InputLabel
                for="faq_answer"
                value="Réponse"
                class="text-xs text-slate-200"
              />
              <textarea
                id="faq_answer"
                v-model="faqForm.answer"
                rows="4"
                class="mt-1 block w-full rounded-md border-slate-700 bg-slate-950 text-xs text-slate-50 focus:border-indigo-500 focus:ring-indigo-500"
                required
              />
              <InputError
                class="mt-1 text-xs"
                :message="faqForm.errors.answer"
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
                  v-model.number="faqForm.order"
                  type="number"
                  min="0"
                  class="mt-1 block w-full bg-slate-950 border-slate-700 text-slate-50"
                />
              </div>

              <label class="flex items-center gap-2 text-xs text-slate-200">
                <input
                  v-model="faqForm.is_active"
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
                @click="closeFaqModal"
              >
                Annuler
              </button>
              <button
                type="submit"
                class="rounded-full bg-indigo-500 px-4 py-1.5 font-medium text-slate-950 hover:bg-indigo-400 disabled:opacity-50"
                :disabled="faqForm.processing"
              >
                {{ faqForm.processing ? 'Enregistrement...' : 'Enregistrer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>
