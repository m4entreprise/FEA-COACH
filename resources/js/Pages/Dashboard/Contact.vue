<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    messages: Array,
});

const markAsRead = (message) => {
    if (message.is_read) return;

    router.patch(route('dashboard.contact.read', message.id), {
        preserveScroll: true,
    });
};

const deleteMessage = (message) => {
    if (confirm(`Supprimer ce message de ${message.name} ?`)) {
        router.delete(route('dashboard.contact.destroy', message.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Messages de contact" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Messages de contact
            </h2>
        </template>

        <div class="py-12 bg-gradient-to-br from-slate-50 via-cyan-50 to-slate-50 dark:from-slate-900 dark:via-cyan-900/20 dark:to-slate-900 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash success -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-xl p-6 text-white transform hover:scale-[1.01] transition-all duration-300 backdrop-blur-xl border border-green-400/20">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="font-semibold">✨ {{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Intro -->
                <div class="mb-8 rounded-2xl bg-gradient-to-br from-white to-cyan-50 dark:from-gray-800 dark:to-cyan-900/20 shadow-xl border border-cyan-200/50 dark:border-cyan-500/30 backdrop-blur-xl p-8">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-4 shadow-lg">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-3">📧 Contacts depuis votre site</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                Les personnes qui remplissent le formulaire de contact sur votre site public apparaîtront ici. Vous pouvez marquer les messages comme lus ou les supprimer une fois traités.
                            </p>
                            <div class="mt-4 inline-flex items-center px-4 py-2 rounded-xl bg-cyan-100 dark:bg-cyan-900/30 border border-cyan-200/30 dark:border-cyan-500/20">
                                <svg class="h-5 w-5 text-cyan-600 dark:text-cyan-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm font-semibold text-cyan-700 dark:text-cyan-300">📊 {{ messages.length }} message{{ messages.length > 1 ? 's' : '' }} reçu{{ messages.length > 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages list -->
                <div v-if="messages.length" class="space-y-6">
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        class="rounded-2xl bg-gradient-to-br from-white to-cyan-50/30 dark:from-gray-800 dark:to-cyan-900/10 p-6 shadow-xl hover:shadow-2xl transform hover:scale-[1.01] transition-all duration-300 border border-cyan-200/50 dark:border-cyan-500/30 backdrop-blur-xl"
                        :class="{ 'opacity-60': message.is_read }"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="flex-shrink-0 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-3 shadow-md">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        👤 {{ message.name }}
                                    </h4>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <a :href="`mailto:${message.email}`" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold shadow-md hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200">
                                            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            {{ message.email }}
                                        </a>
                                        <span v-if="message.phone" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-semibold shadow-md">
                                            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            {{ message.phone }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex flex-col items-end gap-2">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-bold shadow-md"
                                    :class="message.is_read
                                        ? 'bg-gradient-to-r from-gray-400 to-gray-500 text-white'
                                        : 'bg-gradient-to-r from-blue-500 to-cyan-600 text-white animate-pulse'"
                                >
                                    {{ message.is_read ? '✅ Lu' : '🆕 Nouveau' }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 text-xs font-semibold text-cyan-700 dark:text-cyan-300">
                                    📅 {{ message.created_at }}
                                </span>
                            </div>
                        </div>

                        <div class="rounded-xl bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 p-4 mb-4 border border-blue-200/30 dark:border-blue-500/20">
                            <p class="whitespace-pre-line text-sm text-gray-800 dark:text-gray-200 leading-relaxed">
                                💬 {{ message.message }}
                            </p>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button
                                v-if="!message.is_read"
                                type="button"
                                @click="markAsRead(message)"
                                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-lg hover:from-blue-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Marquer comme lu
                            </button>
                            <button
                                type="button"
                                @click="deleteMessage(message)"
                                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 border border-transparent rounded-xl font-semibold text-sm text-white shadow-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-else class="rounded-2xl bg-gradient-to-br from-white to-cyan-50 dark:from-gray-800 dark:to-cyan-900/20 shadow-xl border border-cyan-200/50 dark:border-cyan-500/30 backdrop-blur-xl p-12 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-4 shadow-lg">
                            <svg class="h-16 w-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">📨 Aucun message pour le moment</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Dès que quelqu'un remplira le formulaire de contact sur votre site, son message apparaîtra ici.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
