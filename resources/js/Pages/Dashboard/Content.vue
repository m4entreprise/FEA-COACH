<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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

// Character counters
const heroTitleCount = computed(() => form.hero_title.length);
const heroSubtitleCount = computed(() => form.hero_subtitle.length);
const aboutTextCount = computed(() => form.about_text.length);
const methodTextCount = computed(() => form.method_text.length);
const ctaTextCount = computed(() => form.cta_text.length);

// Completion percentage
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

const submit = () => {
    form.post(route('dashboard.content.update'), {
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
        faqForm.patch(route('dashboard.faq.update', editingFaq.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeFaqModal();
                router.reload({ only: ['faqs', 'faqsCount', 'faqsActiveCount'] });
            },
        });
    } else {
        faqForm.post(route('dashboard.faq.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeFaqModal();
                router.reload({ only: ['faqs', 'faqsCount', 'faqsActiveCount'] });
            },
        });
    }
};

const deleteFaq = (faq) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer cette question ?\n"${faq.question}"`)) {
        router.delete(route('dashboard.faq.destroy', faq.id), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload({ only: ['faqs', 'faqsCount', 'faqsActiveCount'] });
            },
        });
    }
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

    router.post(route('dashboard.content.profile-photo.upload'), {
        profile_photo: photoInput.value.files[0],
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Reload the page to get the updated photo URL
            router.reload();
        },
    });
};

const deletePhoto = () => {
    if (confirm('Êtes-vous sûr de vouloir supprimer votre photo de profil ?')) {
        router.delete(route('dashboard.content.profile-photo.delete'), {
            preserveScroll: true,
            onSuccess: () => {
                photoPreview.value = null;
                router.reload({ only: ['profilePhotoUrl'] });
            },
        });
    }
};
</script>

<template>
    <Head title="Contenu du site" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Gestion du Contenu
            </h2>
        </template>

        <div class="py-12 bg-gradient-to-br from-slate-50 via-purple-50 to-slate-50 dark:from-slate-900 dark:via-purple-900/20 dark:to-slate-900 min-h-screen">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-xl p-6 text-white transform hover:scale-[1.01] transition-all duration-300 backdrop-blur-xl border border-green-400/20">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="font-semibold">✨ {{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Completion Card -->
                <div class="overflow-hidden rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 shadow-xl hover:shadow-2xl transform hover:scale-[1.01] transition-all duration-300">
                    <div class="p-8 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold">📊 Complétion du contenu</h3>
                                <p class="mt-2 text-sm text-blue-100">
                                    {{ completionPercentage }}% des sections sont remplies
                                </p>
                            </div>
                            <div class="text-5xl font-bold">
                                {{ completionPercentage }}%
                            </div>
                        </div>
                        <div class="mt-6 h-3 overflow-hidden rounded-full bg-blue-400/50 backdrop-blur-sm">
                            <div
                                class="h-full bg-white shadow-lg transition-all duration-500"
                                :style="{ width: completionPercentage + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="rounded-2xl bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 shadow-xl border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl transform hover:scale-[1.01] transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 shadow-lg">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">💡 Personnalisez votre contenu</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Personnalisez les textes de votre site public pour attirer vos clients. Soyez authentique, clair et mettez en avant votre différence !
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Form -->
                <div class="overflow-hidden bg-gradient-to-br from-white to-slate-50 dark:from-gray-800 dark:to-slate-900 shadow-xl sm:rounded-2xl border border-slate-200/50 dark:border-slate-500/30 backdrop-blur-xl">
                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Hero Section -->
                            <div class="rounded-2xl border border-indigo-200/50 bg-gradient-to-br from-white to-indigo-50 p-8 dark:border-indigo-500/30 dark:from-gray-800 dark:to-indigo-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                        🎯 Section Hero (Première impression)
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    La première section que vos visiteurs verront. Faites-la percutante !
                                </p>
                                <div class="space-y-6">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <InputLabel for="hero_title" value="Titre principal *" />
                                            <span class="text-xs text-gray-500">
                                                {{ heroTitleCount }}/255
                                            </span>
                                        </div>
                                        <input
                                            id="hero_title"
                                            type="text"
                                            v-model="form.hero_title"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Transformez votre corps, libérez votre potentiel"
                                            maxlength="255"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.hero_title" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            💡 Astuce : Utilisez un message inspirant et orienté résultat
                                        </p>
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between">
                                            <InputLabel for="hero_subtitle" value="Sous-titre" />
                                            <span class="text-xs text-gray-500">
                                                {{ heroSubtitleCount }}/500
                                            </span>
                                        </div>
                                        <textarea
                                            id="hero_subtitle"
                                            v-model="form.hero_subtitle"
                                            rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Coaching sportif personnalisé pour atteindre vos objectifs de santé et de bien-être"
                                            maxlength="500"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.hero_subtitle" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            💡 Astuce : Précisez votre proposition de valeur unique
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Photo Section -->
                            <div class="rounded-2xl border border-pink-200/50 bg-gradient-to-br from-white to-pink-50 p-8 dark:border-pink-500/30 dark:from-gray-800 dark:to-pink-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">
                                        📸 Photo de profil
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Ajoutez votre photo pour personnaliser votre profil
                                </p>

                                <div class="flex flex-col items-center gap-6 sm:flex-row">
                                    <!-- Photo Preview -->
                                    <div class="flex-shrink-0">
                                        <div v-if="photoPreview" class="relative">
                                            <img
                                                :src="photoPreview"
                                                alt="Photo de profil"
                                                class="h-32 w-32 rounded-full object-cover shadow-lg ring-4 ring-gray-200 dark:ring-gray-700"
                                            />
                                            <button
                                                @click="deletePhoto"
                                                type="button"
                                                class="absolute -right-2 -top-2 rounded-full bg-red-600 p-2 text-white shadow-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div v-else class="flex h-32 w-32 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Upload Controls -->
                                    <div class="flex-1">
                                        <input
                                            ref="photoInput"
                                            type="file"
                                            accept="image/*"
                                            class="hidden"
                                            @change="updatePhotoPreview"
                                        />
                                        
                                        <div class="space-y-3">
                                            <div class="flex gap-3">
                                                <button
                                                    @click="selectPhoto"
                                                    type="button"
                                                    class="inline-flex items-center rounded-xl bg-gradient-to-r from-pink-500 to-rose-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-pink-600 hover:to-rose-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                                                >
                                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                    Choisir une photo
                                                </button>
                                                
                                                <button
                                                    v-if="photoInput?.files?.length"
                                                    @click="uploadPhoto"
                                                    type="button"
                                                    class="inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                                                >
                                                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Enregistrer la photo
                                                </button>
                                            </div>
                                            
                                            <div class="rounded-xl bg-pink-50 p-4 dark:bg-pink-900/20 border border-pink-200/30 dark:border-pink-500/20">
                                                <p class="text-xs text-pink-700 dark:text-pink-300">
                                                    <strong>💡 Conseils :</strong>
                                                </p>
                                                <ul class="mt-1 list-inside list-disc space-y-1 text-xs text-pink-700 dark:text-pink-300">
                                                    <li>Utilisez une photo professionnelle et souriante</li>
                                                    <li>Format carré recommandé (500x500px minimum)</li>
                                                    <li>Formats acceptés : JPG, PNG, WEBP (max 2MB)</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics Section -->
                            <div class="rounded-2xl border border-blue-200/50 bg-gradient-to-br from-white to-blue-50 p-8 dark:border-blue-500/30 dark:from-gray-800 dark:to-blue-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                                        📊 Statistiques
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Personnalisez vos statistiques affichées sur le site public
                                </p>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <!-- Satisfaction Rate -->
                                    <div>
                                        <InputLabel for="satisfaction_rate" value="Taux de satisfaction (%) *" />
                                        <div class="mt-1 flex items-center gap-3">
                                            <input
                                                id="satisfaction_rate"
                                                type="number"
                                                v-model.number="form.satisfaction_rate"
                                                min="0"
                                                max="100"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                required
                                            />
                                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                                {{ form.satisfaction_rate }}%
                                            </span>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.satisfaction_rate" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            Exemple : 100% pour "100% Satisfaits"
                                        </p>
                                    </div>

                                    <!-- Average Rating -->
                                    <div>
                                        <InputLabel for="average_rating" value="Note moyenne (étoiles) *" />
                                        <div class="mt-1 flex items-center gap-3">
                                            <input
                                                id="average_rating"
                                                type="number"
                                                v-model.number="form.average_rating"
                                                min="0"
                                                max="5"
                                                step="0.1"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                required
                                            />
                                            <span class="text-2xl font-bold text-yellow-500">
                                                {{ form.average_rating }}★
                                            </span>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.average_rating" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            De 0 à 5 étoiles (décimales acceptées)
                                        </p>
                                    </div>
                                </div>

                                <!-- Info Box -->
                                <div class="mt-6 rounded-xl bg-blue-50 p-4 dark:bg-blue-900/20 border border-blue-200/30 dark:border-blue-500/20">
                                    <p class="text-xs text-blue-700 dark:text-blue-300">
                                        <strong>💡 Conseil :</strong>
                                    </p>
                                    <ul class="mt-1 list-inside list-disc space-y-1 text-xs text-blue-700 dark:text-blue-300">
                                        <li>Ces statistiques apparaissent dans la section "À propos" de votre site</li>
                                        <li>Mettez à jour régulièrement selon vos vrais résultats</li>
                                        <li>Soyez transparent et honnête avec vos clients</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- About Section -->
                            <div class="rounded-2xl border border-green-200/50 bg-gradient-to-br from-white to-green-50 p-8 dark:border-green-500/30 dark:from-gray-800 dark:to-green-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                        👤 Section "À propos"
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Présentez-vous et créez une connexion avec vos futurs clients
                                </p>
                                <div>
                                    <div class="flex items-center justify-between">
                                        <InputLabel for="about_text" value="Texte de présentation" />
                                        <span class="text-xs text-gray-500">
                                            {{ aboutTextCount }}/5000
                                        </span>
                                    </div>
                                    <textarea
                                        id="about_text"
                                        v-model="form.about_text"
                                        rows="8"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                        placeholder="Parlez de votre parcours, vos qualifications, votre passion pour le coaching, ce qui vous motive..."
                                        maxlength="5000"
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.about_text" />
                                    <div class="mt-2 rounded-xl bg-green-50 p-4 dark:bg-green-900/20 border border-green-200/30 dark:border-green-500/20">
                                        <p class="text-xs text-green-700 dark:text-green-300">
                                            <strong>💡 Conseils :</strong>
                                        </p>
                                        <ul class="mt-1 list-inside list-disc space-y-1 text-xs text-green-700 dark:text-green-300">
                                            <li>Mentionnez vos certifications et formations</li>
                                            <li>Partagez votre histoire personnelle</li>
                                            <li>Expliquez pourquoi vous êtes devenu coach</li>
                                            <li>Créez de l'empathie avec vos futurs clients</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Method Section -->
                            <div class="rounded-2xl border border-purple-200/50 bg-gradient-to-br from-white to-purple-50 p-8 dark:border-purple-500/30 dark:from-gray-800 dark:to-purple-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                        ⚡ Section "Ma méthode"
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Personnalisez tous les éléments de la section "Ma méthode" de votre site
                                </p>
                                
                                <div class="space-y-6">
                                    <!-- Titre de la section -->
                                    <div>
                                        <InputLabel for="method_title" value="Titre de la section" />
                                        <input
                                            id="method_title"
                                            type="text"
                                            v-model="form.method_title"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Ma méthode de coaching"
                                            maxlength="255"
                                        />
                                        <InputError class="mt-2" :message="form.errors.method_title" />
                                    </div>

                                    <!-- Sous-titre -->
                                    <div>
                                        <InputLabel for="method_subtitle" value="Sous-titre" />
                                        <input
                                            id="method_subtitle"
                                            type="text"
                                            v-model="form.method_subtitle"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Une approche personnalisée et scientifique pour des résultats durables"
                                            maxlength="255"
                                        />
                                        <InputError class="mt-2" :message="form.errors.method_subtitle" />
                                    </div>

                                    <!-- Description générale -->
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <InputLabel for="method_text" value="Description de votre méthode" />
                                            <span class="text-xs text-gray-500">
                                                {{ methodTextCount }}/5000
                                            </span>
                                        </div>
                                        <textarea
                                            id="method_text"
                                            v-model="form.method_text"
                                            rows="6"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Décrivez votre méthode d'entraînement, votre philosophie, vos principes clés..."
                                            maxlength="5000"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.method_text" />
                                    </div>

                                    <div class="border-t border-gray-300 dark:border-gray-600 pt-6">
                                        <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                            Les 3 étapes de votre méthode
                                        </h4>

                                        <!-- Étape 1 -->
                                        <div class="mb-6 rounded-lg border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                                            <h5 class="text-sm font-semibold text-purple-900 dark:text-purple-100 mb-3">
                                                1️⃣ Première étape
                                            </h5>
                                            <div class="space-y-3">
                                                <div>
                                                    <InputLabel for="method_step1_title" value="Titre de l'étape 1" />
                                                    <input
                                                        id="method_step1_title"
                                                        type="text"
                                                        v-model="form.method_step1_title"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                        placeholder="Ex: Évaluation"
                                                        maxlength="255"
                                                    />
                                                    <InputError class="mt-2" :message="form.errors.method_step1_title" />
                                                </div>
                                                <div>
                                                    <InputLabel for="method_step1_description" value="Description de l'étape 1" />
                                                    <textarea
                                                        id="method_step1_description"
                                                        v-model="form.method_step1_description"
                                                        rows="3"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                        placeholder="Ex: Bilan complet de votre condition physique et définition de vos objectifs personnalisés."
                                                        maxlength="1000"
                                                    ></textarea>
                                                    <InputError class="mt-2" :message="form.errors.method_step1_description" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Étape 2 -->
                                        <div class="mb-6 rounded-lg border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                                            <h5 class="text-sm font-semibold text-purple-900 dark:text-purple-100 mb-3">
                                                2️⃣ Deuxième étape
                                            </h5>
                                            <div class="space-y-3">
                                                <div>
                                                    <InputLabel for="method_step2_title" value="Titre de l'étape 2" />
                                                    <input
                                                        id="method_step2_title"
                                                        type="text"
                                                        v-model="form.method_step2_title"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                        placeholder="Ex: Programme"
                                                        maxlength="255"
                                                    />
                                                    <InputError class="mt-2" :message="form.errors.method_step2_title" />
                                                </div>
                                                <div>
                                                    <InputLabel for="method_step2_description" value="Description de l'étape 2" />
                                                    <textarea
                                                        id="method_step2_description"
                                                        v-model="form.method_step2_description"
                                                        rows="3"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                        placeholder="Ex: Plan d'entraînement sur mesure adapté à votre niveau et vos disponibilités."
                                                        maxlength="1000"
                                                    ></textarea>
                                                    <InputError class="mt-2" :message="form.errors.method_step2_description" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Étape 3 -->
                                        <div class="mb-4 rounded-lg border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                                            <h5 class="text-sm font-semibold text-purple-900 dark:text-purple-100 mb-3">
                                                3️⃣ Troisième étape
                                            </h5>
                                            <div class="space-y-3">
                                                <div>
                                                    <InputLabel for="method_step3_title" value="Titre de l'étape 3" />
                                                    <input
                                                        id="method_step3_title"
                                                        type="text"
                                                        v-model="form.method_step3_title"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                        placeholder="Ex: Suivi"
                                                        maxlength="255"
                                                    />
                                                    <InputError class="mt-2" :message="form.errors.method_step3_title" />
                                                </div>
                                                <div>
                                                    <InputLabel for="method_step3_description" value="Description de l'étape 3" />
                                                    <textarea
                                                        id="method_step3_description"
                                                        v-model="form.method_step3_description"
                                                        rows="3"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                        placeholder="Ex: Accompagnement régulier et ajustements constants pour garantir vos progrès."
                                                        maxlength="1000"
                                                    ></textarea>
                                                    <InputError class="mt-2" :message="form.errors.method_step3_description" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-xl bg-purple-50 p-4 dark:bg-purple-900/20 border border-purple-200/30 dark:border-purple-500/20">
                                        <p class="text-xs text-purple-700 dark:text-purple-300">
                                            <strong>💡 Conseil :</strong> Tous ces champs sont personnalisables. Laissez-les vides pour utiliser les valeurs par défaut.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA Section -->
                            <div class="rounded-2xl border border-orange-200/50 bg-gradient-to-br from-white to-orange-50 p-8 dark:border-orange-500/30 dark:from-gray-800 dark:to-orange-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                                        🚀 Appel à l'action (CTA)
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Le texte du bouton qui incitera vos visiteurs à passer à l'action
                                </p>
                                <div>
                                    <div class="flex items-center justify-between">
                                        <InputLabel for="cta_text" value="Texte du bouton principal *" />
                                        <span class="text-xs text-gray-500">
                                            {{ ctaTextCount }}/100
                                        </span>
                                    </div>
                                    <input
                                        id="cta_text"
                                        type="text"
                                        v-model="form.cta_text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                        placeholder="Ex: Réserver une séance gratuite"
                                        maxlength="100"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.cta_text" />
                                    <div class="mt-2 rounded-xl bg-orange-50 p-4 dark:bg-orange-900/20 border border-orange-200/30 dark:border-orange-500/20">
                                        <p class="text-xs text-orange-700 dark:text-orange-300">
                                            <strong>💡 Exemples efficaces :</strong>
                                        </p>
                                        <ul class="mt-1 list-inside list-disc space-y-1 text-xs text-orange-700 dark:text-orange-300">
                                            <li>"Réserver ma séance découverte"</li>
                                            <li>"Commencer ma transformation"</li>
                                            <li>"Demander mon bilan gratuit"</li>
                                            <li>"Me lancer maintenant"</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Section -->
                            <div class="rounded-2xl border border-emerald-200/50 bg-gradient-to-br from-white to-emerald-50 p-8 dark:border-emerald-500/30 dark:from-gray-800 dark:to-emerald-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">
                                        💰 Section "Tarifs"
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Personnalisez le titre et sous-titre de votre section tarifs (les plans se gèrent dans le menu "Plans")
                                </p>
                                
                                <div class="space-y-6">
                                    <!-- Titre de la section tarifs -->
                                    <div>
                                        <InputLabel for="pricing_title" value="Titre de la section tarifs" />
                                        <input
                                            id="pricing_title"
                                            type="text"
                                            v-model="form.pricing_title"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Mes formules de coaching"
                                            maxlength="255"
                                        />
                                        <InputError class="mt-2" :message="form.errors.pricing_title" />
                                    </div>

                                    <!-- Sous-titre -->
                                    <div>
                                        <InputLabel for="pricing_subtitle" value="Sous-titre" />
                                        <input
                                            id="pricing_subtitle"
                                            type="text"
                                            v-model="form.pricing_subtitle"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Choisissez la formule qui correspond le mieux à vos objectifs"
                                            maxlength="255"
                                        />
                                        <InputError class="mt-2" :message="form.errors.pricing_subtitle" />
                                    </div>

                                    <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-900/20 border border-emerald-200/30 dark:border-emerald-500/20">
                                        <p class="text-xs text-emerald-700 dark:text-emerald-300">
                                            <strong>💡 Conseil :</strong> Pour gérer vos plans (prix, descriptions, CTA), rendez-vous dans le menu "Plans". Ici vous ne modifiez que le titre et le sous-titre de cette section.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Transformations Section -->
                            <div class="rounded-2xl border border-teal-200/50 bg-gradient-to-br from-white to-teal-50 p-8 dark:border-teal-500/30 dark:from-gray-800 dark:to-teal-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-teal-600 to-cyan-600 bg-clip-text text-transparent">
                                        📈 Section "Transformations"
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Personnalisez le titre et sous-titre de votre section transformations (les transformations se gèrent dans le menu "Transformations")
                                </p>
                                
                                <div class="space-y-6">
                                    <!-- Titre de la section transformations -->
                                    <div>
                                        <InputLabel for="transformations_title" value="Titre de la section transformations" />
                                        <input
                                            id="transformations_title"
                                            type="text"
                                            v-model="form.transformations_title"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Leurs transformations"
                                            maxlength="255"
                                        />
                                        <InputError class="mt-2" :message="form.errors.transformations_title" />
                                    </div>

                                    <!-- Sous-titre -->
                                    <div>
                                        <InputLabel for="transformations_subtitle" value="Sous-titre" />
                                        <input
                                            id="transformations_subtitle"
                                            type="text"
                                            v-model="form.transformations_subtitle"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Des résultats réels de personnes comme vous"
                                            maxlength="255"
                                        />
                                        <InputError class="mt-2" :message="form.errors.transformations_subtitle" />
                                    </div>

                                    <div class="rounded-xl bg-teal-50 p-4 dark:bg-teal-900/20 border border-teal-200/30 dark:border-teal-500/20">
                                        <p class="text-xs text-teal-700 dark:text-teal-300">
                                            <strong>💡 Conseil :</strong> Pour gérer vos transformations (photos avant/après, descriptions), rendez-vous dans le menu "Transformations". Ici vous ne modifiez que le titre et le sous-titre de cette section.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Intermediate CTA Section -->
                            <div class="rounded-2xl border border-orange-200/50 bg-gradient-to-r from-orange-50 to-red-50 p-8 dark:border-orange-500/30 dark:from-orange-900/20 dark:to-red-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                                        ⚡ Section CTA intermédiaire (après Méthode)
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Section d'appel à l'action qui apparaît entre "Ma méthode" et "Tarifs" pour encourager le visiteur à passer à l'action.
                                </p>
                                
                                <div class="space-y-6">
                                    <!-- Titre CTA intermédiaire -->
                                    <div>
                                        <InputLabel for="intermediate_cta_title" value="Titre de la section CTA" />
                                        <input
                                            id="intermediate_cta_title"
                                            type="text"
                                            v-model="form.intermediate_cta_title"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Prêt à transformer votre corps et votre vie ?"
                                            maxlength="255"
                                        />
                                        <InputError class="mt-2" :message="form.errors.intermediate_cta_title" />
                                    </div>

                                    <!-- Sous-titre CTA intermédiaire -->
                                    <div>
                                        <InputLabel for="intermediate_cta_subtitle" value="Sous-titre" />
                                        <textarea
                                            id="intermediate_cta_subtitle"
                                            v-model="form.intermediate_cta_subtitle"
                                            rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Ne restez pas seul face à vos objectifs. Bénéficiez d'un accompagnement personnalisé qui vous mènera au succès."
                                            maxlength="500"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.intermediate_cta_subtitle" />
                                    </div>

                                    <div class="rounded-xl bg-orange-50 p-4 dark:bg-orange-900/20 border border-orange-200/30 dark:border-orange-500/20">
                                        <p class="text-xs text-orange-700 dark:text-orange-300">
                                            <strong>💡 Astuce :</strong> Cette section apparaît entre "Ma méthode" et "Tarifs". Le bouton utilise automatiquement votre texte CTA défini ci-dessus et redirige vers la section Tarifs.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Final CTA Section -->
                            <div class="rounded-2xl border border-purple-200/50 bg-gradient-to-br from-white to-purple-50 p-8 dark:border-purple-500/30 dark:from-gray-800 dark:to-purple-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                                        🎯 Section "Appel à l'action finale"
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Personnalisez le titre et sous-titre de la section d'appel à l'action en fin de page (actuellement : "Prêt à commencer votre transformation ?")
                                </p>
                                
                                <div class="space-y-6">
                                    <!-- Titre CTA finale -->
                                    <div>
                                        <InputLabel for="final_cta_title" value="Titre de l'appel à l'action final" />
                                        <input
                                            id="final_cta_title"
                                            type="text"
                                            v-model="form.final_cta_title"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Prêt à commencer votre transformation ?"
                                            maxlength="255"
                                        />
                                        <InputError class="mt-2" :message="form.errors.final_cta_title" />
                                    </div>

                                    <!-- Sous-titre CTA finale -->
                                    <div>
                                        <InputLabel for="final_cta_subtitle" value="Sous-titre" />
                                        <textarea
                                            id="final_cta_subtitle"
                                            v-model="form.final_cta_subtitle"
                                            rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                            placeholder="Ex: Ne laissez pas vos objectifs être de simples rêves. Agissez maintenant !"
                                            maxlength="500"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.final_cta_subtitle" />
                                    </div>

                                    <div class="rounded-xl bg-purple-50 p-4 dark:bg-purple-900/20 border border-purple-200/30 dark:border-purple-500/20">
                                        <p class="text-xs text-purple-700 dark:text-purple-300">
                                            <strong>💡 Astuce :</strong> Cette section apparaît juste avant le footer. Le texte du bouton se modifie dans la section "Appel à l'action (CTA)" ci-dessus.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media Section -->
                            <div class="rounded-2xl border border-blue-200/50 bg-gradient-to-r from-blue-50 to-cyan-50 p-8 dark:border-blue-500/30 dark:from-blue-900/20 dark:to-cyan-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                        </svg>
                                    </div>
                                    <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">
                                        📱 Réseaux sociaux
                                    </h3>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Ajoutez vos liens de réseaux sociaux pour les afficher dans le footer de votre site public
                                </p>
                                
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <!-- Facebook -->
                                    <div>
                                        <InputLabel for="facebook_url" value="Facebook" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                                                </svg>
                                            </div>
                                            <input
                                                id="facebook_url"
                                                type="url"
                                                v-model="form.facebook_url"
                                                class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                placeholder="https://facebook.com/votre-page"
                                            />
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.facebook_url" />
                                    </div>

                                    <!-- Instagram -->
                                    <div>
                                        <InputLabel for="instagram_url" value="Instagram" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="h-5 w-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/>
                                                </svg>
                                            </div>
                                            <input
                                                id="instagram_url"
                                                type="url"
                                                v-model="form.instagram_url"
                                                class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                placeholder="https://instagram.com/votre-profil"
                                            />
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.instagram_url" />
                                    </div>

                                    <!-- Twitter/X -->
                                    <div>
                                        <InputLabel for="twitter_url" value="Twitter / X" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="h-5 w-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                                </svg>
                                            </div>
                                            <input
                                                id="twitter_url"
                                                type="url"
                                                v-model="form.twitter_url"
                                                class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                placeholder="https://twitter.com/votre-profil"
                                            />
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.twitter_url" />
                                    </div>

                                    <!-- LinkedIn -->
                                    <div>
                                        <InputLabel for="linkedin_url" value="LinkedIn" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="h-5 w-5 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                </svg>
                                            </div>
                                            <input
                                                id="linkedin_url"
                                                type="url"
                                                v-model="form.linkedin_url"
                                                class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                placeholder="https://linkedin.com/in/votre-profil"
                                            />
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.linkedin_url" />
                                    </div>

                                    <!-- YouTube -->
                                    <div>
                                        <InputLabel for="youtube_url" value="YouTube" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                                </svg>
                                            </div>
                                            <input
                                                id="youtube_url"
                                                type="url"
                                                v-model="form.youtube_url"
                                                class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                placeholder="https://youtube.com/@votre-chaine"
                                            />
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.youtube_url" />
                                    </div>

                                    <!-- TikTok -->
                                    <div>
                                        <InputLabel for="tiktok_url" value="TikTok" />
                                        <div class="relative mt-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="h-5 w-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                                </svg>
                                            </div>
                                            <input
                                                id="tiktok_url"
                                                type="url"
                                                v-model="form.tiktok_url"
                                                class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                placeholder="https://tiktok.com/@votre-profil"
                                            />
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.tiktok_url" />
                                    </div>
                                </div>

                                <div class="mt-6 rounded-xl bg-blue-50 p-4 dark:bg-blue-900/20 border border-blue-200/30 dark:border-blue-500/20">
                                    <p class="text-xs text-blue-700 dark:text-blue-300">
                                        <strong>💡 Conseil :</strong> Ajoutez uniquement les réseaux sociaux où vous êtes actif. Les icônes apparaîtront automatiquement dans le footer de votre site. Pensez à vérifier que vos liens sont corrects et commencent par "https://".
                                    </p>
                                </div>
                            </div>

                            <!-- FAQ Section -->
                            <div class="rounded-2xl border border-yellow-200/50 bg-gradient-to-r from-yellow-50 to-amber-50 p-8 dark:border-yellow-500/30 dark:from-yellow-900/20 dark:to-amber-900/20 shadow-xl hover:shadow-2xl transition-all duration-300 backdrop-blur-xl">
                                <div class="mb-6 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-xl p-3 shadow-lg">
                                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="ml-4 text-2xl font-bold bg-gradient-to-r from-yellow-600 to-amber-600 bg-clip-text text-transparent">
                                            ❓ Section FAQ (Questions fréquentes)
                                        </h3>
                                    </div>
                                    <button @click="openCreateFaqModal" class="inline-flex items-center rounded-xl bg-gradient-to-r from-yellow-500 to-amber-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-yellow-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Nouvelle Question
                                    </button>
                                </div>
                                <p class="mb-6 text-gray-600 dark:text-gray-400">
                                    Gérez vos questions fréquentes directement depuis cette page
                                </p>
                                
                                <!-- FAQ Stats -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div class="rounded-xl bg-white/50 p-4 shadow-md dark:bg-gray-800/50 backdrop-blur-sm border border-yellow-200/30 dark:border-yellow-500/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total FAQs</p>
                                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                                                    {{ faqsCount }}
                                                </p>
                                            </div>
                                            <div class="rounded-full bg-yellow-100 p-3 dark:bg-yellow-900/30">
                                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="rounded-xl bg-white/50 p-4 shadow-md dark:bg-gray-800/50 backdrop-blur-sm border border-green-200/30 dark:border-green-500/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">FAQs actives</p>
                                                <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">
                                                    {{ faqsActiveCount }}
                                                </p>
                                            </div>
                                            <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/30">
                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="rounded-xl bg-white/50 p-4 shadow-md dark:bg-gray-800/50 backdrop-blur-sm border border-orange-200/30 dark:border-orange-500/20">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Statut</p>
                                                <p class="mt-1 text-sm font-semibold" :class="faqsActiveCount > 0 ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400'">
                                                    {{ faqsActiveCount > 0 ? '✓ Configuré' : '! À configurer' }}
                                                </p>
                                            </div>
                                            <div class="rounded-full p-3" :class="faqsActiveCount > 0 ? 'bg-green-100 dark:bg-green-900/30' : 'bg-orange-100 dark:bg-orange-900/30'">
                                                <svg class="h-6 w-6" :class="faqsActiveCount > 0 ? 'text-green-600' : 'text-orange-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path v-if="faqsActiveCount > 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- FAQs List -->
                                <div v-if="faqs && faqs.length > 0" class="mt-6 space-y-3">
                                    <div
                                        v-for="faq in faqs"
                                        :key="faq.id"
                                        class="rounded-xl bg-white/50 p-5 shadow-md transition-all hover:shadow-xl dark:bg-gray-800/50 backdrop-blur-sm border border-yellow-200/30 dark:border-yellow-500/20 hover:scale-[1.01] duration-200"
                                    >
                                        <!-- Status Badge & Order -->
                                        <div class="mb-3 flex items-start justify-between">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    :class="[
                                                        faq.is_active
                                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                        'inline-flex rounded-full px-2 py-1 text-xs font-semibold'
                                                    ]"
                                                >
                                                    {{ faq.is_active ? 'Actif' : 'Inactif' }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    Ordre: {{ faq.order }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Question -->
                                        <div class="mb-2">
                                            <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ faq.question }}
                                            </h5>
                                        </div>

                                        <!-- Answer -->
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">
                                                {{ faq.answer }}
                                            </p>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex gap-2">
                                            <button
                                                @click="openEditFaqModal(faq)"
                                                class="flex-1 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                                            >
                                                Modifier
                                            </button>
                                            <button
                                                @click="deleteFaq(faq)"
                                                class="rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-4 py-2 text-sm font-semibold text-white shadow-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                                            >
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Empty State -->
                                <div v-else class="mt-6 rounded-2xl bg-white/50 p-12 text-center shadow-md dark:bg-gray-800/50 backdrop-blur-sm border border-yellow-200/30 dark:border-yellow-500/20">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucune question</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Commencez par créer votre première question fréquemment posée.
                                    </p>
                                    <div class="mt-6">
                                        <button @click="openCreateFaqModal" class="inline-flex items-center rounded-xl bg-gradient-to-r from-yellow-500 to-amber-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-yellow-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Créer une Question
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="rounded-2xl border-2 border-dashed border-indigo-300 bg-gradient-to-br from-indigo-50 to-purple-50 p-8 dark:border-indigo-500/50 dark:from-indigo-900/20 dark:to-purple-900/20 shadow-xl backdrop-blur-xl">
                                <div class="mb-6 flex items-center">
                                    <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-3 shadow-lg">
                                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </div>
                                    <h4 class="ml-4 text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                        👁️ Aperçu en temps réel
                                    </h4>
                                </div>
                                <div class="space-y-4">
                                    <!-- Hero Preview -->
                                    <div class="rounded-xl bg-white/70 p-4 shadow-md dark:bg-gray-800/70 backdrop-blur-sm border border-indigo-200/30 dark:border-indigo-500/20">
                                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Titre Hero
                                        </p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ form.hero_title || '(Aucun titre défini)' }}
                                        </p>
                                    </div>
                                    
                                    <!-- Subtitle Preview -->
                                    <div class="rounded-xl bg-white/70 p-4 shadow-md dark:bg-gray-800/70 backdrop-blur-sm border border-indigo-200/30 dark:border-indigo-500/20">
                                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Sous-titre
                                        </p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ form.hero_subtitle || '(Aucun sous-titre défini)' }}
                                        </p>
                                    </div>
                                    
                                    <!-- CTA Preview -->
                                    <div class="rounded-xl bg-white/70 p-4 shadow-md dark:bg-gray-800/70 backdrop-blur-sm border border-indigo-200/30 dark:border-indigo-500/20">
                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Bouton CTA
                                        </p>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg"
                                            disabled
                                        >
                                            {{ form.cta_text || 'Texte du bouton' }}
                                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Completion Stats -->
                                    <div class="mt-4 grid grid-cols-2 gap-3">
                                        <div class="rounded-xl bg-white/70 p-3 text-center shadow-md dark:bg-gray-800/70 backdrop-blur-sm border border-indigo-200/30 dark:border-indigo-500/20">
                                            <p class="text-2xl font-bold text-indigo-600">{{ completionPercentage }}%</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Complet</p>
                                        </div>
                                        <div class="rounded-xl bg-white/70 p-3 text-center shadow-md dark:bg-gray-800/70 backdrop-blur-sm border border-green-200/30 dark:border-green-500/20">
                                            <p class="text-2xl font-bold text-green-600">
                                                {{ heroTitleCount + heroSubtitleCount + aboutTextCount + methodTextCount + ctaTextCount }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Caractères</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Actions -->
                            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                                <div class="flex items-center gap-2">
                                    <svg v-if="form.recentlySuccessful" class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <p v-if="form.recentlySuccessful" class="text-sm font-medium text-green-600 dark:text-green-400">
                                        ✅ Modifications enregistrées avec succès !
                                    </p>
                                </div>
                                <div class="flex gap-3">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 border border-transparent rounded-xl font-bold text-white shadow-xl hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200"
                                    >
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="mr-3 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ form.processing ? 'Enregistrement en cours...' : '💾 Enregistrer les modifications' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Modal Create/Edit -->
        <div
            v-if="showFaqModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    aria-hidden="true"
                    @click="closeFaqModal"
                ></div>

                <!-- Modal panel -->
                <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                    <form @submit.prevent="submitFaq">
                        <div class="bg-white px-4 pb-4 pt-5 dark:bg-gray-800 sm:p-6 sm:pb-4">
                            <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-gray-100" id="modal-title">
                                {{ editingFaq ? 'Modifier la question' : 'Créer une nouvelle question' }}
                            </h3>

                            <div class="space-y-4">
                                <!-- Question -->
                                <div>
                                    <InputLabel for="faq_question" value="Question *" />
                                    <TextInput
                                        id="faq_question"
                                        v-model="faqForm.question"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                        placeholder="Ex: Combien coûte un accompagnement ?"
                                    />
                                    <InputError class="mt-2" :message="faqForm.errors.question" />
                                </div>

                                <!-- Answer -->
                                <div>
                                    <InputLabel for="faq_answer" value="Réponse *" />
                                    <textarea
                                        id="faq_answer"
                                        v-model="faqForm.answer"
                                        rows="5"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                        placeholder="Rédigez votre réponse détaillée..."
                                    ></textarea>
                                    <InputError class="mt-2" :message="faqForm.errors.answer" />
                                </div>

                                <!-- Order -->
                                <div>
                                    <InputLabel for="faq_order" value="Ordre d'affichage" />
                                    <TextInput
                                        id="faq_order"
                                        v-model="faqForm.order"
                                        type="number"
                                        min="0"
                                        class="mt-1 block w-full"
                                        placeholder="0"
                                    />
                                    <InputError class="mt-2" :message="faqForm.errors.order" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Les questions sont triées par ordre croissant (0 = en premier)
                                    </p>
                                </div>

                                <!-- Is Active -->
                                <div class="flex items-center">
                                    <input
                                        id="faq_is_active"
                                        v-model="faqForm.is_active"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:ring-offset-gray-800"
                                    />
                                    <label for="faq_is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                        Question active (visible sur le site public)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-4 py-3 dark:bg-gray-700 sm:flex sm:flex-row-reverse sm:px-6">
                            <PrimaryButton
                                type="submit"
                                class="w-full justify-center sm:ml-3 sm:w-auto"
                                :class="{ 'opacity-25': faqForm.processing }"
                                :disabled="faqForm.processing"
                            >
                                {{ editingFaq ? 'Mettre à jour' : 'Créer' }}
                            </PrimaryButton>
                            <button
                                type="button"
                                @click="closeFaqModal"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-600 dark:text-gray-100 dark:ring-gray-500 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto"
                            >
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
