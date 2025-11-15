<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <nav
                class="border-b border-purple-200/50 dark:border-purple-500/30 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-lg"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')" class="flex items-center">
                                    <!-- Coach Logo if available -->
                                    <img 
                                        v-if="page.props.coachLogoUrl" 
                                        :src="page.props.coachLogoUrl" 
                                        alt="Logo" 
                                        class="h-10 w-auto object-contain"
                                    />
                                    <!-- Ignite Coach text if no logo -->
                                    <span 
                                        v-else
                                        class="text-xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent"
                                    >
                                        Ignite Coach
                                    </span>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('dashboard.branding')"
                                    :active="route().current('dashboard.branding')"
                                >
                                    Branding
                                </NavLink>
                                <NavLink
                                    :href="route('dashboard.content')"
                                    :active="route().current('dashboard.content')"
                                >
                                    Contenu
                                </NavLink>
                                <NavLink
                                    :href="route('dashboard.gallery')"
                                    :active="route().current('dashboard.gallery')"
                                >
                                    Galerie
                                </NavLink>
                                <NavLink
                                    :href="route('dashboard.plans')"
                                    :active="route().current('dashboard.plans')"
                                >
                                    Plans
                                </NavLink>
                                <NavLink
                                    :href="route('dashboard.contact')"
                                    :active="route().current('dashboard.contact')"
                                >
                                    Contact
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-xl border border-purple-200/50 dark:border-purple-500/30 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm px-4 py-2 text-sm font-semibold leading-4 text-gray-700 dark:text-gray-300 transition-all duration-200 ease-in-out hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:border-purple-300 dark:hover:border-purple-400 hover:shadow-md focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-xl border border-purple-200/50 dark:border-purple-500/30 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm p-2 text-gray-600 dark:text-gray-300 transition-all duration-200 ease-in-out hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:border-purple-300 dark:hover:border-purple-400 hover:shadow-md focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border-t border-purple-200/50 dark:border-purple-500/30"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('dashboard.branding')"
                            :active="route().current('dashboard.branding')"
                        >
                            Branding
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('dashboard.content')"
                            :active="route().current('dashboard.content')"
                        >
                            Contenu
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('dashboard.gallery')"
                            :active="route().current('dashboard.gallery')"
                        >
                            Galerie
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('dashboard.plans')"
                            :active="route().current('dashboard.plans')"
                        >
                            Plans
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('dashboard.contact')"
                            :active="route().current('dashboard.contact')"
                        >
                            Contact
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-purple-200/50 dark:border-purple-500/30 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800 dark:text-gray-200"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-gradient-to-r from-white/90 to-purple-50/90 dark:from-gray-800/90 dark:to-purple-900/20 backdrop-blur-xl shadow-lg border-b border-purple-200/50 dark:border-purple-500/30"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
