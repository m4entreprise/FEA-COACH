<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { toast } from 'vue-sonner';
import {
    Users,
    Calendar,
    Clock,
    Euro,
    Check,
    X,
    Mail,
    Phone,
    Filter,
    CheckCircle,
    XCircle,
    Trash2,
} from 'lucide-vue-next';

const props = defineProps({
    bookings: Array,
    stats: Object,
});

const selectedFilter = ref('all');

const dashboardBackUrl = computed(() => {
    if (typeof window === 'undefined') return route('dashboard');
    const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
    return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
    router.visit(dashboardBackUrl.value);
};

const filteredBookings = computed(() => {
    if (!props.bookings) return [];
    
    if (selectedFilter.value === 'all') {
        return props.bookings;
    }
    
    return props.bookings.filter(booking => booking.status === selectedFilter.value);
});

const getStatusConfig = (status) => {
    const configs = {
        pending: {
            label: 'En attente',
            class: 'bg-yellow-500/20 text-yellow-100 border-yellow-500/40',
            icon: Clock,
        },
        confirmed: {
            label: 'Confirmé',
            class: 'bg-emerald-500/20 text-emerald-100 border-emerald-500/40',
            icon: CheckCircle,
        },
        completed: {
            label: 'Terminé',
            class: 'bg-blue-500/20 text-blue-100 border-blue-500/40',
            icon: Check,
        },
        cancelled: {
            label: 'Annulé',
            class: 'bg-rose-500/20 text-rose-100 border-rose-500/40',
            icon: XCircle,
        },
    };
    
    return configs[status] || configs.pending;
};

const markAsCompleted = (booking) => {
    if (confirm('Marquer cette réservation comme terminée ?')) {
        router.post(route('dashboard.bookings.complete', booking.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Réservation marquée comme terminée');
            },
            onError: () => {
                toast.error('Erreur lors de la mise à jour');
            },
        });
    }
};

const cancelBooking = (booking) => {
    if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
        return;
    }

    const reason = window.prompt('Motif d’annulation (optionnel)') ?? '';

    router.post(route('dashboard.bookings.cancel', booking.id), { reason }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Réservation annulée');
        },
        onError: () => {
            toast.error('Erreur lors de l\'annulation');
        },
    });
};

const deleteBooking = (booking) => {
    if (!confirm('Supprimer définitivement cette réservation ?')) {
        return;
    }

    router.delete(route('dashboard.bookings.destroy', booking.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Réservation supprimée');
        },
        onError: () => {
            toast.error('Erreur lors de la suppression');
        },
    });
};

const formatPaymentDate = (paidAt) => {
    if (!paidAt) {
        return 'Date de paiement inconnue';
    }
    return new Intl.DateTimeFormat('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(paidAt));
};

const formatPaymentTime = (paidAt) => {
    if (!paidAt) {
        return '--:--';
    }
    return new Intl.DateTimeFormat('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(paidAt));
};

const getClientName = (booking) => {
    if (booking.client_full_name) {
        return booking.client_full_name;
    }
    const parts = [booking.client_first_name, booking.client_last_name].filter(Boolean);
    if (parts.length) {
        return parts.join(' ');
    }
    if (booking.client && booking.client.full_name) {
        return booking.client.full_name;
    }
    return 'Client';
};
</script>

<template>
    <Head title="Mes Réservations" />

    <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
        <!-- Top bar -->
        <header
            class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl"
        >
            <div class="flex items-center gap-3">
                <div class="flex flex-col">
                    <p class="text-xs uppercase tracking-wide text-slate-400">Panel coach</p>
                    <h1 class="text-base md:text-lg font-semibold flex items-center gap-2">
                        <span>Mes réservations</span>
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                    type="button"
                    @click="goBack"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs font-medium text-slate-100 hover:border-slate-500 hover:bg-slate-800"
                >
                    <span class="text-xs">←</span>
                    <span>Retour panel</span>
                </button>
            </div>
        </header>

        <!-- Main content -->
        <main
            class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8"
        >
            <div class="max-w-6xl mx-auto space-y-6">
                <!-- Header -->
                <div>
                    <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                        <Users class="h-5 w-5 text-purple-300" />
                        Mes Réservations
                    </h2>
                    <p class="text-sm text-slate-400 mt-1">
                        Gérez vos réservations et séances
                    </p>
                </div>

            <!-- Stats -->
            <div v-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 shadow-xl">
                    <p class="text-xs text-slate-400 mb-1">Total</p>
                    <p class="text-2xl font-bold text-slate-50">{{ stats.total_bookings || 0 }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 shadow-xl">
                    <p class="text-xs text-slate-400 mb-1">À venir</p>
                    <p class="text-2xl font-bold text-blue-400">{{ stats.upcoming_bookings || 0 }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 shadow-xl">
                    <p class="text-xs text-slate-400 mb-1">Terminées</p>
                    <p class="text-2xl font-bold text-emerald-400">{{ stats.completed_bookings || 0 }}</p>
                </div>
                <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 shadow-xl">
                    <p class="text-xs text-slate-400 mb-1">Total encaissé</p>
                    <p class="text-2xl font-bold text-purple-400">{{ stats.total_revenue || 0 }}€</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-2">
                <button
                    @click="selectedFilter = 'all'"
                    :class="selectedFilter === 'all' ? 'bg-purple-600 text-white' : 'bg-slate-800 text-slate-300 border border-slate-700'"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium hover:bg-purple-700 transition-colors"
                >
                    <Filter class="h-3.5 w-3.5" />
                    Toutes
                </button>
                <button
                    @click="selectedFilter = 'pending'"
                    :class="selectedFilter === 'pending' ? 'bg-yellow-600 text-white' : 'bg-slate-800 text-slate-300 border border-slate-700'"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium hover:bg-yellow-700 transition-colors"
                >
                    En attente
                </button>
                <button
                    @click="selectedFilter = 'confirmed'"
                    :class="selectedFilter === 'confirmed' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300 border border-slate-700'"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium hover:bg-emerald-700 transition-colors"
                >
                    Confirmées
                </button>
                <button
                    @click="selectedFilter = 'completed'"
                    :class="selectedFilter === 'completed' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-300 border border-slate-700'"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium hover:bg-blue-700 transition-colors"
                >
                    Terminées
                </button>
                <button
                    @click="selectedFilter = 'cancelled'"
                    :class="selectedFilter === 'cancelled' ? 'bg-rose-600 text-white' : 'bg-slate-800 text-slate-300 border border-slate-700'"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium hover:bg-rose-700 transition-colors"
                >
                    Annulées
                </button>
            </div>

            <!-- Bookings list -->
            <div v-if="filteredBookings.length > 0" class="space-y-4">
                <div
                    v-for="booking in filteredBookings"
                    :key="booking.id"
                    class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl"
                >
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex-1 space-y-4">
                            <!-- Status & Service -->
                            <div class="flex items-start gap-3">
                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-400 flex items-center justify-center shadow-lg flex-shrink-0">
                                    <component :is="getStatusConfig(booking.status).icon" class="h-5 w-5 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-base font-semibold text-slate-50">{{ booking.service_name }}</h3>
                                        <span
                                            :class="getStatusConfig(booking.status).class"
                                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium border"
                                        >
                                            {{ getStatusConfig(booking.status).label }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-400">
                                        <div class="flex items-center gap-1.5">
                                            <Calendar class="h-3.5 w-3.5" />
                                            <span>{{ formatPaymentDate(booking.paid_at || booking.created_at) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <Clock class="h-3.5 w-3.5" />
                                            <span>{{ formatPaymentTime(booking.paid_at || booking.created_at) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-emerald-400 font-medium">
                                            <Euro class="h-3.5 w-3.5" />
                                            <span>{{ booking.amount }}€</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Client info -->
                            <div class="rounded-lg border border-slate-700 bg-slate-800/50 p-4">
                                <p class="text-xs text-slate-400 mb-2">Infos client</p>
                                <div class="space-y-2 text-sm text-slate-200">
                                    <p class="font-semibold">{{ getClientName(booking) }}</p>
                                    <div class="flex flex-wrap gap-3 text-slate-400">
                                        <div v-if="booking.client_email" class="flex items-center gap-1.5">
                                            <Mail class="h-3.5 w-3.5" />
                                            <a :href="'mailto:' + booking.client_email" class="hover:text-slate-200">
                                                {{ booking.client_email }}
                                            </a>
                                        </div>
                                        <div v-if="booking.client_phone" class="flex items-center gap-1.5">
                                            <Phone class="h-3.5 w-3.5" />
                                            <a :href="'tel:' + booking.client_phone" class="hover:text-slate-200">
                                                {{ booking.client_phone }}
                                            </a>
                                        </div>
                                    </div>
                                    <p v-if="booking.client_notes" class="text-xs text-slate-400 mt-2 italic">
                                        Note : {{ booking.client_notes }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex lg:flex-col gap-2">
                            <button
                                v-if="booking.status === 'confirmed'"
                                @click="markAsCompleted(booking)"
                                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-medium text-white hover:bg-emerald-700 transition-colors"
                            >
                                <Check class="h-3.5 w-3.5" />
                                Marquer terminée
                            </button>
                            <button
                                v-if="booking.status !== 'cancelled' && booking.status !== 'completed'"
                                @click="cancelBooking(booking)"
                                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-rose-950/40 px-3 py-2 text-xs font-medium text-rose-200 border border-rose-500/40 hover:bg-rose-950/60 transition-colors"
                            >
                                <X class="h-3.5 w-3.5" />
                                Annuler
                            </button>
                            <button
                                @click="deleteBooking(booking)"
                                class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-800 px-3 py-2 text-xs font-medium text-slate-200 border border-slate-700 hover:bg-slate-700 transition-colors"
                            >
                                <Trash2 class="h-3.5 w-3.5" />
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="rounded-2xl border border-slate-800 bg-slate-900/70 p-12 shadow-xl text-center">
                <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-600 flex items-center justify-center mb-4">
                    <Users class="h-8 w-8 text-slate-300" />
                </div>
                <h3 class="text-lg font-semibold text-slate-50 mb-2">Aucune réservation</h3>
                <p class="text-sm text-slate-400 max-w-md mx-auto">
                    Les réservations de vos clients apparaîtront ici.
                </p>
            </div>
            </div>
        </main>
    </div>
</template>
