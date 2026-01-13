<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import WizardLayout from '@/Components/WizardLayout.vue';
import axios from 'axios';
import { Globe2, ImageUp, Palette, Droplet, LayoutPanelLeft } from 'lucide-vue-next';
import SetupLivePreview from '@/Components/SetupLivePreview.vue';

const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    coach: Object,
    availableLayouts: Object,
    defaultLayout: String,
});

const form = useForm({
    action: 'save',
    slug: props.coach.slug || '',
    color_primary: props.coach.color_primary || '#9333ea',
    color_secondary: props.coach.color_secondary || '#ec4899',
    site_layout: props.coach.site_layout || props.defaultLayout || 'classic',
    logo: null,
});

const previewPayload = () => ({
    slug: form.slug,
    color_primary: form.color_primary,
    color_secondary: form.color_secondary,
    site_layout: form.site_layout,
});

const slugChecking = ref(false);
const slugAvailable = ref(true);
const logoPreview = ref(null);

// Vérifier disponibilité du slug
const checkSlug = async () => {
    if (!form.slug || form.slug === props.coach.slug) {
        slugAvailable.value = true;
        return;
    }
    
    slugChecking.value = true;
    try {
        const response = await axios.post(route('setup.check-slug'), { slug: form.slug });
        slugAvailable.value = response.data.available;
    } catch (error) {
        console.error(error);
    } finally {
        slugChecking.value = false;
    }
};

watch(() => form.slug, () => {
    const timeout = setTimeout(checkSlug, 500);
    return () => clearTimeout(timeout);
});

const handleLogoUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = (action) => {
    form.action = action;
    form.post(route('setup.step1.save'), {
        forceFormData: true,
        preserveScroll: true,
    });
};

const skip = () => {
    form.post(route('setup.skip', { step: props.currentStep }));
};
</script>

<template>
    <Head title="Étape 1 : Branding & Identité" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps" variant="beta">
        <div class="space-y-6">
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <Palette class="h-6 w-6 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Étape 1</p>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-50">
                            Branding & identité
                        </h2>
                        <p class="text-sm text-slate-400 mt-1">
                            Choisissez votre URL personnalisée et les couleurs qui représentent votre coaching.
                        </p>
                    </div>
                </div>
            </section>

            <SetupLivePreview
                :payload="previewPayload()"
                title="Aperçu en direct"
                subtitle="Prévisualisez votre site en temps réel avec vos couleurs et votre layout."
            />

            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl space-y-4">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <Globe2 class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Adresse web</p>
                        <h3 class="text-base font-semibold text-slate-50">Votre slug (identifiant unique)</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Choisissez l'URL unique qui identifiera votre site de coaching.
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-200 mb-2">
                        Slug *
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            v-model="form.slug"
                            required
                            pattern="[a-z0-9-]+"
                            class="w-full rounded-xl border bg-slate-950 px-4 py-3 text-sm text-slate-50 placeholder-slate-500 focus:outline-none focus:ring-2 transition"
                            :class="[
                                form.slug && slugAvailable && !form.errors.slug
                                    ? 'border-emerald-500/50 focus:ring-emerald-500'
                                    : form.errors.slug || !slugAvailable
                                        ? 'border-rose-500/50 focus:ring-rose-500'
                                        : 'border-slate-700 focus:ring-purple-500'
                            ]"
                            placeholder="mon-coaching"
                        />
                        <div v-if="slugChecking" class="absolute right-3 top-3">
                            <svg class="animate-spin h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div v-else-if="slugAvailable && form.slug" class="absolute right-3 top-3 text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div v-else-if="!slugAvailable" class="absolute right-3 top-3 text-rose-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>

                    <p v-if="form.slug" class="mt-2 text-xs text-slate-400">
                        Votre site sera accessible sur :
                        <span class="font-mono text-purple-300">{{ form.slug }}.unicoach.app</span>
                    </p>

                    <p v-if="!slugAvailable" class="mt-2 text-xs text-rose-300">
                        Ce slug est déjà utilisé. Choisissez-en un autre.
                    </p>

                    <p v-if="form.errors.slug" class="mt-2 text-xs text-rose-300">
                        {{ form.errors.slug }}
                    </p>

                    <p class="mt-2 text-[11px] text-slate-500">
                        Utilisez uniquement des lettres minuscules, chiffres et tirets (-)
                    </p>
                </div>
            </section>

            <section class="grid md:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                            <Droplet class="h-5 w-5 text-white" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Couleurs</p>
                            <h3 class="text-base font-semibold text-slate-50">Couleur principale</h3>
                            <p class="text-xs text-slate-400 mt-1">Pour les boutons et éléments importants.</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <input
                            type="color"
                            v-model="form.color_primary"
                            class="w-16 h-16 rounded-xl cursor-pointer border-2 border-slate-700 bg-slate-950"
                        />
                        <div class="flex-1">
                            <input
                                type="text"
                                v-model="form.color_primary"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-50 font-mono focus:border-purple-500 focus:ring-purple-500"
                            />
                        </div>
                    </div>
                    <div
                        class="mt-4 h-12 rounded-xl border border-slate-800"
                        :style="{ backgroundColor: form.color_primary }"
                    ></div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 flex items-center justify-center shadow-lg flex-shrink-0">
                            <Droplet class="h-5 w-5 text-white" />
                        </div>
                        <div class="flex-1">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Couleurs</p>
                            <h3 class="text-base font-semibold text-slate-50">Couleur secondaire</h3>
                            <p class="text-xs text-slate-400 mt-1">Pour les accents et variations.</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <input
                            type="color"
                            v-model="form.color_secondary"
                            class="w-16 h-16 rounded-xl cursor-pointer border-2 border-slate-700 bg-slate-950"
                        />
                        <div class="flex-1">
                            <input
                                type="text"
                                v-model="form.color_secondary"
                                class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-50 font-mono focus:border-purple-500 focus:ring-purple-500"
                            />
                        </div>
                    </div>
                    <div
                        class="mt-4 h-12 rounded-xl border border-slate-800"
                        :style="{ backgroundColor: form.color_secondary }"
                    ></div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <LayoutPanelLeft class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Layout</p>
                        <h3 class="text-base font-semibold text-slate-50">Choisissez votre design</h3>
                        <p class="text-xs text-slate-400 mt-1">Vous pourrez le changer plus tard depuis le dashboard.</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <button
                        v-for="(layout, key) in (props.availableLayouts || {})"
                        :key="key"
                        type="button"
                        @click="form.site_layout = key"
                        class="rounded-2xl border p-4 text-left transition"
                        :class="form.site_layout === key
                            ? 'border-purple-500/60 bg-purple-500/10'
                            : 'border-slate-800 bg-slate-950/40 hover:border-slate-700'"
                    >
                        <p class="text-sm font-semibold text-slate-50">{{ layout.label || key }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ layout.description || '' }}</p>
                        <p class="mt-2 text-[11px] text-slate-500">
                            <span class="font-mono">{{ key }}</span>
                        </p>
                    </button>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <ImageUp class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Branding</p>
                        <h3 class="text-base font-semibold text-slate-50">Logo (optionnel)</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Format PNG ou SVG recommandé. Taille maximale : 2MB.
                        </p>
                    </div>
                </div>

                <div class="mt-4 flex items-start gap-6">
                    <div v-if="logoPreview" class="flex-shrink-0">
                        <img :src="logoPreview" alt="Logo preview" class="w-28 h-28 object-contain rounded-xl bg-slate-950/70 border border-slate-800 p-3" />
                    </div>
                    <div class="flex-1">
                        <input
                            type="file"
                            @change="handleLogoUpload"
                            accept="image/png,image/jpeg,image/svg+xml"
                            class="hidden"
                            id="logo-upload"
                        />
                        <label
                            for="logo-upload"
                            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-5 py-3 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 cursor-pointer transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Choisir un logo
                        </label>
                    </div>
                </div>
            </section>

            <div class="flex flex-col sm:flex-row gap-3">
                <button
                    @click="skip"
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-900 px-6 py-3 text-xs font-semibold text-slate-200 hover:border-slate-500 hover:bg-slate-800 transition-colors disabled:opacity-50"
                >
                    Passer cette étape
                </button>

                <button
                    @click="submit('demo')"
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-800/80 px-6 py-3 text-xs font-semibold text-slate-100 hover:bg-slate-700 transition-colors disabled:opacity-50"
                >
                    Utiliser les couleurs par défaut
                </button>

                <button
                    @click="submit('save')"
                    type="button"
                    :disabled="form.processing || !slugAvailable || !form.slug"
                    class="flex-1 inline-flex items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-3 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="!form.processing">Continuer</span>
                    <span v-else class="flex items-center justify-center">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                    </span>
                </button>
            </div>
        </div>
    </WizardLayout>
</template>
