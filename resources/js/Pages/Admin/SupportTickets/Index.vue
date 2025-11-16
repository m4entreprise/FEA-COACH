<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    tickets: Array,
    stats: Object,
    filters: Object,
});

const selectedTicketId = ref(props.tickets.length ? props.tickets[0].id : null);

const selectedTicket = computed(() => {
    return props.tickets.find((t) => t.id === selectedTicketId.value) || null;
});

const replyForm = useForm({
    message: '',
});

const selectTicket = (ticketId) => {
    selectedTicketId.value = ticketId;
};

const submitReply = () => {
    if (!selectedTicket.value) return;

    replyForm.post(route('admin.support-tickets.reply', selectedTicket.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            replyForm.reset('message');
        },
    });
};

const updateStatus = (status) => {
    if (!selectedTicket.value) return;

    router.patch(route('admin.support-tickets.status', selectedTicket.value.id), { status }, {
        preserveScroll: true,
    });
};

const filterByStatus = (status) => {
    if (status) {
        router.get(route('admin.support-tickets.index', { status }), {}, {
            preserveScroll: true,
            preserveState: true,
        });
    } else {
        router.get(route('admin.support-tickets.index'), {}, {
            preserveScroll: true,
            preserveState: true,
        });
    }
};
</script>

<template>
    <Head title="Tickets de support" />

    <AdminLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span>Tickets de support</span>
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Gérez les demandes de support envoyées par les coachs.
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    type="button"
                                    @click="filterByStatus(null)"
                                    class="px-3 py-1.5 text-xs rounded-full border"
                                    :class="!props.filters.status
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600'"
                                >
                                    Tous ({{ props.stats.total }})
                                </button>
                                <button
                                    type="button"
                                    @click="filterByStatus('open')"
                                    class="px-3 py-1.5 text-xs rounded-full border"
                                    :class="props.filters.status === 'open'
                                        ? 'bg-green-600 text-white border-green-600'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600'"
                                >
                                    Ouverts ({{ props.stats.open }})
                                </button>
                                <button
                                    type="button"
                                    @click="filterByStatus('closed')"
                                    class="px-3 py-1.5 text-xs rounded-full border"
                                    :class="props.filters.status === 'closed'
                                        ? 'bg-gray-800 text-white border-gray-800'
                                        : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600'"
                                >
                                    Clôturés ({{ props.stats.closed }})
                                </button>
                            </div>
                        </div>

                        <div
                            v-if="$page.props.flash?.success"
                            class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 px-4 py-3 text-sm text-green-800 dark:text-green-200"
                        >
                            {{ $page.props.flash.success }}
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Tickets list -->
                            <div class="lg:col-span-1 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900/40 max-h-[600px] overflow-y-auto">
                                <div
                                    v-if="props.tickets.length === 0"
                                    class="py-10 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Aucun ticket pour le moment.
                                </div>

                                <div v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <button
                                        v-for="ticket in props.tickets"
                                        :key="ticket.id"
                                        type="button"
                                        @click="selectTicket(ticket.id)"
                                        class="w-full text-left px-4 py-3 flex flex-col gap-1 hover:bg-gray-100/80 dark:hover:bg-gray-800/80 transition"
                                        :class="selectedTicketId === ticket.id ? 'bg-blue-50 dark:bg-blue-900/30' : ''"
                                    >
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                                {{ ticket.subject }}
                                            </p>
                                            <span
                                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                                :class="ticket.status === 'open'
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                                                    : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'"
                                            >
                                                {{ ticket.status === 'open' ? 'Ouvert' : 'Clôturé' }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center justify-between gap-2">
                                            <span class="truncate">
                                                {{ ticket.user.name }} · {{ ticket.user.email }}
                                            </span>
                                            <span class="whitespace-nowrap">
                                                {{ ticket.last_message_at || ticket.created_at }}
                                            </span>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Conversation -->
                            <div class="lg:col-span-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900/40 p-4 flex flex-col min-h-[400px] max-h-[600px]">
                                <div v-if="selectedTicket" class="flex flex-col flex-1">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                                {{ selectedTicket.subject }}
                                            </h3>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                {{ selectedTicket.user.name }} · {{ selectedTicket.user.email }}
                                            </p>
                                            <p v-if="selectedTicket.category" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Catégorie : {{ selectedTicket.category }}
                                            </p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-semibold"
                                                :class="selectedTicket.status === 'open'
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                                                    : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'"
                                            >
                                                {{ selectedTicket.status === 'open' ? 'Ouvert' : 'Clôturé' }}
                                            </span>
                                            <div class="flex gap-2">
                                                <button
                                                    type="button"
                                                    @click="updateStatus('open')"
                                                    class="px-3 py-1 text-[11px] rounded-full border border-green-500/60 text-green-700 dark:text-green-300 bg-green-50/60 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 transition"
                                                >
                                                    Marquer comme ouvert
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="updateStatus('closed')"
                                                    class="px-3 py-1 text-[11px] rounded-full border border-gray-500/60 text-gray-700 dark:text-gray-200 bg-gray-100/60 dark:bg-gray-800/70 hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                                                >
                                                    Clôturer
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex-1 overflow-y-auto space-y-3 pr-1 mb-3">
                                        <div
                                            v-for="message in selectedTicket.messages"
                                            :key="message.id"
                                            class="flex"
                                            :class="message.is_from_admin ? 'justify-end' : 'justify-start'"
                                        >
                                            <div
                                                class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm shadow"
                                                :class="message.is_from_admin
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700'"
                                            >
                                                <div class="text-xs font-semibold mb-1 opacity-80">
                                                    {{ message.is_from_admin ? 'Support FEA' : selectedTicket.user.name }}
                                                </div>
                                                <p class="whitespace-pre-line leading-relaxed">
                                                    {{ message.message }}
                                                </p>
                                                <div class="mt-1 text-[10px] opacity-70 text-right">
                                                    {{ message.created_at }}
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="selectedTicket.messages.length === 0" class="text-center text-xs text-gray-500 dark:text-gray-400 mt-8">
                                            Aucun message pour le moment.
                                        </div>
                                    </div>

                                    <form @submit.prevent="submitReply" class="border-t border-gray-200 dark:border-gray-700 pt-3 flex items-end gap-2">
                                        <textarea
                                            v-model="replyForm.message"
                                            rows="2"
                                            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                            placeholder="Répondre au coach..."
                                        ></textarea>
                                        <button
                                            type="submit"
                                            :disabled="replyForm.processing || !replyForm.message"
                                            class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold shadow hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            <span v-if="replyForm.processing">Envoi...</span>
                                            <span v-else>Envoyer</span>
                                        </button>
                                    </form>
                                </div>

                                <div v-else class="flex-1 flex items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div>
                                        <p class="mb-2 font-medium">Aucun ticket sélectionné</p>
                                        <p class="text-xs">Sélectionnez un ticket dans la liste à gauche pour voir la conversation.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
