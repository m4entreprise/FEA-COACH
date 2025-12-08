<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion - UNICOACH" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center px-4">
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 -left-20 w-96 h-96 bg-purple-600 rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-pink-600 rounded-full opacity-20 blur-3xl"></div>
        </div>

        <div class="w-full max-w-md relative">
            <!-- Logo / Branding -->
            <div class="text-center mb-8">
                <Link :href="route('dashboard')" class="inline-block">
                    <div class="text-4xl font-bold text-white mb-2">
                        Ignite <span class="text-purple-400">Coach</span>
                    </div>
                </Link>
                <p class="text-gray-400">Connectez-vous à votre espace</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl p-8">
                <!-- Status Message -->
                <div v-if="status" class="mb-6 px-4 py-3 bg-green-500/20 border border-green-400/30 rounded-lg">
                    <p class="text-sm text-green-300">{{ status }}</p>
                </div>

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
                            autocomplete="current-password"
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-400">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="w-4 h-4 rounded bg-white/5 border-white/10 text-purple-600 focus:ring-purple-500 focus:ring-offset-0"
                            />
                            <span class="ml-2 text-sm text-gray-300">Se souvenir de moi</span>
                        </label>
                        
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-purple-400 hover:text-purple-300 transition"
                        >
                            Mot de passe oublié ?
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-lg shadow-lg transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <span v-if="!form.processing">Se connecter</span>
                        <span v-else class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Connexion...
                        </span>
                    </button>
                </form>
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
