<script setup>
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});
</script>

<template>
  <Head title="Profil (beta)" />

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
            <span>Mon profil</span>
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
      <div class="max-w-5xl mx-auto space-y-6">
        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl"
        >
          <h2 class="text-sm font-semibold mb-3">Informations du compte</h2>
          <UpdateProfileInformationForm
            :must-verify-email="props.mustVerifyEmail"
            :status="props.status"
            class="max-w-xl"
          />
        </section>

        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl"
        >
          <h2 class="text-sm font-semibold mb-3">Mot de passe</h2>
          <UpdatePasswordForm class="max-w-xl" />
        </section>

        <section
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-xl"
        >
          <h2 class="text-sm font-semibold mb-3">Suppression du compte</h2>
          <DeleteUserForm class="max-w-xl" />
        </section>
      </div>
    </main>
  </div>
</template>
