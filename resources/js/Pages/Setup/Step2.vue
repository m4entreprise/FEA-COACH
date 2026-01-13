<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import WizardLayout from '@/Components/WizardLayout.vue';
import { Image as ImageIcon, User as UserIcon, Upload, Info, Sparkles } from 'lucide-vue-next';
import SetupLivePreview from '@/Components/SetupLivePreview.vue';

const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    coach: Object,
});

const form = useForm({
    action: 'save',
    hero_image: null,
    profile_photo: null,
});

const previewPayload = () => ({
    site_layout: props.coach.site_layout,
    color_primary: props.coach.color_primary,
    color_secondary: props.coach.color_secondary,
});

const heroPreview = ref(null);
const profilePreview = ref(null);

const handleHeroUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.hero_image = file;
        heroPreview.value = URL.createObjectURL(file);
    }
};

const handleProfileUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.profile_photo = file;
        profilePreview.value = URL.createObjectURL(file);
    }
};

const submit = (action) => {
    form.action = action;
    form.post(route('setup.step2.save'), {
        forceFormData: true,
        preserveScroll: true,
    });
};

const skip = () => {
    form.post(route('setup.skip', { step: props.currentStep }));
};
</script>

<template>
    <Head title="Étape 2 : Images" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps" variant="beta">
        <div class="space-y-6">
            <!-- Header -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <ImageIcon class="h-6 w-6 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Étape 2</p>
                        <h2 class="text-xl md:text-2xl font-bold text-slate-50">Images</h2>
                        <p class="text-sm text-slate-400 mt-1">
                            Ajoutez des images qui captiveront vos visiteurs dès la première seconde.
                        </p>
                    </div>
                </div>
            </section>

            <SetupLivePreview
                :payload="previewPayload()"
                title="Aperçu en direct"
                subtitle="Prévisualisez votre site en temps réel (les images apparaîtront après enregistrement)."
            />

            <!-- Hero Image -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-6">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <ImageIcon class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Image</p>
                        <h3 class="text-base font-semibold text-slate-50">Image Hero (arrière-plan d'accueil)</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            La première impression compte ! Cette image apparaîtra en grand format sur votre page d'accueil.
                        </p>
                    </div>
                </div>

                    <div v-if="heroPreview" class="mb-6">
                        <img 
                            :src="heroPreview" 
                            alt="Hero preview" 
                            class="w-full h-64 object-cover rounded-xl border border-slate-800"
                        />
                    </div>

                    <input
                        type="file"
                        @change="handleHeroUpload"
                        accept="image/*"
                        class="hidden"
                        id="hero-upload"
                    />
                    <label
                        for="hero-upload"
                        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-sky-500 to-indigo-500 px-5 py-3 text-xs font-semibold text-white shadow-lg hover:from-sky-600 hover:to-indigo-600 cursor-pointer transition"
                    >
                        <Upload class="h-4 w-4" />
                        {{ heroPreview ? 'Changer l\'image hero' : 'Choisir une image hero' }}
                    </label>

                    <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                        <p class="text-xs font-semibold text-slate-200 flex items-center gap-2">
                            <Info class="h-4 w-4 text-sky-300" />
                            Conseils
                        </p>
                        <ul class="mt-2 text-xs text-slate-400 space-y-1 ml-5 list-disc">
                            <li>Image large recommandée (minimum 1920x1080)</li>
                            <li>Utilisez une image inspirante liée au fitness/sport</li>
                            <li>Taille maximale : 5MB</li>
                        </ul>
                    </div>
            </section>

            <!-- Profile Photo -->
            <section class="rounded-2xl border border-slate-800 bg-slate-900/80 p-6 shadow-xl">
                <div class="flex items-start gap-4 mb-6">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                        <UserIcon class="h-5 w-5 text-white" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Image</p>
                        <h3 class="text-base font-semibold text-slate-50">Photo de profil</h3>
                        <p class="text-xs text-slate-400 mt-1">
                            Montrez votre visage ! Une photo professionnelle et souriante crée la confiance.
                        </p>
                    </div>
                </div>

                    <div class="flex items-start space-x-6">
                        <div v-if="profilePreview" class="flex-shrink-0">
                            <img 
                                :src="profilePreview" 
                                alt="Profile preview" 
                                class="w-40 h-40 object-cover rounded-full border-2 border-slate-800 shadow-xl"
                            />
                        </div>
                        <div v-else class="flex-shrink-0">
                            <div class="w-40 h-40 bg-slate-950/60 border-2 border-dashed border-slate-700 rounded-full flex items-center justify-center">
                                <UserIcon class="h-12 w-12 text-slate-600" />
                            </div>
                        </div>

                        <div class="flex-1">
                            <input
                                type="file"
                                @change="handleProfileUpload"
                                accept="image/*"
                                class="hidden"
                                id="profile-upload"
                            />
                            <label
                                for="profile-upload"
                                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-5 py-3 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 cursor-pointer transition"
                            >
                                <Upload class="h-4 w-4" />
                                {{ profilePreview ? 'Changer la photo' : 'Ajouter une photo' }}
                            </label>

                            <div class="mt-4 rounded-xl border border-slate-800 bg-slate-950/70 p-4">
                                <p class="text-xs font-semibold text-slate-200 flex items-center gap-2">
                                    <Info class="h-4 w-4 text-purple-300" />
                                    Conseils
                                </p>
                                <ul class="mt-2 text-xs text-slate-400 space-y-1 ml-5 list-disc">
                                    <li>Photo professionnelle et souriante</li>
                                    <li>Format carré recommandé (500x500px minimum)</li>
                                    <li>Fond neutre ou sportif</li>
                                    <li>Formats acceptés : JPG, PNG, WEBP (max 2MB)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
            </section>

            <!-- Info Box -->
            <section class="rounded-2xl border border-emerald-500/30 bg-emerald-950/20 p-6 shadow-xl">
                <div class="flex items-start gap-3">
                    <Sparkles class="h-5 w-5 text-emerald-300 flex-shrink-0 mt-0.5" />
                    <div>
                        <p class="text-sm font-semibold text-emerald-100 mb-1">
                            Ces images sont optionnelles
                        </p>
                        <p class="text-xs text-emerald-100/80">
                            Vous pouvez passer cette étape et ajouter vos images plus tard depuis le dashboard.
                            Cependant, des visuels de qualité augmentent considérablement votre crédibilité.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button
                    @click="skip"
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-900 px-6 py-3 text-xs font-semibold text-slate-200 hover:border-slate-500 hover:bg-slate-800 transition-colors disabled:opacity-50"
                >
                    Passer cette étape →
                </button>
                
                <button
                    @click="submit('save')"
                    type="button"
                    :disabled="form.processing"
                    class="flex-1 inline-flex items-center justify-center rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-3 text-xs font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="!form.processing">Continuer →</span>
                    <span v-else class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
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
