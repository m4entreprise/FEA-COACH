<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import InputError from '@/Components/InputError.vue';
import { Building2, User, Briefcase, Scale, Eye, Save, Copy, AlertCircle, FileText, Sparkles } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
  coach: Object,
  user: Object,
});

const form = useForm({
  // Identité entité
  entity_type: props.user?.entity_type || 'PP',
  legal_name: props.user?.legal_name || '',
  company_number: props.user?.company_number || '',
  legal_representative: props.user?.legal_representative || '',
  phone_contact: props.user?.phone_contact || '',
  vat_number: props.user?.vat_number || '',
  legal_address: props.user?.legal_address || '',
  
  // Types de services
  is_coaching_presentiel: props.coach?.is_coaching_presentiel || false,
  is_coaching_online: props.coach?.is_coaching_online || false,
  has_digital_products: props.coach?.has_digital_products || false,
  has_subscriptions: props.coach?.has_subscriptions || false,
  use_client_photos: props.coach?.use_client_photos || false,
  
  // Règles métier
  vat_regime: props.coach?.vat_regime || 'ASSUJETTI',
  cancellation_delay: props.coach?.cancellation_delay || 24,
  tribunal_city: props.coach?.tribunal_city || 'Bruxelles',
  insurance_company: props.coach?.insurance_company || '',
  insurance_policy_number: props.coach?.insurance_policy_number || '',
});

// Preview management
const previewHtml = ref('');
const previewLoading = ref(false);
const previewError = ref(null);
const showPreview = ref(false);
let previewTimeoutId = null;

const hasPreviewRequirements = computed(() => {
  return Boolean(
    form.legal_name?.trim() && 
    form.company_number?.trim() && 
    form.legal_address?.trim() &&
    form.tribunal_city?.trim()
  );
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
      route('api.legal.generate-preview'),
      {
        ...form.data(),
        nom_commercial: props.coach.name,
        email: props.user.email,
      },
      {
        headers: { Accept: 'application/json' },
        withCredentials: true,
      }
    );

    previewHtml.value = data.html;
  } catch (error) {
    previewError.value =
      error.response?.data?.message || 'Impossible de générer l\'aperçu pour le moment.';
  } finally {
    previewLoading.value = false;
  }
};

const schedulePreview = () => {
  if (previewTimeoutId) {
    clearTimeout(previewTimeoutId);
  }

  previewTimeoutId = window.setTimeout(() => {
    if (showPreview.value) {
      fetchPreview();
    }
  }, 800);
};

watch(
  form,
  () => {
    schedulePreview();
  },
  { deep: true }
);

watch(showPreview, (value) => {
  if (value && hasPreviewRequirements.value) {
    fetchPreview();
  }
});

onBeforeUnmount(() => {
  if (previewTimeoutId) {
    clearTimeout(previewTimeoutId);
  }
});

const submitForm = () => {
  form.post(route('dashboard.legal.update'), {
    preserveScroll: true,
    onSuccess: () => {
      if (showPreview.value) {
        fetchPreview();
      }
    },
  });
};

const copyToClipboard = () => {
  if (previewHtml.value) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = previewHtml.value;
    const text = tempDiv.textContent || tempDiv.innerText || '';
    navigator.clipboard.writeText(text);
    alert('Contenu copié dans le presse-papier !');
  }
};

const completionPercentage = computed(() => {
  let filled = 0;
  let total = 8;

  if (form.legal_name?.trim()) filled++;
  if (form.company_number?.trim()) filled++;
  if (form.legal_address?.trim()) filled++;
  if (form.tribunal_city?.trim()) filled++;
  if (form.is_coaching_presentiel || form.is_coaching_online) filled++;
  if (form.vat_regime) filled++;
  if (form.cancellation_delay > 0) filled++;
  if (form.entity_type) filled++;

  return Math.round((filled / total) * 100);
});
</script>

<template>
  <Head title="Mentions légales & CGV" />

  <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
    <!-- Top bar -->
    <header
      class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl sticky top-0 z-50"
    >
      <div class="flex items-center gap-3">
        <div class="flex flex-col">
          <p class="text-xs uppercase tracking-wide text-slate-400">
            Panel coach
          </p>
          <h1 class="text-base md:text-lg font-semibold flex items-center gap-2">
            <FileText class="w-4 h-4" />
            <span>Mentions légales & CGV</span>
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden md:flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5">
          <Sparkles class="w-3 h-3 text-purple-400" />
          <span class="text-xs text-slate-300">{{ completionPercentage }}% complété</span>
        </div>
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
      class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"
    >
      <div class="lg:grid lg:grid-cols-2 lg:gap-6 lg:h-full">
        <!-- Form Column -->
        <div class="px-4 md:px-6 py-6 md:py-8 lg:overflow-y-auto">
          <div class="max-w-2xl mx-auto lg:max-w-none space-y-6">
            <!-- Intro -->
            <div class="rounded-2xl border border-purple-500/20 bg-gradient-to-r from-purple-900/20 to-pink-900/20 p-5 shadow-xl">
              <div class="flex gap-3">
                <AlertCircle class="w-5 h-5 text-purple-400 flex-shrink-0 mt-0.5" />
                <div class="space-y-2">
                  <h2 class="text-sm font-semibold text-purple-100">Générateur intelligent</h2>
                  <p class="text-xs text-slate-300">
                    Créez vos CGV et politique de confidentialité conformes au droit belge. Remplissez les informations ci-dessous et l'aperçu se génère automatiquement.
                  </p>
                  <p class="text-[11px] text-purple-200/80 flex items-center gap-1.5">
                    <Scale class="w-3 h-3" />
                    <span>Validé par juriste • Conforme RGPD • Livre XIX CDE</span>
                  </p>
                </div>
              </div>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
              <!-- Section 1: Identité de l'entité -->
              <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl space-y-4">
                <div class="flex items-center gap-2 mb-2">
                  <Building2 class="w-5 h-5 text-purple-400" />
                  <h2 class="text-sm font-semibold">1. Identité de l'entité</h2>
                </div>

                <!-- Type d'entité -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Type d'entité <span class="text-red-400">*</span>
                  </label>
                  <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                      <input
                        type="radio"
                        v-model="form.entity_type"
                        value="PP"
                        class="text-purple-500 focus:ring-purple-500"
                      />
                      <span class="text-xs text-slate-200">Personne Physique</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                      <input
                        type="radio"
                        v-model="form.entity_type"
                        value="SOC"
                        class="text-purple-500 focus:ring-purple-500"
                      />
                      <span class="text-xs text-slate-200">Société</span>
                    </label>
                  </div>
                  <p class="text-[11px] text-slate-400">
                    Choisissez si vous exercez en tant qu'indépendant ou via une société.
                  </p>
                  <InputError :message="form.errors.entity_type" class="mt-1 text-[11px]" />
                </div>

                <!-- Nom légal -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Nom légal <span class="text-red-400">*</span>
                  </label>
                  <input
                    v-model="form.legal_name"
                    type="text"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Jean Dupont ou FitCoach SPRL"
                  />
                  <p class="text-[11px] text-slate-400">
                    Votre nom complet (PP) ou dénomination sociale (SOC).
                  </p>
                  <InputError :message="form.errors.legal_name" class="mt-1 text-[11px]" />
                </div>

                <!-- Numéro BCE -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Numéro BCE <span class="text-red-400">*</span>
                  </label>
                  <input
                    v-model="form.company_number"
                    type="text"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="0xxx.xxx.xxx"
                  />
                  <p class="text-[11px] text-slate-400">
                    Numéro d'entreprise (vérifiez sur kbopub.economie.fgov.be).
                  </p>
                  <InputError :message="form.errors.company_number" class="mt-1 text-[11px]" />
                </div>

                <!-- Représentant légal (si société) -->
                <div v-if="form.entity_type === 'SOC'" class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Représentant légal
                  </label>
                  <input
                    v-model="form.legal_representative"
                    type="text"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Marie Dubois, Gérante"
                  />
                  <p class="text-[11px] text-slate-400">
                    Nom et fonction du représentant légal.
                  </p>
                  <InputError :message="form.errors.legal_representative" class="mt-1 text-[11px]" />
                </div>

                <!-- Adresse -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Adresse du siège <span class="text-red-400">*</span>
                  </label>
                  <textarea
                    v-model="form.legal_address"
                    rows="2"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Rue de la Santé 42, 1000 Bruxelles"
                  ></textarea>
                  <InputError :message="form.errors.legal_address" class="mt-1 text-[11px]" />
                </div>

                <!-- TVA -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Numéro de TVA
                  </label>
                  <input
                    v-model="form.vat_number"
                    type="text"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="BE0xxx.xxx.xxx"
                  />
                  <p class="text-[11px] text-slate-400">
                    Si vous êtes assujetti à la TVA.
                  </p>
                  <InputError :message="form.errors.vat_number" class="mt-1 text-[11px]" />
                </div>

                <!-- Téléphone -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Téléphone professionnel
                  </label>
                  <input
                    v-model="form.phone_contact"
                    type="text"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="+32 xxx xx xx xx"
                  />
                  <InputError :message="form.errors.phone_contact" class="mt-1 text-[11px]" />
                </div>
              </section>

              <!-- Section 2: Types de services -->
              <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl space-y-4">
                <div class="flex items-center gap-2 mb-2">
                  <Briefcase class="w-5 h-5 text-blue-400" />
                  <h2 class="text-sm font-semibold">2. Types de services</h2>
                </div>
                <p class="text-[11px] text-slate-400 -mt-2">
                  Cochez les services que vous proposez pour adapter les articles des CGV.
                </p>

                <div class="space-y-3">
                  <label class="flex items-start gap-3 cursor-pointer group">
                    <input
                      type="checkbox"
                      v-model="form.is_coaching_presentiel"
                      class="mt-0.5 rounded text-purple-500 focus:ring-purple-500"
                    />
                    <div>
                      <span class="text-xs text-slate-200 font-medium">Coaching en présentiel</span>
                      <p class="text-[11px] text-slate-400">Séances en salle, à domicile ou en extérieur</p>
                    </div>
                  </label>

                  <label class="flex items-start gap-3 cursor-pointer group">
                    <input
                      type="checkbox"
                      v-model="form.is_coaching_online"
                      class="mt-0.5 rounded text-purple-500 focus:ring-purple-500"
                    />
                    <div>
                      <span class="text-xs text-slate-200 font-medium">Coaching en ligne</span>
                      <p class="text-[11px] text-slate-400">Visio, suivi via application</p>
                    </div>
                  </label>

                  <label class="flex items-start gap-3 cursor-pointer group">
                    <input
                      type="checkbox"
                      v-model="form.has_digital_products"
                      class="mt-0.5 rounded text-purple-500 focus:ring-purple-500"
                    />
                    <div>
                      <span class="text-xs text-slate-200 font-medium">Produits numériques</span>
                      <p class="text-[11px] text-slate-400">Ebooks, PDF, programmes vidéo</p>
                    </div>
                  </label>

                  <label class="flex items-start gap-3 cursor-pointer group">
                    <input
                      type="checkbox"
                      v-model="form.has_subscriptions"
                      class="mt-0.5 rounded text-purple-500 focus:ring-purple-500"
                    />
                    <div>
                      <span class="text-xs text-slate-200 font-medium">Abonnements récurrents</span>
                      <p class="text-[11px] text-slate-400">Formules mensuelles avec reconduction</p>
                    </div>
                  </label>

                  <label class="flex items-start gap-3 cursor-pointer group">
                    <input
                      type="checkbox"
                      v-model="form.use_client_photos"
                      class="mt-0.5 rounded text-purple-500 focus:ring-purple-500"
                    />
                    <div>
                      <span class="text-xs text-slate-200 font-medium">Photos avant/après</span>
                      <p class="text-[11px] text-slate-400">Utilisation à des fins promotionnelles</p>
                    </div>
                  </label>
                </div>
              </section>

              <!-- Section 3: Règles métier -->
              <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl space-y-4">
                <div class="flex items-center gap-2 mb-2">
                  <Scale class="w-5 h-5 text-green-400" />
                  <h2 class="text-sm font-semibold">3. Règles métier</h2>
                </div>

                <!-- Régime TVA -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Régime TVA <span class="text-red-400">*</span>
                  </label>
                  <select
                    v-model="form.vat_regime"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                  >
                    <option value="ASSUJETTI">Assujetti (21%)</option>
                    <option value="FRANCHISE">Franchise de TVA</option>
                  </select>
                  <p class="text-[11px] text-slate-400">
                    Assujetti si vous facturez la TVA, Franchise si exempté.
                  </p>
                  <InputError :message="form.errors.vat_regime" class="mt-1 text-[11px]" />
                </div>

                <!-- Délai annulation -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Délai d'annulation (heures) <span class="text-red-400">*</span>
                  </label>
                  <input
                    v-model.number="form.cancellation_delay"
                    type="number"
                    min="0"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                  />
                  <p class="text-[11px] text-slate-400">
                    Nombre d'heures minimum pour annuler sans frais (ex: 24 ou 48).
                  </p>
                  <InputError :message="form.errors.cancellation_delay" class="mt-1 text-[11px]" />
                </div>

                <!-- Tribunal -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Tribunal compétent <span class="text-red-400">*</span>
                  </label>
                  <input
                    v-model="form.tribunal_city"
                    type="text"
                    class="w-full rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                    placeholder="Bruxelles"
                  />
                  <p class="text-[11px] text-slate-400">
                    Ville du tribunal en cas de litige B2B (pour les consommateurs, tribunal de leur domicile).
                  </p>
                  <InputError :message="form.errors.tribunal_city" class="mt-1 text-[11px]" />
                </div>

                <!-- Assurance -->
                <div class="space-y-2">
                  <label class="block text-[11px] font-semibold text-slate-200">
                    Assurance RC professionnelle
                  </label>
                  <div class="grid grid-cols-2 gap-3">
                    <input
                      v-model="form.insurance_company"
                      type="text"
                      class="rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                      placeholder="AXA, Ethias..."
                    />
                    <input
                      v-model="form.insurance_policy_number"
                      type="text"
                      class="rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                      placeholder="N° de police"
                    />
                  </div>
                  <p class="text-[11px] text-slate-400">
                    Optionnel mais recommandé pour rassurer vos clients.
                  </p>
                  <InputError :message="form.errors.insurance_company" class="mt-1 text-[11px]" />
                </div>
              </section>

              <!-- Submit buttons -->
              <div class="flex justify-end gap-2 pt-2">
                <button
                  type="button"
                  class="rounded-full border border-slate-700 px-4 py-2 text-xs text-slate-200 hover:bg-slate-800"
                  @click="form.reset()"
                >
                  Réinitialiser
                </button>
                <button
                  type="submit"
                  class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-5 py-2 text-xs font-semibold text-white hover:from-purple-600 hover:to-pink-600 disabled:opacity-60 inline-flex items-center gap-2"
                  :disabled="form.processing"
                >
                  <Save class="w-3.5 h-3.5" />
                  <span>{{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Preview Column -->
        <div class="border-t lg:border-t-0 lg:border-l border-slate-800 bg-slate-950/50 px-4 md:px-6 py-6 md:py-8 lg:overflow-y-auto lg:h-full">
          <div class="max-w-2xl mx-auto lg:max-w-none space-y-4 sticky top-6">
            <!-- Preview Header -->
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <Eye class="w-4 h-4 text-slate-400" />
                <h2 class="text-sm font-semibold text-slate-200">Aperçu</h2>
              </div>
              <div class="flex gap-2">
                <button
                  type="button"
                  @click="showPreview = !showPreview"
                  class="px-3 py-1.5 rounded-full text-xs font-medium border border-slate-700 hover:bg-slate-800"
                  :class="showPreview ? 'bg-slate-800 text-slate-100' : 'text-slate-400'"
                >
                  {{ showPreview ? 'Masquer' : 'Afficher' }}
                </button>
                <button
                  v-if="previewHtml"
                  type="button"
                  @click="copyToClipboard"
                  class="px-3 py-1.5 rounded-full text-xs font-medium bg-slate-800 text-slate-100 hover:bg-slate-700 inline-flex items-center gap-1.5"
                >
                  <Copy class="w-3 h-3" />
                  <span>Copier</span>
                </button>
              </div>
            </div>

            <!-- Preview Content -->
            <div v-if="showPreview" class="rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl overflow-hidden">
              <!-- Loading State -->
              <div v-if="previewLoading" class="p-8 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mx-auto mb-3"></div>
                <p class="text-xs text-slate-400">Génération en cours...</p>
              </div>

              <!-- Error State -->
              <div v-else-if="previewError" class="p-8 text-center">
                <AlertCircle class="w-8 h-8 text-red-400 mx-auto mb-3" />
                <p class="text-xs text-red-300">{{ previewError }}</p>
              </div>

              <!-- Empty State -->
              <div v-else-if="!previewHtml && !previewLoading" class="p-8 text-center">
                <FileText class="w-12 h-12 text-slate-600 mx-auto mb-3" />
                <p class="text-xs text-slate-400 mb-1">Remplissez les champs obligatoires</p>
                <p class="text-[11px] text-slate-500">
                  L'aperçu se générera automatiquement
                </p>
              </div>

              <!-- Preview HTML -->
              <div
                v-else
                v-html="previewHtml"
                class="p-6 text-xs text-slate-100 max-h-[calc(100vh-200px)] overflow-y-auto prose prose-invert prose-sm max-w-none
                       prose-headings:text-slate-100 prose-p:text-slate-300 prose-li:text-slate-300
                       prose-strong:text-slate-200 prose-a:text-purple-400"
              ></div>
            </div>

            <!-- Warning -->
            <div v-if="showPreview && previewHtml" class="rounded-xl border border-yellow-500/20 bg-yellow-900/10 p-4">
              <div class="flex gap-2">
                <AlertCircle class="w-4 h-4 text-yellow-400 flex-shrink-0 mt-0.5" />
                <div class="text-[11px] text-yellow-200/80">
                  <p class="font-medium mb-1">Validation juridique</p>
                  <p>Ce document a été validé par un juriste belge. Pensez néanmoins à le faire relire par votre conseiller juridique pour adapter certains détails à votre situation spécifique.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
