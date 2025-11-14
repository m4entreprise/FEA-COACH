<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    coach: Object,
});

const form = useForm({
    name: props.coach.name,
    email: props.coach.user_email,
    password: '',
    subdomain: props.coach.subdomain,
    color_primary: props.coach.color_primary,
    color_secondary: props.coach.color_secondary,
    is_active: props.coach.is_active,
    is_fea_graduate: props.coach.is_fea_graduate,
    trial_ends_at: props.coach.trial_ends_at,
});

const submit = () => {
    form.patch(route('admin.coaches.update', props.coach.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Modifier un Coach" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Modifier: {{ coach.name }}
                </h2>
                <Link
                    :href="route('admin.coaches.index')"
                    class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                >
                    ← Retour
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="space-y-6">
                            <!-- Coach Name -->
                            <div>
                                <InputLabel for="name" value="Nom du Coach" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <!-- Password -->
                            <div>
                                <InputLabel for="password" value="Nouveau mot de passe (optionnel)" />
                                <TextInput
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Laissez vide pour conserver le mot de passe actuel
                                </p>
                            </div>

                            <!-- Subdomain -->
                            <div>
                                <InputLabel for="subdomain" value="Sous-domaine" />
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <TextInput
                                        id="subdomain"
                                        v-model="form.subdomain"
                                        type="text"
                                        class="block w-full rounded-none rounded-l-md"
                                        required
                                        placeholder="nom-coach"
                                    />
                                    <span class="inline-flex items-center rounded-r-md border border-l-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        .kineseducation.academy
                                    </span>
                                </div>
                                <InputError class="mt-2" :message="form.errors.subdomain" />
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Uniquement des lettres minuscules, chiffres et tirets
                                </p>
                            </div>

                            <!-- Colors -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <InputLabel for="color_primary" value="Couleur Primaire" />
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input
                                            id="color_primary"
                                            v-model="form.color_primary"
                                            type="color"
                                            class="h-10 w-20 cursor-pointer rounded border-gray-300 dark:border-gray-700"
                                        />
                                        <TextInput
                                            v-model="form.color_primary"
                                            type="text"
                                            class="block flex-1"
                                            pattern="^#[0-9A-Fa-f]{6}$"
                                        />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.color_primary" />
                                </div>

                                <div>
                                    <InputLabel for="color_secondary" value="Couleur Secondaire" />
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input
                                            id="color_secondary"
                                            v-model="form.color_secondary"
                                            type="color"
                                            class="h-10 w-20 cursor-pointer rounded border-gray-300 dark:border-gray-700"
                                        />
                                        <TextInput
                                            v-model="form.color_secondary"
                                            type="text"
                                            class="block flex-1"
                                            pattern="^#[0-9A-Fa-f]{6}$"
                                        />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.color_secondary" />
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 dark:border-gray-700 dark:bg-gray-900 dark:ring-offset-gray-800"
                                />
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    Coach actif (visible publiquement)
                                </label>
                            </div>

                            <!-- FEA Graduate Status -->
                            <div class="flex items-center">
                                <input
                                    id="is_fea_graduate"
                                    v-model="form.is_fea_graduate"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-600 dark:border-gray-700 dark:bg-gray-900 dark:ring-offset-gray-800"
                                />
                                <label for="is_fea_graduate" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    Diplômé FEA
                                </label>
                            </div>

                            <!-- Trial End Date -->
                            <div>
                                <InputLabel for="trial_ends_at" value="Fin période d'essai (optionnel)" />
                                <TextInput
                                    id="trial_ends_at"
                                    v-model="form.trial_ends_at"
                                    type="date"
                                    class="mt-1 block w-full"
                                />
                                <InputError class="mt-2" :message="form.errors.trial_ends_at" />
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Date de fin de la période d'essai gratuite (pour les comptes FEA)
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-4">
                            <Link
                                :href="route('admin.coaches.index')"
                                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                            >
                                Annuler
                            </Link>

                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Enregistrer
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
