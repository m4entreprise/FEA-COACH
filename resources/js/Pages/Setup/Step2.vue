<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import WizardLayout from '@/Components/WizardLayout.vue';

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
        preserveScroll: true,
    });
};

const skip = () => {
    form.post(route('setup.skip', { step: props.currentStep }));
};
</script>

<template>
    <Head title="Étape 2 : Images" />
    
    <WizardLayout :current-step="currentStep" :total-steps="totalSteps">
        <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-8 md:p-12">
            <!-- Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-500/20 rounded-full mb-6">
                    <span class="text-5xl">📸</span>
                </div>
                <h2 class="text-4xl font-bold text-white mb-4">
                    Donnez vie à votre site
                </h2>
                <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                    Ajoutez des images qui captiveront vos visiteurs dès la première seconde
                </p>
            </div>

            <!-- Hero Image -->
            <div class="mb-10">
                <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-400/30 rounded-2xl p-8">
                    <div class="flex items-start space-x-4 mb-6">
                        <span class="text-4xl">🎬</span>
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-2">Image Hero (arrière-plan d'accueil)</h3>
                            <p class="text-gray-300">
                                La première impression compte ! Cette image apparaîtra en grand format sur votre page d'accueil.
                            </p>
                        </div>
                    </div>

                    <div v-if="heroPreview" class="mb-6">
                        <img 
                            :src="heroPreview" 
                            alt="Hero preview" 
                            class="w-full h-64 object-cover rounded-xl border-2 border-white/20"
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
                        class="inline-flex items-center px-8 py-4 bg-blue-600/20 hover:bg-blue-600/30 border-2 border-blue-500/50 text-blue-200 font-bold rounded-xl cursor-pointer transition transform hover:scale-105"
                    >
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ heroPreview ? 'Changer l\'image hero' : 'Choisir une image hero' }}
                    </label>

                    <div class="mt-4 p-4 bg-blue-500/10 border border-blue-400/20 rounded-lg">
                        <p class="text-sm text-gray-300">
                            💡 <strong>Conseils :</strong>
                        </p>
                        <ul class="mt-2 text-sm text-gray-400 space-y-1 ml-6 list-disc">
                            <li>Image large recommandée (minimum 1920x1080)</li>
                            <li>Utilisez une image inspirante liée au fitness/sport</li>
                            <li>Taille maximale : 5MB</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Profile Photo -->
            <div class="mb-10">
                <div class="bg-gradient-to-r from-purple-500/10 to-pink-500/10 border border-purple-400/30 rounded-2xl p-8">
                    <div class="flex items-start space-x-4 mb-6">
                        <span class="text-4xl">👤</span>
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-2">Photo de profil</h3>
                            <p class="text-gray-300">
                                Montrez votre visage ! Une photo professionnelle et souriante crée la confiance.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-6">
                        <div v-if="profilePreview" class="flex-shrink-0">
                            <img 
                                :src="profilePreview" 
                                alt="Profile preview" 
                                class="w-40 h-40 object-cover rounded-full border-4 border-white/20 shadow-xl"
                            />
                        </div>
                        <div v-else class="flex-shrink-0">
                            <div class="w-40 h-40 bg-white/5 border-4 border-dashed border-white/20 rounded-full flex items-center justify-center">
                                <span class="text-6xl text-gray-500">?</span>
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
                                class="inline-flex items-center px-8 py-4 bg-purple-600/20 hover:bg-purple-600/30 border-2 border-purple-500/50 text-purple-200 font-bold rounded-xl cursor-pointer transition transform hover:scale-105"
                            >
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ profilePreview ? 'Changer la photo' : 'Ajouter une photo' }}
                            </label>

                            <div class="mt-4 p-4 bg-purple-500/10 border border-purple-400/20 rounded-lg">
                                <p class="text-sm text-gray-300">
                                    💡 <strong>Conseils :</strong>
                                </p>
                                <ul class="mt-2 text-sm text-gray-400 space-y-1 ml-6 list-disc">
                                    <li>Photo professionnelle et souriante</li>
                                    <li>Format carré recommandé (500x500px minimum)</li>
                                    <li>Fond neutre ou sportif</li>
                                    <li>Formats acceptés : JPG, PNG, WEBP (max 2MB)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mb-10 p-6 bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-400/30 rounded-2xl">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-white font-medium mb-2">
                            ✨ Ces images sont optionnelles
                        </p>
                        <p class="text-sm text-gray-300">
                            Vous pouvez passer cette étape et ajouter vos images plus tard depuis le dashboard. 
                            Cependant, des visuels de qualité augmentent considérablement votre crédibilité !
                        </p>
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
                    @click="submit('save')"
                    type="button"
                    :disabled="form.processing"
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
