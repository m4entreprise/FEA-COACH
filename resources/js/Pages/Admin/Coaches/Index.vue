<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    users: Array,
});

const page = usePage();
const appDomain = computed(() => page.props.appDomain);

// Statistiques
const stats = computed(() => {
    const total = props.users.length;
    const feaUsers = props.users.filter(u => u.is_fea_graduate).length;
    const withCoachProfile = props.users.filter(u => u.has_coach_profile).length;
    const activeTrials = props.users.filter(u => u.trial_ends_at && !u.trial_expired).length;
    
    return {
        total,
        feaUsers,
        standardUsers: total - feaUsers,
        withCoachProfile,
        withoutCoachProfile: total - withCoachProfile,
        activeTrials,
    };
});

const deleteUser = (userId, userName) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ? Cette action est irréversible et supprimera aussi son profil coach s'il existe.`)) {
        router.delete(route('admin.coaches.destroy', userId), {
            preserveScroll: true,
        });
    }
};

const getStatusBadgeColor = (user) => {
    if (!user.onboarding_completed) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
    if (!user.has_coach_profile) return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
    if (!user.setup_completed) return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
    if (user.is_active) return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
};

const getStatusText = (user) => {
    if (!user.onboarding_completed) return 'Onboarding en cours';
    if (!user.has_coach_profile) return 'Pas de profil coach';
    if (!user.setup_completed) return 'Setup en cours';
    if (user.is_active) return 'Actif';
    return 'Inactif';
};
</script>

<template>
    <Head title="Gestion des Utilisateurs & Coachs" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Gestion des Utilisateurs & Coachs
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ stats.total }} utilisateur{{ stats.total > 1 ? 's' : '' }} · 
                        {{ stats.feaUsers }} FEA · 
                        {{ stats.withCoachProfile }} avec profil coach · 
                        {{ stats.activeTrials }} période{{ stats.activeTrials > 1 ? 's' : '' }} d'essai active{{ stats.activeTrials > 1 ? 's' : '' }}
                    </p>
                </div>
                <Link
                    :href="route('admin.coaches.create')"
                    class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Créer un Coach
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Success Message -->
                        <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
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

                        <!-- Error Message -->
                        <div v-if="$page.props.flash?.error" class="mb-4 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                        {{ $page.props.flash.error }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Users Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Utilisateur
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Origine
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Statut
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Site Coach
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Période d'essai
                                        </th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                            Inscrit le
                                        </th>
                                        <th scope="col" class="relative px-4 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    <tr v-for="user in users" :key="user.user_id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <!-- Utilisateur -->
                                        <td class="whitespace-nowrap px-4 py-4">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ user.full_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ user.email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Origine -->
                                        <td class="whitespace-nowrap px-4 py-4">
                                            <span
                                                v-if="user.is_fea_graduate"
                                                class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-semibold text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                                            >
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                FEA
                                            </span>
                                            <span
                                                v-else
                                                class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-200"
                                            >
                                                Standard
                                            </span>
                                        </td>

                                        <!-- Statut -->
                                        <td class="whitespace-nowrap px-4 py-4">
                                            <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold', getStatusBadgeColor(user)]">
                                                {{ getStatusText(user) }}
                                            </span>
                                        </td>

                                        <!-- Profil Coach -->
                                        <td class="px-4 py-4">
                                            <div v-if="user.has_coach_profile" class="text-sm">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ user.coach_name }}
                                                </div>
                                                <div v-if="user.subdomain && appDomain" class="text-xs text-gray-500 dark:text-gray-400">
                                                    <a
                                                        :href="`http://${user.subdomain}.${appDomain}`"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                    >
                                                        {{ user.subdomain }}.{{ appDomain }}
                                                    </a>
                                                </div>
                                            </div>
                                            <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                                                —
                                            </span>
                                        </td>

                                        <!-- Période d'essai -->
                                        <td class="whitespace-nowrap px-4 py-4">
                                            <div v-if="user.trial_ends_at" class="text-sm">
                                                <div class="font-medium" :class="user.trial_expired ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
                                                    {{ user.trial_ends_at }}
                                                </div>
                                                <div v-if="!user.trial_expired && user.trial_days_left !== null" class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ user.trial_days_left }} jour{{ user.trial_days_left > 1 ? 's' : '' }} restant{{ user.trial_days_left > 1 ? 's' : '' }}
                                                </div>
                                                <div v-else-if="user.trial_expired" class="text-xs text-red-500">
                                                    Expirée
                                                </div>
                                            </div>
                                            <span v-else class="text-sm text-gray-500 dark:text-gray-400">
                                                —
                                            </span>
                                        </td>

                                        <!-- Date création -->
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ user.created_at }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-medium">
                                            <Link
                                                v-if="user.has_coach_profile"
                                                :href="route('admin.coaches.edit', user.coach_id)"
                                                class="mr-3 text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                            >
                                                Modifier
                                            </Link>
                                            <span v-else class="mr-3 text-gray-400 dark:text-gray-600">
                                                —
                                            </span>
                                            <button
                                                @click="deleteUser(user.user_id, user.full_name)"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                Supprimer
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Empty State -->
                            <div v-if="users.length === 0" class="py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucun utilisateur</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Créez votre premier coach pour commencer.</p>
                                <div class="mt-6">
                                    <Link
                                        :href="route('admin.coaches.create')"
                                        class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                                    >
                                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Créer un Coach
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
