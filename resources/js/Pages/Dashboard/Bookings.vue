<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { CalendarIcon, ClockIcon, UserIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    bookings: Object,
    filter: String,
    stats: Object,
});

const getStatusBadge = (status) => {
    const badges = {
        pending: { color: 'bg-yellow-100 text-yellow-800', text: 'En attente' },
        confirmed: { color: 'bg-green-100 text-green-800', text: 'Confirmée' },
        completed: { color: 'bg-blue-100 text-blue-800', text: 'Terminée' },
        cancelled: { color: 'bg-red-100 text-red-800', text: 'Annulée' },
        no_show: { color: 'bg-gray-100 text-gray-800', text: 'Absent' },
    };
    return badges[status] || badges.pending;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatTime = (time) => {
    return time.substring(0, 5);
};
</script>

<template>
    <Head title="Réservations" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Réservations
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Statistiques -->
                <div v-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-600">Total réservations</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_bookings }}</p>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-600">Ce mois</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.this_month_bookings }}</p>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-600">À venir</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.upcoming_bookings }}</p>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <p class="text-sm text-gray-600">Taux réalisation</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.completion_rate }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex space-x-4">
                            <Link
                                :href="route('dashboard.bookings.index', { filter: 'upcoming' })"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium',
                                    filter === 'upcoming'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                À venir
                            </Link>
                            <Link
                                :href="route('dashboard.bookings.index', { filter: 'past' })"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium',
                                    filter === 'past'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Passées
                            </Link>
                            <Link
                                :href="route('dashboard.bookings.index', { filter: 'cancelled' })"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm font-medium',
                                    filter === 'cancelled'
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                ]"
                            >
                                Annulées
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Liste des réservations -->
                <div v-if="bookings.data.length === 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        Aucune réservation pour le moment.
                    </div>
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="booking in bookings.data"
                        :key="booking.id"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-3">
                                        <CalendarIcon class="h-5 w-5 text-gray-400 mr-2" />
                                        <span class="font-semibold text-gray-900">
                                            {{ formatDate(booking.booking_date) }}
                                        </span>
                                        <span class="mx-2 text-gray-400">•</span>
                                        <ClockIcon class="h-5 w-5 text-gray-400 mr-2" />
                                        <span class="text-gray-700">
                                            {{ formatTime(booking.start_time) }} - {{ formatTime(booking.end_time) }}
                                        </span>
                                        <span
                                            :class="['ml-3 px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusBadge(booking.status).color]"
                                        >
                                            {{ getStatusBadge(booking.status).text }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 mb-1">
                                                {{ booking.service_type.name }}
                                            </p>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <UserIcon class="h-4 w-4 mr-1" />
                                                {{ booking.client_full_name }}
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <span class="mr-1">📧</span>
                                                {{ booking.client_email }}
                                            </div>
                                            <div v-if="booking.client_phone" class="flex items-center text-sm text-gray-600">
                                                <span class="mr-1">📞</span>
                                                {{ booking.client_phone }}
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex items-center mb-1">
                                                <span class="text-sm font-medium text-gray-700">Paiement:</span>
                                                <span class="ml-2 text-lg font-bold text-gray-900">{{ booking.formatted_amount }}</span>
                                                <CheckCircleIcon
                                                    v-if="booking.payment_status === 'succeeded'"
                                                    class="h-5 w-5 text-green-500 ml-2"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="booking.client_notes" class="bg-gray-50 rounded p-3 text-sm text-gray-700 mb-3">
                                        <strong>Note du client:</strong> {{ booking.client_notes }}
                                    </div>

                                    <div v-if="booking.coach_notes" class="bg-blue-50 rounded p-3 text-sm text-gray-700">
                                        <strong>Vos notes:</strong> {{ booking.coach_notes }}
                                    </div>
                                </div>

                                <div class="ml-4">
                                    <Link
                                        :href="route('dashboard.bookings.show', booking.id)"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
                                    >
                                        Détails
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.links && bookings.links.length > 3" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-center space-x-2">
                            <Link
                                v-for="(link, index) in bookings.links"
                                :key="index"
                                :href="link.url"
                                :class="[
                                    'px-4 py-2 rounded-md text-sm',
                                    link.active
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
