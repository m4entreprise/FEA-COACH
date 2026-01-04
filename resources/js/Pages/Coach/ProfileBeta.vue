<script setup>
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import { Head, router } from '@inertiajs/vue3';
import { User, Lock, AlertTriangle } from 'lucide-vue-next';
import { computed } from 'vue';
import { Toaster } from 'vue-sonner';

const props = defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const dashboardBackUrl = computed(() => {
  const tab = window.sessionStorage?.getItem('coach_dashboard_tab');
  return tab ? `${route('dashboard')}?tab=${tab}` : route('dashboard');
});

const goBack = () => {
  router.visit(dashboardBackUrl.value);
};
</script>

<template>
  <Head title="Profil " />

  <div class="min-h-screen bg-slate-950 text-slate-50 flex flex-col">
    <Toaster rich-colors theme="dark" position="top-right" close-button />
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
            <span>Mon profil</span>
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
            <h2 class="text-lg font-semibold">Paramètres du compte</h2>
            <p class="text-sm text-slate-400">
              Gérez vos informations personnelles, votre sécurité et vos préférences.
            </p>
          </div>
        </section>

        <section
          class="rounded-2xl border border-slate-800 bg-slate-950/70 p-5 shadow-xl space-y-2"
        >
          <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-300">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-breathe"></span>
              <span>Compte actif et sécurisé</span>
            </div>
          </div>
        </section>

        <!-- Profile Information -->
        <section class="space-y-4">
          <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
            <div class="flex items-start gap-4 mb-5">
              <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                <User class="h-4 w-4" />
              </div>
              <div class="flex-1">
                <div class="flex flex-wrap items-start justify-between gap-4">
                  <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wide text-slate-500">
                      Informations personnelles
                    </p>
                    <h3 class="text-sm md:text-base font-semibold text-slate-50">
                      Informations du compte
                    </h3>
                    <p class="text-xs text-slate-400">
                      Mettez à jour votre nom et votre adresse e-mail.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="border-t border-slate-800 pt-5">
              <UpdateProfileInformationForm
                :must-verify-email="props.mustVerifyEmail"
                :status="props.status"
                class="max-w-xl"
              />
            </div>
          </div>
        </section>

        <!-- Password -->
        <section class="space-y-4">
          <div class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl">
            <div class="flex items-start gap-4 mb-5">
              <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-sky-500 to-sky-400 flex items-center justify-center shadow-lg flex-shrink-0">
                <Lock class="h-4 w-4" />
              </div>
              <div class="flex-1">
                <div class="flex flex-wrap items-start justify-between gap-4">
                  <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wide text-slate-500">
                      Sécurité
                    </p>
                    <h3 class="text-sm md:text-base font-semibold text-slate-50">
                      Mot de passe
                    </h3>
                    <p class="text-xs text-slate-400">
                      Assurez-vous que votre compte utilise un mot de passe long et aléatoire.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="border-t border-slate-800 pt-5">
              <UpdatePasswordForm class="max-w-xl" />
            </div>
          </div>
        </section>

        <!-- Delete Account -->
        <section class="space-y-4">
          <div class="rounded-2xl border border-rose-500/40 bg-rose-950/40 p-5 shadow-xl">
            <div class="flex items-start gap-4 mb-5">
              <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-rose-500 to-rose-400 flex items-center justify-center shadow-lg flex-shrink-0">
                <AlertTriangle class="h-4 w-4" />
              </div>
              <div class="flex-1">
                <div class="flex flex-wrap items-start justify-between gap-4">
                  <div class="space-y-1">
                    <p class="text-xs uppercase tracking-wide text-rose-400">
                      Zone dangereuse
                    </p>
                    <h3 class="text-sm md:text-base font-semibold text-rose-50">
                      Suppression du compte
                    </h3>
                    <p class="text-xs text-rose-200">
                      Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="border-t border-rose-500/40 pt-5">
              <DeleteUserForm class="max-w-xl" />
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
