<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { CreditCard, Calendar, Check, ExternalLink, AlertCircle, Sparkles, Crown } from 'lucide-vue-next';

const props = defineProps({
  subscription: Object,
  user: Object,
  planInfo: Object,
});

const subscriptionEndDate = computed(() => {
  if (!props.subscription.trial_ends_at) return null;
  return new Date(props.subscription.trial_ends_at).toLocaleDateString(
    'fr-FR',
    {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    },
  );
});

const currentPeriodEndDate = computed(() => {
  if (!props.subscription.current_period_end) return null;
  return new Date(props.subscription.current_period_end).toLocaleDateString(
    'fr-FR',
    {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    },
  );
});

const statusBadgeClass = computed(() => {
  const status = props.subscription.status;
  if (props.subscription.is_on_trial) {
    return 'border-blue-500/40 bg-blue-500/10 text-blue-200';
  }
  if (status === 'active') {
    return 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200';
  }
  return 'border-slate-700 bg-slate-800 text-slate-300';
});

const statusLabel = computed(() => {
  if (props.subscription.is_on_trial) {
    return 'Période d\'essai';
  }
  if (props.subscription.status === 'active') {
    return 'Actif';
  }
  return 'Inactif';
});

const handleSubscribe = () => {
  router.post(route('dashboard.subscription.checkout'));
};

const handleManageSubscription = () => {
  router.post(route('dashboard.subscription.portal'));
};
</script>

<template>
  <Head title="Abonnement " />

  <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
    <!-- Top bar -->
    <header
      class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl"
    >
      <div class="flex items-center gap-3">
        <div class="flex flex-col">
          <p class="text-xs uppercase tracking-wide text-slate-400">
            Panel coach
          </p>
          <h1 class="text-base md:text-lg font-semibold flex items-center gap-2">
            <span>Abonnement</span>
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <a
          :href="route('dashboard')"
          class="inline-flex items-center gap-2 rounded-full border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs font-medium text-slate-100 hover:border-slate-500 hover:bg-slate-800"
        >
          <span class="text-xs">←</span>
          <span>Retour panel</span>
        </a>
      </div>
    </header>

    <!-- Main content -->
    <main
      class="flex-1 overflow-y-auto bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-4 md:px-6 py-6 md:py-8"
    >
      <div class="max-w-6xl mx-auto space-y-6">
        <!-- Header -->
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
          <div>
            <h2 class="text-lg font-semibold">Gestion de l'abonnement</h2>
            <p class="text-sm text-slate-400">
              Suivez votre période d'essai, gérez votre formule et vos paiements.
            </p>
          </div>
        </section>

        <section
          class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl space-y-2"
        >
          <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-300">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-breathe"></span>
              <span>Abonnement {{ subscription.status === 'active' ? 'actif' : subscription.is_on_trial ? 'en période d\'essai' : 'inactif' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span
                class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-semibold"
                :class="statusBadgeClass"
              >
                {{ statusLabel }}
              </span>
            </div>
          </div>
        </section>

        <!-- Subscription status -->
        <section class="space-y-4">
          <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
            <div class="flex items-start gap-4 mb-5">
              <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-400 flex items-center justify-center shadow-lg flex-shrink-0">
                <CreditCard class="h-4 w-4" />
              </div>
              <div class="flex-1">
                <div class="flex flex-wrap items-start justify-between gap-4">
                  <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wide text-slate-500">
                      Statut actuel
                    </p>
                    <h3 class="text-sm md:text-base font-semibold text-slate-50">
                      {{ subscription.is_on_trial ? 'Période d\'essai active' : subscription.status === 'active' ? 'Abonnement actif' : 'Abonnement inactif' }}
                    </h3>
                  </div>
                </div>
              </div>
            </div>

            <!-- Trial info -->
            <div
              v-if="subscription.is_on_trial"
              class="rounded-xl border border-blue-500/40 bg-blue-950/40 p-4 mb-4"
            >
              <div class="flex items-start gap-3">
                <Sparkles class="h-5 w-5 text-blue-300 flex-shrink-0" />
                <div class="flex-1 space-y-2">
                  <p class="text-sm font-semibold text-blue-100">
                    Profitez de votre essai gratuit
                  </p>
                  <p class="text-xs text-blue-200">
                    Il vous reste <span class="font-bold">{{ subscription.trial_days_left }} jour{{ subscription.trial_days_left > 1 ? 's' : '' }}</span> pour tester toutes les fonctionnalités.
                  </p>
                  <div class="flex items-center gap-2 text-xs text-blue-300">
                    <Calendar class="h-3 w-3" />
                    <span>Expire le {{ subscriptionEndDate }}</span>
                  </div>
                  <div v-if="subscription.cancel_at_period_end" class="flex items-center gap-2 text-xs text-amber-300 mt-2 pt-2 border-t border-blue-500/30">
                    <AlertCircle class="h-3 w-3" />
                    <span>Abonnement annulé - l'accès restera actif jusqu'à la fin de la période d'essai</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Active subscription info -->
            <div
              v-else-if="subscription.status === 'active'"
              class="rounded-xl border border-emerald-500/40 bg-emerald-950/40 p-4 mb-4"
            >
              <div class="flex items-start gap-3">
                <Check class="h-5 w-5 text-emerald-300 flex-shrink-0" />
                <div class="flex-1 space-y-2">
                  <p class="text-sm font-semibold text-emerald-100">
                    Abonnement actif
                  </p>
                  <p class="text-xs text-emerald-200">
                    Vous avez accès à toutes les fonctionnalités de la plateforme.
                  </p>
                  <div v-if="currentPeriodEndDate" class="flex items-center gap-2 text-xs text-emerald-300">
                    <Calendar class="h-3 w-3" />
                    <span>{{ subscription.cancel_at_period_end ? 'Se termine' : 'Renouvellement' }} le {{ currentPeriodEndDate }}</span>
                  </div>
                  <div v-if="subscription.cancel_at_period_end" class="flex items-center gap-2 text-xs text-amber-300 mt-2">
                    <AlertCircle class="h-3 w-3" />
                    <span>Annulation programmée - l'accès restera actif jusqu'à la fin de la période</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Inactive info -->
            <div
              v-else
              class="rounded-xl border border-slate-700 bg-slate-950/70 p-4 mb-4"
            >
              <div class="flex items-start gap-3">
                <AlertCircle class="h-5 w-5 text-slate-400 flex-shrink-0" />
                <div class="flex-1 space-y-2">
                  <p class="text-sm font-semibold text-slate-200">
                    Abonnement inactif
                  </p>
                  <p class="text-xs text-slate-300">
                    Souscrivez pour accéder à toutes les fonctionnalités.
                  </p>
                </div>
              </div>
            </div>

            <!-- Account info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs mb-4">
              <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-3">
                <p class="text-slate-400 mb-1 flex items-center gap-1">
                  <span>Nom</span>
                </p>
                <p class="text-slate-50 font-semibold">{{ user.name }}</p>
              </div>
              <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-3">
                <p class="text-slate-400 mb-1">Email de facturation</p>
                <p class="text-slate-50 font-semibold truncate">
                  {{ user.email }}
                </p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-2 text-xs">
              <button
                v-if="subscription.is_on_trial && !subscription.cancel_at_period_end"
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
                @click="handleSubscribe"
              >
                <Crown class="h-3.5 w-3.5" />
                S'abonner maintenant
              </button>
              <button
                v-else-if="subscription.is_on_trial && subscription.cancel_at_period_end"
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full border border-amber-500/50 bg-amber-500/10 px-4 py-2 text-amber-200 hover:bg-amber-500/20"
                @click="handleManageSubscription"
              >
                <ExternalLink class="h-3.5 w-3.5" />
                Gérer l'annulation sur Lemon Squeezy
              </button>
              <button
                v-else-if="subscription.status === 'active' && !subscription.cancel_at_period_end"
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-slate-200 hover:bg-slate-700"
                @click="handleManageSubscription"
              >
                <ExternalLink class="h-3.5 w-3.5" />
                Portail client Lemon Squeezy
              </button>
              <button
                v-else-if="subscription.status === 'active' && subscription.cancel_at_period_end"
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full border border-amber-500/50 bg-amber-500/10 px-4 py-2 text-amber-200 hover:bg-amber-500/20"
                @click="handleManageSubscription"
              >
                <ExternalLink class="h-3.5 w-3.5" />
                Réactiver mon abonnement
              </button>
              <button
                v-else
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
                @click="handleSubscribe"
              >
                <Crown class="h-3.5 w-3.5" />
                Souscrire un abonnement
              </button>
            </div>
          </div>
        </section>

        <!-- Pricing info -->
        <section class="space-y-4">
          <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
            <div class="flex items-start gap-4 mb-5">
              <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                <Crown class="h-4 w-4" />
              </div>
              <div class="flex-1">
                <div class="flex flex-wrap items-start justify-between gap-4">
                  <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wide text-slate-500">
                      Formule
                    </p>
                    <h3 class="text-sm md:text-base font-semibold text-slate-50">
                      {{ planInfo?.name || 'UNICOACH Pro' }}
                    </h3>
                  </div>
                </div>
              </div>
            </div>

            <div class="rounded-2xl bg-gradient-to-r from-purple-500 to-pink-500 p-5 text-white mb-4">
              <div class="flex flex-wrap items-baseline justify-between gap-3 mb-3">
                <div>
                  <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold">{{ planInfo?.price || '20' }}€</span>
                    <span class="text-sm opacity-90">{{ planInfo?.interval || 'HTVA / mois' }}</span>
                  </div>
                  <p v-if="planInfo?.original_price" class="text-xs opacity-80 mt-1">
                    Prix normal : <span class="line-through">{{ planInfo.original_price }}€</span>
                  </p>
                </div>
                <div
                  v-if="planInfo?.is_fea_price"
                  class="rounded-xl bg-white/20 backdrop-blur-sm px-3 py-2 text-right text-xs"
                >
                  <p class="text-emerald-200 font-semibold mb-1">Partenariat</p>
                  <p class="font-bold">Fitness Education Academy</p>
                </div>
              </div>
              <p class="text-xs opacity-90">
                {{ planInfo?.description || 'Bénéficiez d\'une réduction permanente grâce au partenariat avec FEA.' }}
              </p>
            </div>

            <div class="space-y-2 text-xs text-slate-200">
              <p class="font-semibold text-slate-100 mb-3">Fonctionnalités incluses :</p>
              <div class="space-y-2">
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Site web personnalisé avec votre sous-domaine</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Gestion des plans et transformations avant/après</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Formulaire de contact et gestion des prospects</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Base clients avec notes et documents</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Support prioritaire par ticket</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Mises à jour et nouvelles fonctionnalités incluses</span>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<style scoped>
@keyframes breathe {
  0% {
    transform: scale(0.9);
    opacity: 0.8;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.45);
  }
  70% {
    transform: scale(1.4);
    opacity: 0.2;
    box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
  }
  100% {
    transform: scale(0.9);
    opacity: 0.8;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
  }
}

.animate-breathe {
  animation: breathe 2.2s ease-in-out infinite;
}
</style>
