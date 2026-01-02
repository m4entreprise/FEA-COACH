<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    requests: Array,
});

const markAsRead = (id) => {
    router.post(route('admin.contact-requests.mark-read', id), {}, {
        preserveScroll: true,
    });
};

const deleteRequest = (id, name) => {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la demande de "${name}" ?`)) {
        router.delete(route('admin.contact-requests.destroy', id), {
            preserveScroll: true,
        });
    }
};

const getStatusBadgeColor = (isRead) => {
    return isRead 
        ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
        : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
};

const isCustomServiceRequest = (message) => {
    return message.includes('services premium') || message.includes('nom de domaine') || message.includes('site web sur mesure');
};
</script>

<template>
    <Head title="Demandes de Contact" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Demandes de Contact
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ requests.length }} demande{{ requests.length > 1 ? 's' : '' }} · 
                        {{ requests.filter(r => !r.is_read).length }} non lue{{ requests.filter(r => !r.is_read).length > 1 ? 's' : '' }}
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Success Message -->
                        <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>

                        <!-- Requests List -->
                        <div v-if="requests.length > 0" class="space-y-4">
                            <div
                                v-for="request in requests"
                                :key="request.id"
                                class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900"
                                :class="{ 'border-l-4 border-l-blue-500': !request.is_read }"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span
                                                :class="getStatusBadgeColor(request.is_read)"
                                                class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
                                            >
                                                {{ request.is_read ? 'Lu' : 'Non lu' }}
                                            </span>
                                            <span
                                                v-if="isCustomServiceRequest(request.message)"
                                                class="inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                                            >
                                                Services Premium
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Contact</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ request.name }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ request.email }}</p>
                                                <p v-if="request.phone" class="text-xs text-gray-600 dark:text-gray-400">{{ request.phone }}</p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Coach concerné</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ request.coach_name }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ request.coach_email }}</p>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Date</p>
                                                <p class="text-sm text-gray-900 dark:text-gray-100">{{ request.created_at }}</p>
                                            </div>
                                        </div>

                                        <div class="rounded-md bg-gray-50 p-3 dark:bg-gray-800">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Message</p>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ request.message }}</p>
                                        </div>
                                    </div>

                                    <div class="ml-4 flex flex-col gap-2">
                                        <button
                                            v-if="!request.is_read"
                                            @click="markAsRead(request.id)"
                                            class="text-xs text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 whitespace-nowrap"
                                        >
                                            Marquer comme lu
                                        </button>
                                        <button
                                            @click="deleteRequest(request.id, request.name)"
                                            class="text-xs text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 whitespace-nowrap"
                                        >
                                            Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucune demande</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Les demandes de contact apparaîtront ici.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
