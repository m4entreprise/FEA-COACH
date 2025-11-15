<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    client: Object,
});

const activeTab = ref('info');

const getStatusBadgeClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        paused: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    };
    return classes[status] || classes.active;
};

const getStatusLabel = (status) => {
    const labels = {
        active: 'Actif',
        inactive: 'Inactif',
        paused: 'En pause',
    };
    return labels[status] || status;
};
</script>

<template>
    <Head :title="client.full_name" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <Link :href="route('dashboard.clients.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </Link>
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ client.full_name }}</h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Client depuis le {{ client.created_at }}
                                <span v-if="client.age"> • {{ client.age }} ans</span>
                            </p>
                        </div>
                        <span :class="getStatusBadgeClass(client.status)" class="px-3 py-1 text-sm font-semibold rounded-full">
                            {{ getStatusLabel(client.status) }}
                        </span>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="mb-6">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-8">
                            <button
                                @click="activeTab = 'info'"
                                :class="activeTab === 'info' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            >
                                📋 Informations
                            </button>
                            <button
                                @click="activeTab = 'measurements'"
                                :class="activeTab === 'measurements' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            >
                                📊 Mesures ({{ client.measurements?.length || 0 }})
                            </button>
                            <button
                                @click="activeTab = 'documents'"
                                :class="activeTab === 'documents' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            >
                                📄 Documents ({{ client.documents?.length || 0 }})
                            </button>
                            <button
                                @click="activeTab = 'assessments'"
                                :class="activeTab === 'assessments' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            >
                                📝 Bilans ({{ client.assessments?.length || 0 }})
                            </button>
                            <button
                                @click="activeTab = 'activity'"
                                :class="activeTab === 'activity' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                            >
                                🕐 Activité ({{ client.activities?.length || 0 }})
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                    <!-- Informations Tab -->
                    <div v-if="activeTab === 'info'" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ client.email || 'Non renseigné' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Téléphone</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ client.phone || 'Non renseigné' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de naissance</h3>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ client.date_of_birth || 'Non renseignée' }}</p>
                            </div>
                        </div>

                        <div v-if="client.internal_notes">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes internes</h3>
                            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ client.internal_notes }}</p>
                        </div>
                    </div>

                    <!-- Measurements Tab -->
                    <div v-if="activeTab === 'measurements'">
                        <p class="text-gray-500 dark:text-gray-400 text-center py-12">
                            Section en cours de développement - {{ client.measurements?.length || 0 }} mesure(s)
                        </p>
                    </div>

                    <!-- Documents Tab -->
                    <div v-if="activeTab === 'documents'">
                        <p class="text-gray-500 dark:text-gray-400 text-center py-12">
                            Section en cours de développement - {{ client.documents?.length || 0 }} document(s)
                        </p>
                    </div>

                    <!-- Assessments Tab -->
                    <div v-if="activeTab === 'assessments'">
                        <p class="text-gray-500 dark:text-gray-400 text-center py-12">
                            Section en cours de développement - {{ client.assessments?.length || 0 }} bilan(s)
                        </p>
                    </div>

                    <!-- Activity Tab -->
                    <div v-if="activeTab === 'activity'">
                        <div v-if="!client.activities || client.activities.length === 0" class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">Aucune activité enregistrée</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="activity in client.activities" :key="activity.id" class="border-l-4 border-purple-500 pl-4 py-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ activity.description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ activity.created_at }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
