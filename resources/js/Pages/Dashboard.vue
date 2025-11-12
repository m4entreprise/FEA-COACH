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

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Admin Access Banner -->
                <div v-if="user.role === 'admin'" class="mb-6 overflow-hidden bg-blue-600 shadow-sm sm:rounded-lg">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold mb-1">🔐 Accès Administrateur</h3>
                                <p class="text-blue-100">
                                    Vous avez accès au panel d'administration pour gérer tous les coachs.
                                </p>
                            </div>
                            <Link
                                :href="route('admin.coaches.index')"
                                class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-blue-600 shadow-sm hover:bg-blue-50"
                            >
                                Panel Admin
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="mb-6 overflow-hidden bg-red-50 border border-red-200 shadow-sm sm:rounded-lg">
                    <div class="p-6 text-red-900">
                        <h3 class="text-lg font-bold mb-2">⚠️ Erreur</h3>
                        <p>{{ error }}</p>
                    </div>
                </div>

                <!-- Welcome Section -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-2xl font-bold mb-2">Bienvenue, {{ user.name }} ! 👋</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ isAdmin ? 'Gérez la plateforme depuis le panel d\'administration.' : 'Gérez votre site de coaching depuis ce tableau de bord.' }}
                        </p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div v-if="coach && stats" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Profile Completion -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Profil</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ stats.profile_completion }}%
                                </p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" :style="`width: ${stats.profile_completion}%`"></div>
                        </div>
                    </div>

                    <!-- Active Plans -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Plans actifs</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ stats.active_plans }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">sur {{ stats.total_plans }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Transformations -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Transformations</p>
                                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ stats.total_transformations }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div :class="[stats.is_active ? 'bg-green-500' : 'bg-red-500', 'flex-shrink-0 rounded-md p-3']">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Statut site</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ stats.is_active ? 'Actif' : 'Inactif' }}
                                </p>
                                <a :href="`http://${coach.subdomain}.localhost:8000`" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                    Voir le site →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Branding Card -->
                    <Link :href="route('dashboard.branding')" class="block">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Branding</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">
                                Gérez votre logo, vos couleurs et l'image hero de votre site.
                            </p>
                        </div>
                    </Link>

                    <!-- Content Card -->
                    <Link :href="route('dashboard.content')" class="block">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contenu</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">
                                Éditez les textes de votre site : titre, description, à propos, méthode.
                            </p>
                        </div>
                    </Link>

                    <!-- Gallery Card -->
                    <Link :href="route('dashboard.gallery')" class="block">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Galerie</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">
                                Ajoutez et gérez vos transformations avant/après.
                            </p>
                        </div>
                    </Link>

                    <!-- Plans Card -->
                    <Link :href="route('dashboard.plans')" class="block">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                                    <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Plans</h4>
                                </div>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">
                                Gérez vos offres tarifaires et prix de coaching.
                            </p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
