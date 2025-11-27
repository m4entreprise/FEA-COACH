<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Mon Profil
            </h2>
        </template>

        <div class="py-12 bg-gradient-to-br from-slate-50 via-purple-50 to-slate-50 dark:from-slate-900 dark:via-purple-900/20 dark:to-slate-900 min-h-screen">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 p-4 shadow-lg sm:rounded-2xl sm:p-8 border border-blue-200/50 dark:border-blue-500/30 backdrop-blur-xl hover:shadow-2xl transition-all duration-300"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-gradient-to-br from-white to-purple-50 dark:from-gray-800 dark:to-purple-900/20 p-4 shadow-lg sm:rounded-2xl sm:p-8 border border-purple-200/50 dark:border-purple-500/30 backdrop-blur-xl hover:shadow-2xl transition-all duration-300"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-gradient-to-br from-white to-red-50 dark:from-gray-800 dark:to-red-900/20 p-4 shadow-lg sm:rounded-2xl sm:p-8 border border-red-200/50 dark:border-red-500/30 backdrop-blur-xl hover:shadow-2xl transition-all duration-300"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
