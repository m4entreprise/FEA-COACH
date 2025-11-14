<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import WizardLayout from '@/Components/WizardLayout.vue';
import axios from 'axios';

const props = defineProps({
    currentStep: Number,
    totalSteps: Number,
    coach: Object,
});

const form = useForm({
    action: 'save',
    slug: props.coach.slug || '',
    primary_color: props.coach.primary_color || '#9333ea',
    secondary_color: props.coach.secondary_color || '#ec4899',
    logo: null,
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
        preserveScroll: true,
    });
};

const skip = () => {
    form.post(route('setup.skip', { step: props.currentStep }));
};
</script>

<template>
    <Head title="Étape 1 : Branding & Identité" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps">
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-8 md:p-12">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-500/20 rounded-full mb-6">
                    <span class="text-5xl">🎨</span>
                </div>
                <h2 class="text-4xl font-bold text-white mb-4">
                    Identité de votre marque
                </h2>
                <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                    Choisissez votre URL personnalisée et les couleurs qui représentent votre coaching
                </p>
            </div>

            <!-- Slug Selection -->
            <div class="mb-10">
                <div class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border border-purple-400/30 rounded-2xl p-6">
                    <div class="flex items-start space-x-3 mb-4">
                        <span class="text-3xl">🌐</span>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-white mb-2">Votre adresse web personnalisée</h3>
                            <p class="text-sm text-gray-300 mb-4">
                                Choisissez l'URL unique qui identifiera votre site de coaching
                            </p>
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-200 mb-2">
                                        Votre slug (identifiant unique) *
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <div class="relative flex-1">
                                            <input
                                                type="text"
                                                v-model="form.slug"
                                                required
                                                pattern="[a-z0-9-]+"
                                                class="w-full px-4 py-3 bg-white/5 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 transition"
                                                :class="[
                                                    slugAvailable && !form.errors.slug 
                                                        ? 'border-green-500/50 focus:ring-green-500' 
                                                        : form.errors.slug || !slugAvailable
                                                            ? 'border-red-500/50 focus:ring-red-500'
                                                            : 'border-white/10 focus:ring-purple-500'
                                                ]"
                                                placeholder="mon-coaching"
                                            />
                                            <div v-if="slugChecking" class="absolute right-3 top-3">
                                                <svg class="animate-spin h-6 w-6 text-purple-400" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                            <div v-else-if="slugAvailable && form.slug" class="absolute right-3 top-3 text-green-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <div v-else-if="!slugAvailable" class="absolute right-3 top-3 text-red-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p v-if="form.slug" class="mt-2 text-sm text-gray-300">
                                        Votre site sera accessible sur : 
                                        <span class="font-mono text-purple-300">{{ form.slug }}.unicoach.app</span>
                                    </p>
                                    
                                    <p v-if="!slugAvailable" class="mt-2 text-sm text-red-400">
                                        ❌ Ce slug est déjà utilisé. Choisissez-en un autre.
                                    </p>
                                    
                                    <p v-if="form.errors.slug" class="mt-2 text-sm text-red-400">
                                        {{ form.errors.slug }}
                                    </p>
                                    
                                    <p class="mt-2 text-xs text-gray-400">
                                        💡 Utilisez uniquement des lettres minuscules, chiffres et tirets (-)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colors -->
            <div class="grid md:grid-cols-2 gap-6 mb-10">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <label class="block text-sm font-medium text-gray-200 mb-3">
                        🎨 Couleur principale
                    </label>
                    <div class="flex items-center space-x-4">
                        <input
                            type="color"
                            v-model="form.primary_color"
                            class="w-16 h-16 rounded-lg cursor-pointer border-2 border-white/20"
                        />
                        <div class="flex-1">
                            <input
                                type="text"
                                v-model="form.primary_color"
                                class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                            />
                            <p class="mt-2 text-xs text-gray-400">Pour les boutons et éléments importants</p>
                        </div>
                    </div>
                    <div 
                        class="mt-4 h-12 rounded-lg transition-colors"
                        :style="{ backgroundColor: form.primary_color }"
                    ></div>
                </div>

                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <label class="block text-sm font-medium text-gray-200 mb-3">
                        🌈 Couleur secondaire
                    </label>
                    <div class="flex items-center space-x-4">
                        <input
                            type="color"
                            v-model="form.secondary_color"
                            class="w-16 h-16 rounded-lg cursor-pointer border-2 border-white/20"
                        />
                        <div class="flex-1">
                            <input
                                type="text"
                                v-model="form.secondary_color"
                                class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                            />
                            <p class="mt-2 text-xs text-gray-400">Pour les accents et variations</p>
                        </div>
                    </div>
                    <div 
                        class="mt-4 h-12 rounded-lg transition-colors"
                        :style="{ backgroundColor: form.secondary_color }"
                    ></div>
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="mb-10">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
                    <label class="block text-sm font-medium text-gray-200 mb-3">
                        📷 Logo de votre marque (optionnel)
                    </label>
                    <div class="flex items-start space-x-6">
                        <div v-if="logoPreview" class="flex-shrink-0">
                            <img :src="logoPreview" alt="Logo preview" class="w-32 h-32 object-contain rounded-lg bg-white/5 p-2" />
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
                                class="inline-flex items-center px-6 py-3 bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/50 text-purple-300 font-medium rounded-lg cursor-pointer transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                Choisir un logo
                            </label>
                            <p class="mt-3 text-sm text-gray-400">
                                Format PNG ou SVG recommandé. Taille maximale : 2MB
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button
                    @click="skip"
                    type="button"
                    :disabled="form.processing"
                    class="px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 font-semibold rounded-xl transition disabled:opacity-50"
                >
                    Passer cette étape →
                </button>
                
                <button
                    @click="submit('demo')"
                    type="button"
                    :disabled="form.processing"
                    class="px-8 py-4 bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/50 text-blue-300 font-semibold rounded-xl transition disabled:opacity-50"
                >
                    ✨ Utiliser les couleurs par défaut
                </button>
                
                <button
                    @click="submit('save')"
                    type="button"
                    :disabled="form.processing || !slugAvailable || !form.slug"
                    class="flex-1 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-xl shadow-lg transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
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
