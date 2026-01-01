<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  subscription: Object,
  user: Object,
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

const handleSubscribe = () => {
  router.post(route('dashboard.subscription.checkout', { beta: 1 }));
};

const handleManageSubscription = () => {
  router.post(route('dashboard.subscription.portal', { beta: 1 }));
};
</script>

<template>
  <Head title="Abonnement (beta)" />

  <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
    <!-- Top bar -->
    <header
      class="h-16 flex items-center justify-between px-4 md:px-6 border-b border-slate-800 bg-slate-900/80 backdrop-blur-xl"
    >
      <div class="flex items-center gap-3">
        <div class="flex flex-col">
          <p class="text-xs uppercase tracking-wide text-slate-400">
            Panel coach beta
          </p>
          <h1 class="text-base md:text-lg font-semibold flex items-center gap-2">
            <span>Abonnement</span>
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <a
          :href="route('dashboard.coach.beta')"
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
      <div class="max-w-4xl mx-auto space-y-6">
        <!-- Flash messages -->
        <section v-if="$page.props.flash?.success" class="space-y-2">
          <div
            class="rounded-2xl bg-gradient-to-r from-emerald-500 to-green-600 p-4 text-xs text-white shadow-xl flex items-center gap-2"
          >
            <span>✔</span>
            <span>{{ $page.props.flash.success }}</span>
          </div>
        </section>
        <section v-if="$page.props.flash?.info" class="space-y-2">
          <div
            class="rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 p-4 text-xs text-white shadow-xl flex items-center gap-2"
          >
            <span>ℹ</span>
            <span>{{ $page.props.flash.info }}</span>
          </div>
        </section>

        <!-- Subscription status -->
        <section
          class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl"
        >
          <div class="p-6 space-y-5">
            <div class="flex items-center gap-3">
              <div
                class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl p-3 shadow-lg"
              >
                <span class="text-xl">💳</span>
              </div>
              <div>
                <h2 class="text-lg font-semibold">Votre abonnement</h2>
                <p class="text-xs text-slate-400">
                  Gérez votre période d'essai et votre formule payante.
                </p>
              </div>
            </div>

            <div
              v-if="subscription.is_on_trial"
              class="rounded-xl bg-gradient-to-r from-blue-500/20 to-indigo-500/20 border border-blue-500/40 p-4 text-xs text-slate-100"
            >
              <p class="font-semibold mb-1">Période d'essai active</p>
              <p class="mb-1">
                Il vous reste
                <span class="font-bold text-blue-300">
                  {{ subscription.trial_days_left }} jours
                </span>
                d'essai.
              </p>
              <p v-if="subscriptionEndDate" class="text-slate-300">
                Expire le {{ subscriptionEndDate }}.
              </p>
            </div>

            <div
              v-else
              class="rounded-xl bg-gradient-to-r from-emerald-500/20 to-green-500/20 border border-emerald-500/40 p-4 text-xs text-slate-100"
            >
              <p class="font-semibold mb-1">
                Abonnement {{ subscription.status === 'active' ? 'actif' : 'inactif' }}
              </p>
              <p class="text-slate-200">
                {{
                  subscription.status === 'active'
                    ? "Votre abonnement est actif et vous donne accès à toutes les fonctionnalités."
                    : "Votre abonnement n'est pas actif. Souscrivez pour continuer à utiliser la plateforme."
                }}
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
              <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-3">
                <p class="text-slate-400 mb-1">Nom</p>
                <p class="text-slate-50 font-semibold">{{ user.name }}</p>
              </div>
              <div class="rounded-xl bg-slate-950/70 border border-slate-800 p-3">
                <p class="text-slate-400 mb-1">Email</p>
                <p class="text-slate-50 font-semibold">
                  {{ user.email }}
                </p>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-1 text-xs">
              <button
                v-if="subscription.is_on_trial"
                type="button"
                class="flex-1 rounded-full bg-gradient-to-r from-emerald-500 to-green-600 px-4 py-2 font-semibold text-slate-950 shadow-lg hover:from-emerald-600 hover:to-green-700"
                @click="handleSubscribe"
              >
                S'abonner maintenant
              </button>
              <button
                v-else
                type="button"
                class="flex-1 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 px-4 py-2 font-semibold text-slate-50 shadow-lg hover:from-blue-600 hover:to-indigo-700"
                @click="handleManageSubscription"
              >
                Gérer mon abonnement
              </button>
            </div>
          </div>
        </section>

        <!-- Pricing info -->
        <section
          class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/80 shadow-xl"
        >
          <div class="p-6 space-y-4 text-xs">
            <h2 class="text-sm font-semibold">Formule FEA Coach Pro</h2>
            <div
              class="rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 p-5 text-slate-50 flex flex-col gap-3"
            >
              <div class="flex flex-wrap items-baseline justify-between gap-3">
                <div>
                  <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold">20€</span>
                    <span class="text-sm opacity-80">HTVA / mois</span>
                  </div>
                  <p class="text-[11px] opacity-80">
                    Prix normal :
                    <span class="line-through">30€</span>
                  </p>
                </div>
                <div
                  class="rounded-xl bg-white/10 px-3 py-2 text-right text-[11px]"
                >
                  <p class="text-emerald-200 font-semibold mb-1">Partenariat</p>
                  <p class="font-bold">Fitness Education Academy</p>
                </div>
              </div>
              <p class="text-[11px]">
                Bénéficiez d'une réduction permanente grâce à l'intervention de
                FEA.
              </p>
            </div>
            <ul class="space-y-2 text-slate-200">
              <li>• Site web personnalisé avec votre sous-domaine</li>
              <li>• Gestion des plans et transformations</li>
              <li>• Formulaire de contact et gestion des prospects</li>
              <li>• Base clients avec notes</li>
              <li>• Support prioritaire par email</li>
            </ul>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>
