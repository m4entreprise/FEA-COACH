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

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash success -->
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

                <!-- Intro -->
                <div class="mb-6 rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Contacts depuis votre site</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Les personnes qui remplissent le formulaire de contact sur votre site public apparaîtront ici. Vous pouvez marquer les messages comme lus ou les supprimer une fois traités.
                    </p>
                </div>

                <!-- Messages list -->
                <div v-if="messages.length" class="space-y-4">
                    <div
                        v-for="message in messages"
                        :key="message.id"
                        class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800 border border-gray-100 dark:border-gray-700"
                        :class="{ 'opacity-70': message.is_read }"
                    >
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                    {{ message.name }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <a :href="`mailto:${message.email}`" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                                        {{ message.email }}
                                    </a>
                                    <span v-if="message.phone" class="ml-2 text-gray-500 dark:text-gray-400">
                                        • {{ message.phone }}
                                    </span>
                                </p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="message.is_read
                                        ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                        : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100'"
                                >
                                    {{ message.is_read ? 'Lu' : 'Nouveau' }}
                                </span>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ message.created_at }}
                                </p>
                            </div>
                        </div>

                        <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-300 mb-4">
                            {{ message.message }}
                        </p>

                        <div class="flex gap-2 justify-end">
                            <PrimaryButton
                                v-if="!message.is_read"
                                type="button"
                                @click="markAsRead(message)"
                                class="bg-blue-600 hover:bg-blue-500 focus:ring-blue-600"
                            >
                                Marquer comme lu
                            </PrimaryButton>
                            <button
                                type="button"
                                @click="deleteMessage(message)"
                                class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2"
                            >
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-else class="rounded-lg bg-white p-12 text-center shadow-sm dark:bg-gray-800">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-9 4v8" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucun message pour le moment</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Dès que quelqu'un remplira le formulaire de contact sur votre site, son message apparaîtra ici.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
