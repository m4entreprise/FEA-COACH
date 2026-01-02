<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import {
    CreditCard,
    CheckCircle,
    XCircle,
    Clock,
    Banknote,
    Calendar,
    Users,
    ArrowLeft,
    ExternalLink,
    Sparkles,
} from 'lucide-vue-next';

const props = defineProps({
    hasPaymentsModule: Boolean,
    paymentsModulePrice: Number,
    stripeAccount: Object,
    stats: Object,
});

const connectStripe = () => {
    window.location.href = route('dashboard.payments.connect');
};

const openStripeDashboard = () => {
    window.location.href = route('dashboard.payments.dashboard');
};

const safeStripeAccount = computed(() => props.stripeAccount || { connected: false });
const safeStats = computed(() => props.stats || {});
</script>

<template>
    <Head title="Paiements & Réservations" />

    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('dashboard.coach')"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800 border border-slate-700 text-slate-200 hover:bg-slate-700 transition-colors"
                    >
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold flex items-center gap-2">
                            <CreditCard class="h-5 w-5 text-purple-300" />
                            Paiements & Réservations
                        </h1>
                        <p class="text-sm text-slate-400 mt-1">
                            Gérez vos services, disponibilités et réservations en ligne
                        </p>
                    </div>
                </div>
            </div>

            <!-- Module non activé -->
            <div v-if="!hasPaymentsModule" class="rounded-2xl border border-slate-800 bg-slate-900/70 shadow-xl">
                <div class="p-8 text-center space-y-6">
                    <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                        <Sparkles class="h-8 w-8 text-white" />
                    </div>
                    
                    <div>
                        <h2 class="text-2xl font-bold text-slate-50 mb-2">
                            Module Premium
                        </h2>
                        <p class="text-slate-400 max-w-2xl mx-auto">
                            Activez le module paiements pour recevoir des réservations et des paiements en ligne directement sur votre site.
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-700 bg-slate-800/50 p-6 max-w-2xl mx-auto">
                        <div class="flex items-center justify-center gap-3 mb-4">
                            <div class="text-4xl font-bold text-emerald-400">{{ paymentsModulePrice }}€</div>
                            <div class="text-left">
                                <p class="text-xs text-slate-400">par mois</p>
                                <p class="text-xs text-slate-500">HTVA</p>
                            </div>
                        </div>
                        
                        <ul class="space-y-3 text-left max-w-md mx-auto">
                            <li class="flex items-start gap-2 text-sm text-slate-300">
                                <CheckCircle class="h-4 w-4 text-emerald-400 mt-0.5 flex-shrink-0" />
                                <span>Paiements sécurisés via Stripe</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-slate-300">
                                <CheckCircle class="h-4 w-4 text-emerald-400 mt-0.5 flex-shrink-0" />
                                <span>Gestion des services et tarifs</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-slate-300">
                                <CheckCircle class="h-4 w-4 text-emerald-400 mt-0.5 flex-shrink-0" />
                                <span>Calendrier de disponibilités</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-slate-300">
                                <CheckCircle class="h-4 w-4 text-emerald-400 mt-0.5 flex-shrink-0" />
                                <span>Système de réservation en ligne</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-slate-300">
                                <CheckCircle class="h-4 w-4 text-emerald-400 mt-0.5 flex-shrink-0" />
                                <span>0% de commission plateforme</span>
                            </li>
                        </ul>
                    </div>

                    <button
                        @click="router.post(route('dashboard.payments.activate'))"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 px-8 py-3 text-sm font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600 transition-all"
                    >
                        <Sparkles class="h-4 w-4" />
                        Activer le module ({{ paymentsModulePrice }}€/mois)
                    </button>

                    <p class="text-xs text-slate-500">
                        Frais Stripe standards applicables sur chaque transaction
                    </p>
                </div>
            </div>

            <!-- Module activé mais Stripe non connecté -->
            <div v-else-if="hasPaymentsModule && !safeStripeAccount.connected" class="space-y-6">
                <div class="rounded-2xl border border-emerald-500/40 bg-emerald-950/40 p-6 shadow-xl">
                    <div class="flex items-center gap-3 mb-4">
                        <CheckCircle class="h-6 w-6 text-emerald-400" />
                        <span class="text-sm text-emerald-100">Module premium activé</span>
                    </div>

                    <div class="text-center py-8 space-y-6">
                        <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-400 flex items-center justify-center shadow-lg">
                            <CreditCard class="h-8 w-8 text-white" />
                        </div>
                        
                        <div>
                            <h3 class="text-2xl font-bold text-slate-50 mb-2">
                                Connecter votre compte Stripe
                            </h3>
                            <p class="text-slate-400 max-w-2xl mx-auto">
                                Pour recevoir les paiements de vos clients, vous devez connecter un compte Stripe.
                            </p>
                        </div>

                        <div class="max-w-md mx-auto rounded-xl border border-slate-700 bg-slate-800/50 p-6 text-left space-y-3">
                            <div class="flex items-center gap-2 text-slate-300">
                                <Clock class="h-4 w-4 text-slate-400" />
                                <span class="font-semibold text-sm">Temps estimé : 10 minutes</span>
                            </div>
                            <div class="text-sm text-slate-400">
                                <p class="font-semibold mb-2">Documents nécessaires :</p>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>Pièce d'identité</li>
                                    <li>SIRET / n° TVA</li>
                                    <li>Coordonnées bancaires (IBAN)</li>
                                </ul>
                            </div>
                        </div>

                        <button
                            @click="connectStripe"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-8 py-3 text-sm font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors"
                        >
                            <CreditCard class="h-4 w-4" />
                            Connecter mon compte Stripe
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stripe connecté -->
            <div v-else class="space-y-6">
                <!-- Statut du compte -->
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <CheckCircle v-if="safeStripeAccount.is_fully_activated" class="h-8 w-8 text-emerald-400" />
                            <XCircle v-else class="h-8 w-8 text-yellow-400" />
                            <div>
                                <h3 class="text-lg font-semibold text-slate-50">Compte Stripe</h3>
                                <p v-if="safeStripeAccount.is_fully_activated" class="text-sm text-emerald-400">
                                    Connecté et activé
                                </p>
                                <p v-else class="text-sm text-yellow-400">
                                    Vérification en cours
                                </p>
                            </div>
                        </div>
                        <button
                            @click="openStripeDashboard"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors"
                        >
                            <ExternalLink class="h-4 w-4" />
                            Voir mon dashboard Stripe
                        </button>
                    </div>

                    <div v-if="!safeStripeAccount.is_fully_activated" class="mt-4 rounded-lg border border-yellow-500/40 bg-yellow-950/40 p-4">
                        <p class="text-sm text-yellow-100">
                            Votre compte Stripe est en cours de vérification. Vous pourrez accepter des paiements une fois la vérification terminée.
                        </p>
                    </div>
                </div>

                <!-- Statistiques -->
                <div v-if="safeStripeAccount.is_fully_activated && safeStats" class="grid grid-cols-1 md:grid-cols-4 gap-5">
                    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center">
                                <Banknote class="h-5 w-5 text-white" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Total encaissé</p>
                                <p class="text-xl font-bold text-slate-50">
                                    {{ safeStats.total_revenue || '0' }}€
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-400 flex items-center justify-center">
                                <Calendar class="h-5 w-5 text-white" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Réservations</p>
                                <p class="text-xl font-bold text-slate-50">
                                    {{ safeStats.total_bookings || 0 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-400 flex items-center justify-center">
                                <Clock class="h-5 w-5 text-white" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">À venir</p>
                                <p class="text-xl font-bold text-slate-50">
                                    {{ safeStats.upcoming_bookings || 0 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-pink-500 to-pink-400 flex items-center justify-center">
                                <CheckCircle class="h-5 w-5 text-white" />
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Taux réalisation</p>
                                <p class="text-xl font-bold text-slate-50">
                                    {{ safeStats.completion_rate || 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation rapide -->
                <div v-if="safeStripeAccount.is_fully_activated" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <Link
                        :href="route('dashboard.services.index')"
                        class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl hover:border-emerald-500/60 hover:bg-slate-900/90 transition-colors"
                    >
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center shadow-lg">
                                <CreditCard class="h-5 w-5 text-white" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-50">Mes services</h3>
                                <p class="text-xs text-slate-400">
                                    Gérer les types de séances et tarifs
                                </p>
                            </div>
                        </div>
                    </Link>

                    <Link
                        :href="route('dashboard.availability.index')"
                        class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl hover:border-blue-500/60 hover:bg-slate-900/90 transition-colors"
                    >
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-400 flex items-center justify-center shadow-lg">
                                <Calendar class="h-5 w-5 text-white" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-50">Mes disponibilités</h3>
                                <p class="text-xs text-slate-400">
                                    Définir mes créneaux hebdomadaires
                                </p>
                            </div>
                        </div>
                    </Link>

                    <Link
                        :href="route('dashboard.bookings.index')"
                        class="group rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-xl hover:border-purple-500/60 hover:bg-slate-900/90 transition-colors"
                    >
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-400 flex items-center justify-center shadow-lg">
                                <Users class="h-5 w-5 text-white" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-50">Mes réservations</h3>
                                <p class="text-xs text-slate-400">
                                    Voir et gérer les réservations
                                </p>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes breathe {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-breathe {
    animation: breathe 2s ease-in-out infinite;
}
</style>
