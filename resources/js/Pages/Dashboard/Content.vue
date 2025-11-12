<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    coach: Object,
    faqs: Array,
    faqsCount: Number,
    faqsActiveCount: Number,
});

const form = useForm({
    hero_title: props.coach?.hero_title || '',
    hero_subtitle: props.coach?.hero_subtitle || '',
    about_text: props.coach?.about_text || '',
    method_text: props.coach?.method_text || '',
    cta_text: props.coach?.cta_text || 'Réserver une séance',
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
</script>

<template>
    <Head title="Contenu du site" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Gestion du Contenu
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Completion Card -->
                <div class="overflow-hidden rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 shadow-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">Complétion du contenu</h3>
                                <p class="mt-1 text-sm text-blue-100">
                                    {{ completionPercentage }}% des sections sont remplies
                                </p>
                            </div>
                            <div class="text-4xl font-bold">
                                {{ completionPercentage }}%
                            </div>
                        </div>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-blue-400">
                            <div
                                class="h-full bg-white transition-all duration-500"
                                :style="{ width: completionPercentage + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                <strong>💡 Conseil :</strong> Personnalisez les textes de votre site public pour attirer vos clients. 
                                Soyez authentique, clair et mettez en avant votre différence !
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Main Form -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Hero Section -->
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900">
                                <div class="mb-4 flex items-center">
                                    <svg class="mr-2 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        🎯 Section Hero (Première impression)
                                    </h3>
                                </div>
                                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
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

                            <!-- About Section -->
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900">
                                <div class="mb-4 flex items-center">
                                    <svg class="mr-2 h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        👤 Section "À propos"
                                    </h3>
                                </div>
                                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
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
                                    <div class="mt-2 rounded-md bg-green-50 p-3 dark:bg-green-900/20">
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
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900">
                                <div class="mb-4 flex items-center">
                                    <svg class="mr-2 h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        ⚡ Section "Ma méthode"
                                    </h3>
                                </div>
                                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                                    Expliquez votre approche unique et ce qui vous différencie
                                </p>
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
                                        rows="8"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                        placeholder="Décrivez votre méthode d'entraînement, votre philosophie, vos principes clés..."
                                        maxlength="5000"
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.method_text" />
                                    <div class="mt-2 rounded-md bg-purple-50 p-3 dark:bg-purple-900/20">
                                        <p class="text-xs text-purple-700 dark:text-purple-300">
                                            <strong>💡 Conseils :</strong>
                                        </p>
                                        <ul class="mt-1 list-inside list-disc space-y-1 text-xs text-purple-700 dark:text-purple-300">
                                            <li>Décrivez votre approche étape par étape</li>
                                            <li>Expliquez vos principes fondamentaux</li>
                                            <li>Mentionnez les bénéfices concrets</li>
                                            <li>Montrez ce qui vous rend unique</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA Section -->
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-900">
                                <div class="mb-4 flex items-center">
                                    <svg class="mr-2 h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        🚀 Appel à l'action (CTA)
                                    </h3>
                                </div>
                                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
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
                                    <div class="mt-2 rounded-md bg-orange-50 p-3 dark:bg-orange-900/20">
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

                            <!-- FAQ Section -->
                            <div class="rounded-lg border border-gray-200 bg-gradient-to-r from-yellow-50 to-amber-50 p-6 dark:border-gray-700 dark:from-yellow-900/20 dark:to-amber-900/20">
                                <div class="mb-4 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="mr-2 h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            ❓ Section FAQ (Questions fréquentes)
                                        </h3>
                                    </div>
                                    <a
                                        :href="route('dashboard.faq')"
                                        class="inline-flex items-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 transition-colors"
                                    >
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Gérer mes FAQs
                                    </a>
                                </div>
                                <p class="mb-4 text-sm text-gray-700 dark:text-gray-300">
                                    Les FAQs répondent aux questions les plus fréquentes de vos visiteurs et améliorent leur expérience
                                </p>
                                
                                <!-- FAQ Stats -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
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
                                    
                                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
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
                                    
                                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
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
                                
                                <!-- FAQ Preview / Empty State -->
                                <div v-if="faqs && faqs.length > 0" class="mt-6">
                                    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        📝 Aperçu de vos FAQs actives ({{ faqs.length }} affichées, max 5)
                                    </h4>
                                    <div class="space-y-3">
                                        <div
                                            v-for="faq in faqs"
                                            :key="faq.id"
                                            class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800"
                                        >
                                            <div class="flex items-center justify-between">
                                                <h5 class="font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ faq.question }}
                                                </h5>
                                                <span v-if="faq.is_active !== undefined" class="text-xs px-2 py-1 rounded" :class="faq.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                    {{ faq.is_active ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                {{ faq.answer }}
                                            </p>
                                        </div>
                                    </div>
                                    <div v-if="faqsActiveCount > 5" class="mt-3 text-center">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            + {{ faqsActiveCount - 5 }} autre(s) FAQ(s) active(s) · 
                                            <a :href="route('dashboard.faq')" class="text-yellow-600 hover:text-yellow-500 font-medium">
                                                Voir toutes
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                
                                <div v-else class="mt-4 rounded-md bg-yellow-100 p-4 dark:bg-yellow-900/30">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">
                                                Aucune FAQ active
                                            </h4>
                                            <p class="mt-1 text-xs text-yellow-700 dark:text-yellow-300">
                                                <strong>💡 Astuce :</strong> Créez au moins 3-5 FAQs pour répondre aux questions récurrentes de vos clients potentiels et gagner leur confiance !
                                            </p>
                                            <div class="mt-3">
                                                <a
                                                    :href="route('dashboard.faq')"
                                                    class="inline-flex items-center rounded-md bg-yellow-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-yellow-500"
                                                >
                                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Créer ma première FAQ
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Section -->
                            <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gradient-to-br from-gray-50 to-gray-100 p-8 dark:border-gray-600 dark:from-gray-800 dark:to-gray-900">
                                <div class="mb-4 flex items-center">
                                    <svg class="mr-2 h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        👁️ Aperçu en temps réel
                                    </h4>
                                </div>
                                <div class="space-y-4">
                                    <!-- Hero Preview -->
                                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Titre Hero
                                        </p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ form.hero_title || '(Aucun titre défini)' }}
                                        </p>
                                    </div>
                                    
                                    <!-- Subtitle Preview -->
                                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Sous-titre
                                        </p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ form.hero_subtitle || '(Aucun sous-titre défini)' }}
                                        </p>
                                    </div>
                                    
                                    <!-- CTA Preview -->
                                    <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            Bouton CTA
                                        </p>
                                        <button
                                            type="button"
                                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
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
                                        <div class="rounded-lg bg-white p-3 text-center shadow-sm dark:bg-gray-800">
                                            <p class="text-2xl font-bold text-indigo-600">{{ completionPercentage }}%</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">Complet</p>
                                        </div>
                                        <div class="rounded-lg bg-white p-3 text-center shadow-sm dark:bg-gray-800">
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
                                    <PrimaryButton
                                        type="submit"
                                        :disabled="form.processing"
                                        :class="{ 'opacity-25': form.processing }"
                                        class="inline-flex items-center"
                                    >
                                        <svg v-if="!form.processing" class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <svg v-else class="mr-2 h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ form.processing ? 'Enregistrement en cours...' : 'Enregistrer les modifications' }}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
