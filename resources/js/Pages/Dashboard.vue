<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    coach: Object,
    stats: Object,
    recentTransformations: Array,
    isAdmin: Boolean,
    error: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard
            </h2>
        </template>

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
                <div class="mb-8 overflow-hidden bg-gradient-to-br from-white to-purple-50 shadow-xl sm:rounded-3xl dark:from-gray-800 dark:to-purple-900/20 border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl transform hover:scale-[1.01] transition-all duration-300">
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
                <div v-if="coach && stats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Profile Completion -->
                    <div class="bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">Profil</p>
                                <p class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-500 bg-clip-text text-transparent">
                                    {{ stats.profile_completion }}%
                                </p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700 overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-500 h-3 rounded-full transition-all duration-500 ease-out shadow-lg" :style="`width: ${stats.profile_completion}%`"></div>
                        </div>
                    </div>

                    <!-- Active Plans -->
                    <div class="bg-gradient-to-br from-white to-emerald-50 dark:from-gray-800 dark:to-emerald-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-emerald-200/50 dark:border-emerald-500/30 backdrop-blur-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">Plans actifs</p>
                                <p class="text-4xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent">
                                    {{ stats.active_plans }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mt-1">sur {{ stats.total_plans }} total</p>
                            </div>
                        </div>
                    </div>

                    <!-- Transformations -->
                    <div class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">Transformations</p>
                                <p class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-purple-500 bg-clip-text text-transparent">
                                    {{ stats.total_transformations }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mt-1">témoignages</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div :class="[stats.is_active ? 'from-white to-green-50 dark:from-gray-800 dark:to-green-900/20 border-green-200/50 dark:border-green-500/30' : 'from-white to-red-50 dark:from-gray-800 dark:to-red-900/20 border-red-200/50 dark:border-red-500/30', 'bg-gradient-to-br overflow-hidden shadow-lg sm:rounded-2xl p-6 border backdrop-blur-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group']">
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
                                <a :href="`http://${coach.slug || coach.subdomain}.${$page.props.appDomain}`" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
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
                    <Link :href="route('dashboard.branding')" class="block group">
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
                    <Link :href="route('dashboard.content')" class="block group">
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
                    <Link :href="route('dashboard.gallery')" class="block group">
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
                    <Link :href="route('dashboard.plans')" class="block group">
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
                    <Link :href="route('dashboard.clients.index')" class="block group">
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
                    <Link :href="route('dashboard.contact')" class="block group">
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
                    <Link :href="route('dashboard.legal')" class="block group">
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
            </div>
        </div>
    </AuthenticatedLayout>
</template>
