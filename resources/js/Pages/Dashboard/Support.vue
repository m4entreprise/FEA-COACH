<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    tickets: Array,
});

const mode = ref('form');
const selectedTicketId = ref(props.tickets.length ? props.tickets[0].id : null);

const hasTickets = computed(() => props.tickets.length > 0);

const selectedTicket = computed(() => {
    return props.tickets.find((t) => t.id === selectedTicketId.value) || null;
});

const createForm = useForm({
    subject: '',
    category: '',
    message: '',
});

const replyForm = useForm({
    message: '',
});

const selectTicket = (ticketId) => {
    selectedTicketId.value = ticketId;
    mode.value = 'conversation';
};

const submitNewTicket = () => {
    createForm.post(route('dashboard.support.store'), {
        preserveScroll: true,
        onSuccess: () => {
            createForm.reset('subject', 'category', 'message');
            mode.value = 'conversation';
        },
    });
};

const submitReply = () => {
    if (!selectedTicket.value) return;

    replyForm.post(route('dashboard.support.reply', selectedTicket.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            replyForm.reset('message');
        },
    });
};

const closeTicket = () => {
    if (!selectedTicket.value) return;

    router.post(route('dashboard.support.close', selectedTicket.value.id), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Support" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Support & assistance
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Posez vos questions à l'équipe FEA et suivez vos conversations.
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gradient-to-br from-slate-50 via-purple-50 to-slate-50 dark:from-slate-900 dark:via-purple-900/30 dark:to-slate-900 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash success -->
                <div
                    v-if="$page.props.flash?.success"
                    class="mb-6 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-xl p-6 text-white transform hover:scale-[1.01] transition-all duration-300 backdrop-blur-xl border border-green-400/20"
                >
                    <div class="flex items-center">
                        <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="font-semibold">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Choice: new ticket vs existing -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button
                        type="button"
                        @click="mode = 'form'"
                        class="group relative overflow-hidden rounded-2xl border border-purple-200/60 dark:border-purple-500/40 bg-white/80 dark:bg-gray-800/80 p-6 text-left shadow-lg hover:shadow-2xl transform hover:-translate-y-0.5 transition-all duration-300"
                        :class="mode === 'form' ? 'ring-2 ring-purple-500/60' : ''"
                    >
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 text-white shadow-lg">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">
                                    Ouvrir une nouvelle demande
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Expliquez votre problème, vos questions ou vos idées d'amélioration.
                                </p>
                            </div>
                        </div>
                    </button>

                    <button
                        v-if="hasTickets"
                        type="button"
                        @click="mode = 'conversation'"
                        class="group relative overflow-hidden rounded-2xl border border-blue-200/60 dark:border-blue-500/40 bg-white/80 dark:bg-gray-800/80 p-6 text-left shadow-lg hover:shadow-2xl transform hover:-translate-y-0.5 transition-all duration-300"
                        :class="mode === 'conversation' ? 'ring-2 ring-blue-500/60' : ''"
                    >
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl p-3 text-white shadow-lg">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-6l-4 3v-3H7a2 2 0 01-2-2v-1" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10h-4M15 14H7a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v4z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">
                                    Continuer une conversation
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Retrouvez vos échanges précédents avec l'équipe FEA et répondez directement.
                                </p>
                            </div>
                        </div>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    <!-- New ticket form -->
                    <div class="lg:col-span-1">
                        <div class="rounded-2xl bg-white/90 dark:bg-gray-800/90 shadow-xl border border-purple-200/60 dark:border-purple-500/40 backdrop-blur-xl p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-3 text-white shadow-lg">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        Décrire votre besoin
                                    </h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                        Plus vous êtes précis, plus nous pourrons vous aider rapidement.
                                    </p>
                                </div>
                            </div>

                            <form @submit.prevent="submitNewTicket" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Sujet
                                    </label>
                                    <input
                                        v-model="createForm.subject"
                                        type="text"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-900/70 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/40"
                                        placeholder="Ex: Problème d'accès à mon site"
                                        required
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Catégorie (optionnel)
                                    </label>
                                    <select
                                        v-model="createForm.category"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-900/70 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/40"
                                    >
                                        <option value="">Sélectionner...</option>
                                        <option value="bug">Bug / Problème technique</option>
                                        <option value="billing">Facturation / abonnement</option>
                                        <option value="coaching">Questions sur l'utilisation</option>
                                        <option value="idea">Idée d'amélioration</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Message
                                    </label>
                                    <textarea
                                        v-model="createForm.message"
                                        rows="5"
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/80 dark:bg-gray-900/70 px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/40"
                                        placeholder="Décrivez le contexte, les étapes pour reproduire le problème, ou votre question."
                                        required
                                    ></textarea>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="createForm.processing"
                                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-semibold rounded-xl shadow-lg hover:from-purple-700 hover:to-pink-700 transform hover:scale-[1.02] transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                                >
                                    <svg
                                        v-if="createForm.processing"
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                        ></path>
                                    </svg>
                                    <span>
                                        {{ createForm.processing ? 'Envoi en cours...' : 'Envoyer ma demande' }}
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Conversation & ticket list -->
                    <div class="lg:col-span-2 space-y-4">
                        <div class="rounded-2xl bg-white/90 dark:bg-gray-800/90 shadow-xl border border-blue-200/60 dark:border-blue-500/40 backdrop-blur-xl p-6 min-h-[400px] flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                        <span class="text-xl">💬</span>
                                        <span>Conversations</span>
                                    </h3>
                                    <p class="text-xs text-gray-600 dark:text-gray-400" v-if="hasTickets">
                                        Sélectionnez un ticket pour voir tout l'historique.
                                    </p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400" v-else>
                                        Vous n'avez pas encore de ticket. Créez-en un à gauche pour démarrer.
                                    </p>
                                </div>
                            </div>

                            <!-- Ticket list -->
                            <div v-if="hasTickets" class="flex gap-4 mb-4 overflow-x-auto pb-2">
                                <button
                                    v-for="ticket in props.tickets"
                                    :key="ticket.id"
                                    type="button"
                                    @click="selectTicket(ticket.id)"
                                    class="flex-shrink-0 rounded-xl px-4 py-3 text-left border text-xs sm:text-sm transition-all duration-200"
                                    :class="[
                                        selectedTicketId === ticket.id
                                            ? 'bg-blue-600 text-white border-blue-500 shadow-lg'
                                            : 'bg-white/80 dark:bg-gray-900/60 text-gray-800 dark:text-gray-100 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800',
                                    ]"
                                >
                                    <div class="font-semibold truncate max-w-[180px]">
                                        {{ ticket.subject }}
                                    </div>
                                    <div class="mt-1 flex items-center justify-between gap-2">
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                            :class="ticket.status === 'open'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                                                : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'"
                                        >
                                            {{ ticket.status === 'open' ? 'Ouvert' : 'Clôturé' }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ ticket.last_message_at || ticket.created_at }}
                                        </span>
                                    </div>
                                </button>
                            </div>

                            <!-- Conversation body -->
                            <div v-if="selectedTicket" class="flex-1 flex flex-col rounded-2xl bg-gradient-to-br from-slate-50 to-blue-50/60 dark:from-slate-900/60 dark:to-blue-900/40 border border-blue-100/70 dark:border-blue-500/30 p-4">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-gray-100">
                                            {{ selectedTicket.subject }}
                                        </h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400" v-if="selectedTicket.category">
                                            Catégorie : {{ selectedTicket.category }}
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                            :class="selectedTicket.status === 'open'
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                                                : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'"
                                        >
                                            {{ selectedTicket.status === 'open' ? 'Ouvert' : 'Clôturé' }}
                                        </span>
                                        <button
                                            v-if="selectedTicket.status === 'open'"
                                            type="button"
                                            @click="closeTicket"
                                            class="inline-flex items-center px-3 py-1.5 rounded-full border border-gray-300/80 dark:border-gray-600/80 bg-white/80 dark:bg-gray-900/70 text-[11px] text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition"
                                        >
                                            Clôturer le ticket
                                        </button>
                                    </div>
                                </div>

                                <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                                    <div
                                        v-for="message in selectedTicket.messages"
                                        :key="message.id"
                                        class="flex"
                                        :class="message.is_from_admin ? 'justify-start' : 'justify-end'"
                                    >
                                        <div
                                            class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm shadow-md"
                                            :class="message.is_from_admin
                                                ? 'bg-white/90 text-gray-900 dark:bg-gray-900/90 dark:text-gray-100 border border-blue-100/70 dark:border-blue-500/40'
                                                : 'bg-gradient-to-r from-purple-600 to-pink-600 text-white'"
                                        >
                                            <div class="text-xs font-semibold mb-1 opacity-80">
                                                {{ message.is_from_admin ? 'Support FEA' : 'Vous' }}
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

                                <div class="mt-4 border-t border-blue-100/70 dark:border-blue-500/30 pt-3">
                                    <form @submit.prevent="submitReply" class="flex items-end gap-2">
                                        <textarea
                                            v-model="replyForm.message"
                                            rows="2"
                                            class="flex-1 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/90 dark:bg-gray-900/80 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40"
                                            :placeholder="selectedTicket.status === 'open'
                                                ? 'Écrivez votre réponse au support...'
                                                : 'Ticket clôturé - vous pouvez encore envoyer un message pour le rouvrir.'"
                                        ></textarea>
                                        <button
                                            type="submit"
                                            :disabled="replyForm.processing || !replyForm.message"
                                            class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 text-white text-sm font-semibold shadow-lg hover:from-blue-700 hover:to-cyan-700 transform hover:scale-[1.02] transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                                        >
                                            <svg
                                                v-if="replyForm.processing"
                                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                ></circle>
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                                ></path>
                                            </svg>
                                            <span>
                                                {{ replyForm.processing ? 'Envoi...' : 'Envoyer' }}
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div v-else class="flex-1 flex items-center justify-center text-center text-sm text-gray-500 dark:text-gray-400">
                                <div>
                                    <p class="mb-2 font-medium">Aucune conversation sélectionnée</p>
                                    <p class="text-xs">Créez une demande à gauche ou cliquez sur un ticket pour afficher les messages.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
