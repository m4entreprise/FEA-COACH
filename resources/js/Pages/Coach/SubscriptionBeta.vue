<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import { CreditCard, Calendar, Check, ExternalLink, AlertCircle, Sparkles, Crown, Globe } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
  subscription: Object,
  user: Object,
  planInfo: Object,
  customDomain: Object,
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

// Check if user has an active subscription (even during trial)
const hasSubscription = computed(() => {
  return !!props.subscription.lemonsqueezy_subscription_id;
});

const dashboardBackUrl = computed(() => {
  if (typeof window === 'undefined') return route('dashboard');
  const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
  return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
  if (typeof window !== 'undefined' && window.history.length > 1) {
    window.history.back();
    return;
  }

  router.visit(dashboardBackUrl.value);
};

const handleSubscribe = () => {
  router.post('/dashboard/subscription/checkout');
};

const handleManageSubscription = async () => {
  try {
    const { data } = await axios.post('/dashboard/subscription/portal');
    
    if (data.redirect_url) {
      window.location.href = data.redirect_url;
    }
  } catch (error) {
    console.error('Error accessing customer portal:', error);
    alert('Une erreur est survenue. Veuillez réessayer.');
  }
};

const showDomainModal = ref(false);
const desiredDomain = ref('');

const buyCustomDomain = (requestedDomain = null) => {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = route('dashboard.subscription.custom-domain');
  
  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '_token';
  csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
  form.appendChild(csrfInput);

  if (requestedDomain) {
    const domainInput = document.createElement('input');
    domainInput.type = 'hidden';
    domainInput.name = 'desired_domain';
    domainInput.value = requestedDomain;
    form.appendChild(domainInput);
  }
  
  document.body.appendChild(form);
  form.submit();
};

const openDomainModal = () => {
  desiredDomain.value = '';
  showDomainModal.value = true;
};

const submitDomainPurchase = () => {
  const value = desiredDomain.value.trim();
  if (!value) {
    alert("Veuillez indiquer un nom de domaine souhaité.");
    return;
  }

  showDomainModal.value = false;
  buyCustomDomain(value);
};

const domainExpiryDate = computed(() => {
  if (!props.customDomain?.expires_at) return null;
  return new Date(props.customDomain.expires_at).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
});

const hasCustomDomainOrder = computed(() => !!props.customDomain);
const customDomainStatus = computed(() => props.customDomain?.status ?? null);
const customDomainStatusMeta = computed(() => {
  const status = customDomainStatus.value;
  const defaults = {
    text: 'Acheté - en attente d’installation',
    textClass: 'text-emerald-300',
    badgeClass: 'text-emerald-400',
  };

  switch (status) {
    case 'pending':
      return {
        text: 'En attente - achat en cours',
        textClass: 'text-amber-300',
        badgeClass: 'text-amber-300',
      };
    case 'active':
      return {
        text: 'Actif',
        textClass: 'text-emerald-300',
        badgeClass: 'text-emerald-400',
      };
    case 'expired':
      return {
        text: 'Le nom de domaine a expiré',
        textClass: 'text-rose-300',
        badgeClass: 'text-rose-400',
      };
    case 'cancelled':
      return {
        text: 'Commande annulée',
        textClass: 'text-slate-300',
        badgeClass: 'text-slate-400',
      };
    default:
      return defaults;
  }
});
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
                    {{ hasSubscription ? 'Période d\'essai avec abonnement actif' : 'Profitez de votre essai gratuit' }}
                  </p>
                  <p class="text-xs text-blue-200">
                    <template v-if="hasSubscription">
                      Votre abonnement est actif. Il vous reste <span class="font-bold">{{ subscription.trial_days_left }} jour{{ subscription.trial_days_left > 1 ? 's' : '' }}</span> d'essai gratuit avant le premier paiement.
                    </template>
                    <template v-else>
                      Il vous reste <span class="font-bold">{{ subscription.trial_days_left }} jour{{ subscription.trial_days_left > 1 ? 's' : '' }}</span> pour tester toutes les fonctionnalités.
                    </template>
                  </p>
                  <div class="flex items-center gap-2 text-xs text-blue-300">
                    <Calendar class="h-3 w-3" />
                    <span>{{ hasSubscription ? 'Premier paiement le' : 'Expire le' }} {{ subscriptionEndDate }}</span>
                  </div>
                  <div v-if="subscription.cancel_at_period_end" class="flex items-center gap-2 text-xs text-amber-300 mt-2 pt-2 border-t border-blue-500/30">
                    <AlertCircle class="h-3 w-3" />
                    <span>Abonnement annulé - l'accès restera actif jusqu'au {{ subscriptionEndDate }}</span>
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
              <!-- User has subscription with cancellation scheduled -->
              <button
                v-if="hasSubscription && subscription.cancel_at_period_end"
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full border border-amber-500/50 bg-amber-500/10 px-4 py-2 text-amber-200 hover:bg-amber-500/20"
                @click="handleManageSubscription"
              >
                <ExternalLink class="h-3.5 w-3.5" />
                Réactiver mon abonnement
              </button>
              <!-- User has active subscription (trial or paid) -->
              <button
                v-else-if="hasSubscription"
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full border border-slate-700 bg-slate-800 px-4 py-2 text-slate-200 hover:bg-slate-700"
                @click="handleManageSubscription"
              >
                <ExternalLink class="h-3.5 w-3.5" />
                Gérer mon abonnement
              </button>
              <!-- No subscription yet -->
              <button
                v-else
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-4 py-2 font-semibold text-white shadow-lg hover:from-purple-600 hover:to-pink-600"
                @click="handleSubscribe"
              >
                <Crown class="h-3.5 w-3.5" />
                S'abonner maintenant
              </button>
            </div>
          </div>
        </section>

        <!-- Subscription bundles -->
        <section class="space-y-6">
          <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- UNICOACH subscription card -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
              <div class="flex items-start gap-4 mb-5">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                  <Crown class="h-4 w-4" />
                </div>
                <div class="flex-1">
                  <p class="text-xs uppercase tracking-wide text-slate-500">
                    Abonnement
                  </p>
                  <h3 class="text-base font-semibold text-slate-50">
                    UNICOACH
                  </h3>
                  <p class="text-xs text-slate-400 mt-1">
                    Gardez la main sur vos paiements, votre essai gratuit et votre portail client en un clin d'œil.
                  </p>
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

              <div class="text-xs text-slate-300 flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-1">
                  <Check class="h-3.5 w-3.5 text-emerald-400" />
                  <span>Essai gratuit inclus</span>
                </div>
                <div class="flex items-center gap-1">
                  <CreditCard class="h-3.5 w-3.5 text-purple-300" />
                  <span>Portail Stripe sécurisé</span>
                </div>
                <div class="flex items-center gap-1">
                  <Sparkles class="h-3.5 w-3.5 text-pink-300" />
                  <span>Mises à jour continues</span>
                </div>
              </div>
            </div>

            <!-- Domain subscription card -->
            <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
              <div class="flex items-start gap-4 mb-5">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-400 flex items-center justify-center shadow-lg flex-shrink-0">
                  <Globe class="h-4 w-4" />
                </div>
                <div class="flex-1">
                  <p class="text-xs uppercase tracking-wide text-slate-500">
                    Abonnement
                  </p>
                  <h3 class="text-base font-semibold text-slate-50">
                    Nom de domaine
                  </h3>
                  <p class="text-xs text-slate-400 mt-1">
                    Centralisez l'achat, la configuration et le suivi de votre domaine personnalisé.
                  </p>
                </div>
              </div>

              <!-- Has custom domain -->
              <div v-if="customDomain" class="space-y-4">
                <div class="rounded-xl border border-indigo-500/40 bg-indigo-950/40 p-4">
                  <div class="flex items-start gap-3">
                    <Check class="h-5 w-5 text-indigo-300 flex-shrink-0" />
                    <div class="flex-1 space-y-2">
                      <p class="text-sm font-semibold text-indigo-100">
                        {{ customDomain.domain || 'Domaine configuré' }}
                      </p>
                      <p class="text-xs text-indigo-200">
                        Votre site est accessible via votre nom de domaine personnalisé.
                      </p>
                      <div v-if="domainExpiryDate" class="flex items-center gap-2 text-xs text-indigo-300">
                        <Calendar class="h-3 w-3" />
                        <span>Renouvellement le {{ domainExpiryDate }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                  <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-3">
                    <p class="text-slate-400 mb-1">Domaine</p>
                    <p class="text-slate-50 font-semibold">{{ customDomain.domain || 'Non configuré' }}</p>
                  </div>
                  <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-3">
                    <p class="text-slate-400 mb-1">Statut</p>
                    <p class="font-semibold" :class="customDomainStatusMeta.badgeClass">
                      {{ customDomainStatusMeta.text }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- No custom domain - Promotion -->
              <div v-else class="space-y-4">
                <div class="rounded-xl border border-amber-500/40 bg-amber-500/5 text-amber-100 text-xs p-4">
                  Notre équipe vous recontactera dans les 48h ouvrables afin d'installer le nouveau nom de domaine.
                </div>
                <div class="rounded-xl border border-slate-700/60 bg-gradient-to-br from-slate-900/40 to-slate-900/60 p-4">
                  <div class="flex items-start gap-3">
                    <Sparkles class="h-5 w-5 text-purple-400 flex-shrink-0" />
                    <div class="flex-1 space-y-3">
                      <div>
                        <p class="text-sm font-semibold text-slate-200 mb-1">
                          Donnez plus de professionnalisme à votre présence
                        </p>
                        <p class="text-xs text-slate-400">
                          Utilisez votre propre nom de domaine (exemple : <span class="text-purple-300">www.moncoaching.com</span>) au lieu de <span class="text-slate-500">*.unicoach.app</span>
                        </p>
                      </div>
                      
                      <div class="flex items-center justify-between gap-3 pt-2 border-t border-slate-700/50">
                        <div>
                          <p class="text-xs text-slate-300 font-medium">Nom de domaine personnalisé</p>
                          <p class="text-[10px] text-slate-500">65€ HTVA / an</p>
                        </div>
                        <button
                          type="button"
                          @click="openDomainModal"
                          class="inline-flex items-center gap-1.5 rounded-lg bg-purple-500/20 border border-purple-500/40 px-4 py-2 text-xs font-medium text-purple-100 hover:bg-purple-500/30 hover:border-purple-500/60 transition-colors whitespace-nowrap"
                        >
                          <CreditCard class="h-3.5 w-3.5" />
                          Acheter
                        </button>
                        <span
                          v-if="hasCustomDomainOrder"
                          type="button"
                          class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/40 px-4 py-2 text-xs font-semibold text-emerald-200 whitespace-nowrap"
                        >
                          Acheté
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Formule UNICOACH Pro detail -->
            <div class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl">
              <p class="text-xs uppercase tracking-wide text-slate-500">Formule</p>
              <h3 class="text-lg font-semibold text-slate-50 mb-3">UNICOACH Pro</h3>
              <p class="text-sm text-slate-300 mb-4">
                Tout ce dont vous avez besoin pour lancer et scaler votre activité digitale. La formule Pro regroupe l'intégralité des modules coach, les automatisations emailing et des outils marketing dédiés.
              </p>
              <div class="space-y-3 text-xs text-slate-200">
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Site web personnalisé avec votre sous-domaine et pages illimitées</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Gestion des plans, transformations avant/après et suivi client complet</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Formulaire de contact, pipeline prospects et notes internes</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Support prioritaire par ticket et mises à jour continues</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>Accès aux nouvelles fonctionnalités en avant-première</span>
                </div>
              </div>
            </div>

            <!-- Formule Nom de domaine detail -->
            <div class="rounded-2xl border border-indigo-500/30 bg-indigo-950/40 p-5 shadow-xl">
              <p class="text-xs uppercase tracking-wide text-indigo-200">Formule</p>
              <h3 class="text-lg font-semibold text-white mb-3">Nom de domaine personnalisé</h3>
              <p class="text-sm text-indigo-100 mb-4">
                Renforcez votre image de marque avec une adresse professionnelle (ex. <span class="text-white font-semibold">www.moncoaching.com</span>) déjà configurée et sécurisée par l'équipe UNICOACH.
              </p>
              <div class="space-y-3 text-xs text-indigo-100">
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-white flex-shrink-0 mt-0.5" />
                  <span>Achat, configuration DNS et renouvellement annuel gérés pour vous (65€ HTVA / an)</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-white flex-shrink-0 mt-0.5" />
                  <span>Redirection HTTPS, emails techniques et certificat SSL inclus</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-white flex-shrink-0 mt-0.5" />
                  <span>Suivi de statut en temps réel et notifications avant expiration</span>
                </div>
                <div class="flex items-start gap-2">
                  <Check class="h-4 w-4 text-white flex-shrink-0 mt-0.5" />
                  <span>Accompagnement dédié : prise de contact sous 48h ouvrables</span>
                </div>
              </div>
              <div class="mt-4 rounded-xl border border-white/20 bg-white/5 p-4 text-xs text-indigo-50">
                <p>Passez commande maintenant : nous vérifions la disponibilité, achetons le domaine en votre nom et l'activons sur votre site UNICOACH.</p>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- Modal: custom domain purchase -->
    <Modal :show="showDomainModal" @close="showDomainModal = false">
      <div class="p-6 bg-slate-900 text-slate-50">
        <h2 class="text-lg font-semibold mb-2">Nom de domaine souhaité</h2>
        <p class="text-xs text-slate-400 mb-4">
          Indiquez le nom de domaine que vous aimeriez utiliser pour votre site.
          Par exemple : <span class="text-purple-300">www.moncoaching.com</span>
        </p>

        <div class="space-y-2">
          <label class="text-xs text-slate-300">Nom de domaine préféré</label>
          <input
            v-model="desiredDomain"
            type="text"
            placeholder="exemple : www.moncoaching.com"
            class="mt-1 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-purple-500 focus:ring-purple-500"
          />
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button
            type="button"
            @click="showDomainModal = false"
            class="inline-flex items-center rounded-lg border border-slate-700 px-3 py-1.5 text-xs font-medium text-slate-200 hover:bg-slate-800"
          >
            Annuler
          </button>
          <button
            type="button"
            @click="submitDomainPurchase"
            class="inline-flex items-center rounded-lg bg-purple-500 px-3 py-1.5 text-xs font-semibold text-slate-950 hover:bg-purple-400"
          >
            Continuer vers le paiement
          </button>
        </div>
      </div>
    </Modal>
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
