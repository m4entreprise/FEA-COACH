<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  coach: Object,
  defaultLegalTerms: String,
  user: Object,
});

const showPreview = ref(false);

const form = useForm({
  vat_number: props.user?.vat_number || '',
  legal_terms: props.coach.legal_terms || props.defaultLegalTerms,
});

const submitForm = () => {
  form.post(route('dashboard.legal.update'), {
    preserveScroll: true,
  });
};

const useDefaultTemplate = () => {
  form.legal_terms = props.defaultLegalTerms;
};

const copyToClipboard = () => {
  navigator.clipboard.writeText(form.legal_terms);
  alert('Texte copié dans le presse-papier !');
};
</script>

<template>
  <Head title="Mentions légales " />

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
            <span>Mentions légales & CGV</span>
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
        <!-- Actions rapides -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-wrap gap-3"
        >
          <button
            type="button"
            class="px-4 py-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
            @click="useDefaultTemplate"
          >
            Recharger le modèle
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded-full bg-slate-100 text-xs font-semibold text-slate-900 hover:bg-white"
            @click="copyToClipboard"
          >
            Copier le texte
          </button>
          <button
            type="button"
            class="px-4 py-2 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 text-xs font-semibold text-white shadow-lg hover:from-blue-600 hover:to-cyan-600"
            @click="showPreview = !showPreview"
          >
            {{ showPreview ? 'Masquer' : 'Voir' }} l'aperçu
          </button>
        </section>

        <form
          class="space-y-6"
          @submit.prevent="submitForm"
        >
          <!-- TVA -->
          <section
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl"
          >
            <h2 class="text-sm font-semibold mb-2">Numéro de TVA</h2>
            <p class="text-xs text-slate-400 mb-3">
              Ce numéro sera affiché sur votre site et dans vos mentions
              légales.
            </p>
            <div class="space-y-2">
              <label
                class="block text-[11px] font-semibold text-slate-200"
                for="vat_number"
              >
                N° TVA (si applicable)
              </label>
              <input
                id="vat_number"
                v-model="form.vat_number"
                type="text"
                class="w-full max-w-sm rounded-md border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 focus:border-purple-500 focus:ring-purple-500"
                placeholder="BE0123456789"
              />
              <InputError
                :message="form.errors.vat_number"
                class="mt-1 text-[11px]"
              />
              <p class="text-[11px] text-slate-400">
                Laissez vide si vous n'êtes pas assujetti à la TVA.
              </p>
            </div>
          </section>

          <!-- Aperçu -->
          <section
            v-if="showPreview"
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl"
          >
            <h2 class="text-sm font-semibold mb-2">Aperçu</h2>
            <div
              class="rounded-xl bg-slate-950/70 border border-slate-800 p-4 text-xs whitespace-pre-line text-slate-100 max-h-[320px] overflow-y-auto"
            >
              {{ form.legal_terms }}
            </div>
          </section>

          <!-- Éditeur principal -->
          <section
            class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl space-y-3"
          >
            <h2 class="text-sm font-semibold">
              Texte des mentions légales et CGV
            </h2>
            <textarea
              v-model="form.legal_terms"
              rows="22"
              class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-slate-50 font-mono focus:border-purple-500 focus:ring-purple-500"
            ></textarea>
            <InputError
              :message="form.errors.legal_terms"
              class="mt-1 text-[11px]"
            />
            <div class="flex items-center justify-between text-[11px]">
              <span
                class="inline-flex items-center rounded-full bg-slate-800 px-3 py-1 text-slate-300 border border-slate-700"
              >
                {{ form.legal_terms?.length || 0 }} / 50000 caractères
              </span>
              <span class="text-slate-500">
                Pensez à faire valider ce texte par un professionnel du droit.
              </span>
            </div>
          </section>

          <div class="flex justify-end gap-2 pt-1 text-xs">
            <button
              type="button"
              class="rounded-full border border-slate-700 px-4 py-2 text-slate-200 hover:bg-slate-800"
              @click="form.reset()"
            >
              Réinitialiser
            </button>
            <button
              type="submit"
              class="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-5 py-2 font-medium text-slate-50 hover:from-purple-600 hover:to-pink-600 disabled:opacity-60"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>
