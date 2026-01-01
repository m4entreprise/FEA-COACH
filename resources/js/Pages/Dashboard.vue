<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import OnboardingTour from '@/Components/OnboardingTour.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
    coach: Object,
    stats: Object,
    recentTransformations: Array,
    isAdmin: Boolean,
    error: String,
    hasCompletedOnboarding: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// Modal for profile completion
const showProfileModal = ref(false);

const openProfileModal = () => {
    if (props.stats && props.stats.profile_completion < 100) {
        showProfileModal.value = true;
    }
};

const closeProfileModal = () => {
    showProfileModal.value = false;
};

// Onboarding Tour
const showOnboarding = ref(false);

const onboardingSteps = [
    {
        target: '[data-tour="welcome"]',
        icon: '👋',
        title: 'Bienvenue sur FEA Coach !',
        content: '<p class="mb-3">Bienvenue dans votre espace de gestion ! Ce tableau de bord vous permet de <strong>créer et gérer facilement votre site de coaching professionnel</strong>.</p><p>Laissez-nous vous guider à travers les principales fonctionnalités.</p>',
    },
    {
        target: '[data-tour="profile"]',
        icon: '📊',
        title: 'Complétion de votre profil',
        content: '<p class="mb-3">Ici vous pouvez suivre la <strong>complétion de votre profil</strong>. Plus votre profil est complet, plus votre site sera attractif pour vos clients potentiels.</p><p>Cliquez sur cette carte pour voir ce qu\'il vous reste à compléter !</p>',
    },
    {
        target: '[data-tour="subscription"]',
        icon: '💳',
        title: 'Votre abonnement',
        content: '<p class="mb-3">Suivez l\'état de votre abonnement et votre période d\'essai. Vous bénéficiez d\'une <strong>période d\'essai gratuite</strong> pour tester toutes les fonctionnalités.</p><p>Vous pourrez gérer votre abonnement directement depuis cette section.</p>',
    },
    {
        target: '[data-tour="support"]',
        icon: '🆘',
        title: 'Support disponible',
        content: '<p class="mb-3">Besoin d\'aide ? Notre équipe de support est là pour vous accompagner !</p><p>N\'hésitez pas à nous contacter si vous avez des questions ou besoin d\'assistance.</p>',
    },
    {
        target: '[data-tour="site-status"]',
        icon: '👁️',
        title: 'Statut de votre site',
        content: '<p class="mb-3">Vérifiez si votre site est <strong>actif</strong> ou <strong>inactif</strong>.</p><p>Cliquez sur "Voir le site" pour prévisualiser votre site public à tout moment !</p>',
    },
    {
        target: '[data-tour="branding"]',
        icon: '🎨',
        title: 'Personnalisation visuelle',
        content: '<p class="mb-3">Créez l\'identité visuelle de votre site : <strong>logo, couleurs, image hero</strong>.</p><p>Donnez à votre site un aspect professionnel qui reflète votre marque personnelle.</p>',
    },
    {
        target: '[data-tour="content"]',
        icon: '✍️',
        title: 'Gestion du contenu',
        content: '<p class="mb-3">Éditez tous les <strong>textes de votre site</strong> : titre, description, section "À propos", méthode de coaching.</p><p>Racontez votre histoire et présentez vos services de manière convaincante.</p>',
    },
    {
        target: '[data-tour="gallery"]',
        icon: '📸',
        title: 'Galerie de transformations',
        content: '<p class="mb-3">Ajoutez vos <strong>photos avant/après</strong> pour montrer les résultats obtenus par vos clients.</p><p>Les transformations sont un excellent moyen de prouver l\'efficacité de votre coaching !</p>',
    },
    {
        target: '[data-tour="plans"]',
        icon: '💰',
        title: 'Vos offres tarifaires',
        content: '<p class="mb-3">Définissez vos <strong>plans de coaching</strong> et vos tarifs.</p><p>Créez différentes offres adaptées aux besoins de vos clients.</p>',
    },
    {
        target: '[data-tour="clients"]',
        icon: '👥',
        title: 'Gestion des clients',
        content: '<p class="mb-3">Centralisez les <strong>informations de vos clients</strong> et ajoutez des notes pour suivre leur progression.</p><p>Un outil essentiel pour un coaching personnalisé et efficace.</p>',
    },
    {
        target: '[data-tour="contact"]',
        icon: '📧',
        title: 'Messages de contact',
        content: '<p class="mb-3">Consultez tous les <strong>messages reçus</strong> via le formulaire de contact de votre site.</p><p>Ne manquez aucune opportunité de nouveau client !</p>',
    },
    {
        target: '[data-tour="legal"]',
        icon: '⚖️',
        title: 'Mentions légales',
        content: '<p class="mb-3">Personnalisez vos <strong>CGV et mentions légales</strong> avec votre numéro de TVA.</p><p>Restez en conformité légale facilement.</p>',
    },
    {
        target: null,
        icon: '🎓',
        title: 'Service exclusif FEA',
        content: `
            <div class="space-y-4">
                <p class="text-base font-semibold text-purple-900 dark:text-purple-100">
                    Ce service est proposé <strong>exclusivement aux diplômés de Fitness Education Academy</strong> 🎓
                </p>
                
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 border-2 border-green-500/30">
                    <p class="text-lg font-bold text-green-700 dark:text-green-300 mb-2">
                        💰 Tarif préférentiel
                    </p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                        20€ HTVA/mois
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="line-through">30€ HTVA/mois</span> - <strong>Économisez 10€/mois</strong> grâce à FEA !
                    </p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200/30 dark:border-blue-500/20">
                    <p class="text-sm text-blue-800 dark:text-blue-200 leading-relaxed">
                        <strong>⚠️ Important :</strong> À la fin de votre période d'essai, <strong>poursuivez votre abonnement</strong> pour continuer à bénéficier de ce tarif exclusif réservé aux diplômés FEA. Cette réduction est valable tant que votre abonnement reste actif !
                    </p>
                </div>

                <p class="text-center text-sm text-gray-600 dark:text-gray-400 pt-2">
                    Vous êtes maintenant prêt à créer un site professionnel qui attirera vos futurs clients ! 🚀
                </p>
            </div>
        `,
    },
];

const handleOnboardingComplete = () => {
    showOnboarding.value = false;
    // Save to backend that user completed onboarding
    router.post(route('dashboard.onboarding.complete'), {}, {
        preserveScroll: true,
    });
};

const handleOnboardingSkip = () => {
    showOnboarding.value = false;
    router.post(route('dashboard.onboarding.complete'), {}, {
        preserveScroll: true,
    });
};

const startTutorial = () => {
    showOnboarding.value = true;
};

onMounted(() => {
    // Show onboarding if user hasn't completed it
    if (!props.hasCompletedOnboarding) {
        setTimeout(() => {
            showOnboarding.value = true;
        }, 500);
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Dashboard
                </h2>
                <button
                    @click="startTutorial"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-semibold rounded-xl hover:from-purple-700 hover:to-pink-700 shadow-lg transform hover:scale-105 transition-all duration-200"
                >
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tutoriel
                </button>
            </div>
        </template>

        <!-- Onboarding Tour -->
        <OnboardingTour
            :steps="onboardingSteps"
            :show="showOnboarding"
            @complete="handleOnboardingComplete"
            @skip="handleOnboardingSkip"
            @close="handleOnboardingSkip"
        />

        <div class="py-12 bg-gradient-to-br from-slate-50 via-purple-50 to-slate-50 dark:from-slate-900 dark:via-purple-900/20 dark:to-slate-900 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Admin Access Banner -->
                <div v-if="user.role === 'admin'" class="mb-8 overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-600 shadow-xl sm:rounded-2xl border border-blue-400/20 backdrop-blur-xl transform hover:scale-[1.02] transition-all duration-300">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold mb-2 flex items-center gap-2">
                                    <span class="text-2xl">🔐</span>
                                    <span>Accès Administrateur</span>
                                </h3>
                                <p class="text-blue-100">
                                    Vous avez accès au panel d'administration pour gérer tous les coachs.
                                </p>
                            </div>
                            <Link
                                :href="route('admin.coaches.index')"
                                class="inline-flex items-center rounded-xl bg-white px-6 py-3 text-sm font-semibold text-blue-600 shadow-lg hover:bg-blue-50 hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                            >
                                Panel Admin
                                <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="mb-8 overflow-hidden bg-red-50 border-2 border-red-300 shadow-lg sm:rounded-2xl backdrop-blur-xl dark:bg-red-900/20 dark:border-red-500/30">
                    <div class="p-6 text-red-900 dark:text-red-200">
                        <h3 class="text-xl font-bold mb-2 flex items-center gap-2">
                            <span class="text-2xl">⚠️</span>
                            <span>Erreur</span>
                        </h3>
                        <p>{{ error }}</p>
                    </div>
                </div>

                <!-- Welcome Section -->
                <div data-tour="welcome" class="mb-8 overflow-hidden bg-gradient-to-br from-white to-purple-50 shadow-xl sm:rounded-3xl dark:from-gray-800 dark:to-purple-900/20 border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl transform hover:scale-[1.01] transition-all duration-300">
                    <div class="p-8 text-gray-900 dark:text-gray-100">
                        <h3 class="text-3xl font-bold mb-3 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                            Bienvenue, {{ user.name }} ! 👋
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">
                            {{ isAdmin ? 'Gérez la plateforme depuis le panel d\'administration.' : 'Gérez votre site de coaching depuis ce tableau de bord.' }}
                        </p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div v-if="coach && stats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 items-stretch">
                    <!-- Profile Completion -->
                    <div 
                        data-tour="profile"
                        @click="openProfileModal" 
                        :class="[stats.profile_completion < 100 ? 'cursor-pointer' : 'cursor-default']"
                        class="bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group flex flex-col h-full"
                    >
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">Profil</p>
                                <p class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent">
                                    {{ stats.profile_completion }}%
                                </p>
                            </div>
                        </div>
                        <div class="mt-auto w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700 overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-500 h-3 rounded-full transition-all duration-500 ease-out shadow-lg" :style="`width: ${stats.profile_completion}%`"></div>
                        </div>
                    </div>

                    <!-- Subscription Status -->
                    <div data-tour="subscription" class="bg-gradient-to-br from-white to-emerald-50 dark:from-gray-800 dark:to-emerald-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-emerald-200/50 dark:border-emerald-500/30 backdrop-blur-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group flex flex-col h-full">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">Abonnement</p>
                                <p v-if="stats.subscription.is_on_trial" class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">
                                    Essai - {{ stats.subscription.trial_days_left }} jours
                                </p>
                                <p v-else class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">
                                    {{ stats.subscription.status === 'active' ? 'Actif' : 'Inactif' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <Link 
                                :href="route('dashboard.subscription')"
                                class="w-full px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white text-sm font-semibold rounded-lg hover:from-emerald-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 shadow-lg text-center"
                            >
                                {{ stats.subscription.is_on_trial ? 'S\'abonner maintenant' : 'Gérer l\'abonnement' }}
                            </Link>
                        </div>
                    </div>

                    <!-- Support -->
                    <div data-tour="support" class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group flex flex-col h-full">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">Support</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                    Besoin d'aide ?
                                </p>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <Link 
                                :href="route('dashboard.support')"
                                class="w-full inline-flex justify-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-purple-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg text-center"
                            >
                                Contacter le support
                            </Link>
                        </div>
                    </div>

                    <!-- Status -->
                    <div data-tour="site-status" :class="[stats.is_active ? 'from-white to-green-50 dark:from-gray-800 dark:to-green-900/20 border-green-200/50 dark:border-green-500/30' : 'from-white to-red-50 dark:from-gray-800 dark:to-red-900/20 border-red-200/50 dark:border-red-500/30', 'bg-gradient-to-br overflow-hidden shadow-lg sm:rounded-2xl p-6 border backdrop-blur-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group flex flex-col h-full']">
                        <div class="flex items-center">
                            <div :class="[stats.is_active ? 'from-green-500 to-green-600' : 'from-red-500 to-red-600', 'flex-shrink-0 bg-gradient-to-br rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300']">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">Statut site</p>
                                <p :class="[stats.is_active ? 'from-green-600 to-green-500' : 'from-red-600 to-red-500', 'text-3xl font-bold bg-gradient-to-r bg-clip-text text-transparent mb-2']">
                                    {{ stats.is_active ? '✓ Actif' : '✗ Inactif' }}
                                </p>
                                <a :href="route('coach.site', { coach_slug: coach.slug || coach.subdomain })" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                    Voir le site
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center gap-2">
                    <span class="text-3xl">⚡</span>
                    <span>Actions rapides</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Branding Card -->
                    <Link data-tour="branding" :href="route('dashboard.branding')" class="block group">
                        <div class="bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl transform group-hover:scale-105 group-hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Branding</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                Gérez votre logo, vos couleurs et l'image hero de votre site.
                            </p>
                        </div>
                    </Link>

                    <!-- Content Card -->
                    <Link data-tour="content" :href="route('dashboard.content')" class="block group">
                        <div class="bg-gradient-to-br from-white to-emerald-50 dark:from-gray-800 dark:to-emerald-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border border-emerald-200/50 dark:border-emerald-500/30 backdrop-blur-xl transform group-hover:scale-105 group-hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Contenu</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                Éditez les textes de votre site : titre, description, à propos, méthode.
                            </p>
                        </div>
                    </Link>

                    <!-- Gallery Card -->
                    <Link data-tour="gallery" :href="route('dashboard.gallery')" class="block group">
                        <div class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl transform group-hover:scale-105 group-hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Galerie</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                Ajoutez et gérez vos transformations avant/après.
                            </p>
                        </div>
                    </Link>

                    <!-- Plans Card -->
                    <Link data-tour="plans" :href="route('dashboard.plans')" class="block group">
                        <div class="bg-gradient-to-br from-white to-amber-50 dark:from-gray-800 dark:to-amber-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border border-amber-200/50 dark:border-amber-500/30 backdrop-blur-xl transform group-hover:scale-105 group-hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-yellow-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Plans</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                Gérez vos offres tarifaires et prix de coaching.
                            </p>
                        </div>
                    </Link>

                    <!-- Clients Card -->
                    <Link data-tour="clients" :href="route('dashboard.clients.index')" class="block group">
                        <div class="bg-gradient-to-br from-white to-indigo-50 dark:from-gray-800 dark:to-indigo-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border border-indigo-200/50 dark:border-indigo-500/30 backdrop-blur-xl transform group-hover:scale-105 group-hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Clients</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                Gérez vos clients et centralisez leurs informations et notes.
                            </p>
                        </div>
                    </Link>

                    <!-- Contact Card -->
                    <Link data-tour="contact" :href="route('dashboard.contact')" class="block group">
                        <div class="bg-gradient-to-br from-white to-rose-50 dark:from-gray-800 dark:to-rose-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border border-rose-200/50 dark:border-rose-500/30 backdrop-blur-xl transform group-hover:scale-105 group-hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-gradient-to-br from-rose-500 to-pink-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-9 4v8" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Contact</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                Consultez et gérez les messages reçus depuis le formulaire de contact de votre site.
                            </p>
                        </div>
                    </Link>

                    <!-- Legal Card -->
                    <Link data-tour="legal" :href="route('dashboard.legal')" class="block group">
                        <div class="bg-gradient-to-br from-white to-slate-50 dark:from-gray-800 dark:to-slate-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 hover:shadow-2xl transition-all duration-300 cursor-pointer border border-slate-200/50 dark:border-slate-500/30 backdrop-blur-xl transform group-hover:scale-105 group-hover:-translate-y-1">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-gradient-to-br from-slate-600 to-slate-700 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">Mentions légales</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                Personnalisez vos CGV et mentions légales avec votre numéro de TVA.
                            </p>
                        </div>
                    </Link>
                </div>

                <!-- Profile Completion Modal -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showProfileModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click="closeProfileModal">
                        <div 
                            class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden border-2 border-blue-500/30"
                            @click.stop
                        >
                            <!-- Modal Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <h3 class="text-2xl font-bold">Complétion du profil</h3>
                                            <p class="text-blue-100 text-sm mt-1">{{ stats.profile_completion }}% complété</p>
                                        </div>
                                    </div>
                                    <button 
                                        @click="closeProfileModal" 
                                        class="text-white hover:bg-white/20 rounded-full p-2 transition-colors"
                                    >
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <!-- Progress Bar -->
                                <div class="mt-4 h-3 overflow-hidden rounded-full bg-blue-400/50 backdrop-blur-sm">
                                    <div
                                        class="h-full bg-white shadow-lg transition-all duration-500"
                                        :style="{ width: stats.profile_completion + '%' }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-6 overflow-y-auto max-h-[calc(80vh-12rem)]">
                                <div v-if="stats.profile_missing_fields && stats.profile_missing_fields.length > 0">
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                                        Pour compléter votre profil à 100%, il vous manque les éléments suivants :
                                    </p>
                                    
                                    <div class="space-y-3">
                                        <Link
                                            v-for="field in stats.profile_missing_fields"
                                            :key="field.field"
                                            :href="route(field.route)"
                                            class="block group"
                                        >
                                            <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-lg transition-all duration-200 bg-white dark:bg-gray-900/50">
                                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                        {{ field.label }}
                                                    </p>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        Cliquez pour compléter
                                                    </p>
                                                </div>
                                                <svg class="h-5 w-5 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </Link>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 mb-4">
                                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        Félicitations ! 🎉
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Votre profil est complet à 100% !
                                    </p>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                                <button 
                                    @click="closeProfileModal"
                                    class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg"
                                >
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
