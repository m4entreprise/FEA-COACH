<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Créer mon compte - UNICOACH" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center px-4">
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-purple-600 rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-pink-600 rounded-full opacity-20 blur-3xl"></div>
        </div>

        <div class="w-full max-w-md relative">
            <!-- Logo -->
            <div class="text-center mb-8">
                <Link href="/">
                    <div class="text-4xl font-bold text-white mb-2">
                        Ignite <span class="text-purple-400">Coach</span>
                    </div>
                </Link>
                <p class="text-gray-400">Créez votre compte pour commencer</p>
            </div>

            <!-- Register Card -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl p-8">
                <h1 class="text-2xl font-bold text-white mb-6 text-center">
                    Créer mon compte
                </h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-200 mb-2">
                            Adresse email
                        </label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="votre@email.com"
                        />
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-400">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-200 mb-2">
                            Mot de passe
                        </label>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-400">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-200 mb-2">
                            Confirmer le mot de passe
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-400">
                            {{ form.errors.password_confirmation }}
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-lg shadow-lg transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <span v-if="!form.processing">Créer mon compte</span>
                        <span v-else class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Création...
                        </span>
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center">
                    <p class="text-gray-400 text-sm">
                        Vous avez déjà un compte ?
                        <Link :href="route('login')" class="text-purple-400 hover:text-purple-300 font-medium">
                            Se connecter
                        </Link>
                    </p>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-6 text-center">
                <Link href="/" class="text-sm text-gray-400 hover:text-purple-400 transition">
                    ← Retour à l'accueil
                </Link>
            </div>
        </div>
    </div>
</template>
