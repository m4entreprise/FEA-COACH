<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
});
</script>

<template>
    <Head title="UNICOACH - Votre site vitrine professionnel de coaching" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <!-- Navigation -->
        <nav class="fixed w-full bg-white/10 backdrop-blur-md z-50 border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-2">
                        <img src="/images/unicoach-logo.svg" alt="UNICOACH" class="h-10 w-auto">
                    </div>
                    
                    <div v-if="canLogin" class="flex items-center space-x-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="$page.props.auth.user.onboarding_completed ? route('dashboard') : route('onboarding.step1')"
                            class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-medium transition"
                        >
                            {{ $page.props.auth.user.onboarding_completed ? 'Dashboard' : 'Continuer mon inscription' }}
                        </Link>
                        
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-4 py-2 text-white hover:text-purple-300 transition"
                            >
                                Connexion
                            </Link>
                            <Link
                                :href="route('register')"
                                class="px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-medium transition"
                            >
                                Créer mon site
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="pt-32 pb-20 px-4">
            <div class="max-w-7xl mx-auto text-center">
                <div class="inline-block mb-4 px-4 py-2 bg-purple-500/20 border border-purple-400/30 rounded-full">
                    <span class="text-purple-300 font-medium">Une collaboration M4 Entreprise × Fitness Education Academy</span>
                </div>
                
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                    Votre site vitrine<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400">
                        professionnel de coaching
                    </span>
                </h1>
                
                <p class="text-xl text-gray-300 mb-8 max-w-3xl mx-auto leading-relaxed">
                    Une solution complète développée spécialement pour les diplômés de Fitness Education Academy. 
                    Lancez votre activité de coaching avec un site web optimisé, personnalisable et prêt à l'emploi.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('register')"
                        class="px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-lg shadow-lg transition transform hover:scale-105"
                    >
                        Créer mon site gratuitement
                    </Link>
                    <Link
                        :href="route('login')"
                        class="px-8 py-4 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-bold rounded-lg border border-white/20 transition"
                    >
                        Se connecter
                    </Link>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 px-4 bg-white/5 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-white mb-4">
                        Tout ce dont vous avez besoin pour réussir
                    </h2>
                    <p class="text-xl text-gray-300">
                        Un site complet, personnalisable et optimisé pour convertir vos visiteurs en clients
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-8 bg-gradient-to-br from-purple-900/50 to-pink-900/50 rounded-2xl border border-purple-500/20 backdrop-blur-sm hover:border-purple-500/40 transition">
                        <div class="w-14 h-14 bg-purple-500/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Personnalisation complète</h3>
                        <p class="text-gray-300">
                            Couleurs, logo, contenu, photos... Personnalisez chaque aspect de votre site depuis un dashboard intuitif.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-8 bg-gradient-to-br from-blue-900/50 to-purple-900/50 rounded-2xl border border-blue-500/20 backdrop-blur-sm hover:border-blue-500/40 transition">
                        <div class="w-14 h-14 bg-blue-500/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Optimisé pour convertir</h3>
                        <p class="text-gray-300">
                            Design professionnel pensé pour transformer vos visiteurs en clients. Appels à l'action stratégiques.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-8 bg-gradient-to-br from-pink-900/50 to-purple-900/50 rounded-2xl border border-pink-500/20 backdrop-blur-sm hover:border-pink-500/40 transition">
                        <div class="w-14 h-14 bg-pink-500/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Sous-domaine personnalisé</h3>
                        <p class="text-gray-300">
                            Obtenez votre propre URL professionnelle : votrenom.unicoach.app
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-8 bg-gradient-to-br from-green-900/50 to-blue-900/50 rounded-2xl border border-green-500/20 backdrop-blur-sm hover:border-green-500/40 transition">
                        <div class="w-14 h-14 bg-green-500/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Galerie transformations</h3>
                        <p class="text-gray-300">
                            Mettez en avant vos résultats avec des photos avant/après pour prouver votre expertise.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="p-8 bg-gradient-to-br from-yellow-900/50 to-orange-900/50 rounded-2xl border border-yellow-500/20 backdrop-blur-sm hover:border-yellow-500/40 transition">
                        <div class="w-14 h-14 bg-yellow-500/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Gestion des plans tarifaires</h3>
                        <p class="text-gray-300">
                            Présentez clairement vos offres de coaching avec des plans personnalisables et des CTA Calendly.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="p-8 bg-gradient-to-br from-indigo-900/50 to-purple-900/50 rounded-2xl border border-indigo-500/20 backdrop-blur-sm hover:border-indigo-500/40 transition">
                        <div class="w-14 h-14 bg-indigo-500/20 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">FAQ et contenu</h3>
                        <p class="text-gray-300">
                            Créez une section FAQ, présentez votre méthode et répondez aux questions fréquentes de vos clients.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-20 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-block mb-4 px-4 py-2 bg-purple-500/20 border border-purple-400/30 rounded-full">
                            <span class="text-purple-300 font-medium">Pourquoi UNICOACH ?</span>
                        </div>
                        <h2 class="text-4xl font-bold text-white mb-6">
                            Conçu spécifiquement pour les diplômés FEA
                        </h2>
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white mb-2">Lancez-vous rapidement</h3>
                                    <p class="text-gray-300">
                                        Pas besoin de connaissances techniques. Créez votre site en quelques minutes et commencez à recevoir des clients.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white mb-2">Design professionnel</h3>
                                    <p class="text-gray-300">
                                        Un design moderne et optimisé qui inspire confiance et convertit vos visiteurs en clients.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-white mb-2">Mise à jour facile</h3>
                                    <p class="text-gray-300">
                                        Dashboard intuitif pour modifier votre contenu, ajouter des photos, gérer vos plans tarifaires en toute simplicité.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <div class="aspect-square bg-gradient-to-br from-purple-600 to-pink-600 rounded-3xl opacity-20 blur-3xl absolute inset-0"></div>
                        <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 p-8">
                            <div class="space-y-4">
                                <div class="h-4 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full w-3/4"></div>
                                <div class="h-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full w-1/2"></div>
                                <div class="h-4 bg-gradient-to-r from-green-500 to-blue-500 rounded-full w-5/6"></div>
                                <div class="grid grid-cols-2 gap-4 pt-6">
                                    <div class="h-32 bg-gradient-to-br from-purple-500/30 to-pink-500/30 rounded-xl border border-purple-400/30"></div>
                                    <div class="h-32 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-xl border border-blue-400/30"></div>
                                </div>
                                <div class="h-20 bg-gradient-to-r from-yellow-500/30 to-orange-500/30 rounded-xl border border-yellow-400/30"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="py-20 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <div class="inline-block mb-4 px-4 py-2 bg-purple-500/20 border border-purple-400/30 rounded-full">
                        <span class="text-purple-300 font-medium">Tarification transparente</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        Nos tarifs
                    </h2>
                    <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                        Un abonnement simple et sans engagement pour développer votre présence en ligne
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                    <!-- Standard Pricing Card -->
                    <div class="relative p-8 bg-gradient-to-br from-slate-900/50 to-purple-900/30 rounded-2xl border border-white/10 backdrop-blur-sm hover:border-purple-500/40 transition flex flex-col">
                        <!-- Spacer for badge alignment -->
                        <div class="h-6 mb-4"></div>
                        
                        <div class="text-center mb-6 h-20 flex flex-col justify-center">
                            <h3 class="text-2xl font-bold text-white mb-2">Tarif Standard</h3>
                            <p class="text-gray-400">Pour tous les coachs professionnels</p>
                        </div>
                        
                        <div class="text-center mb-8 h-24 flex flex-col justify-center">
                            <div class="flex items-baseline justify-center">
                                <span class="text-5xl font-bold text-white">30€</span>
                                <span class="text-gray-400 ml-2">HTVA/mois</span>
                            </div>
                        </div>

                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-300">Site vitrine professionnel complet</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-300">Sous-domaine personnalisé</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-300">Personnalisation complète</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-gray-300">Support technique inclus</span>
                            </li>
                        </ul>

                        <Link
                            :href="route('register')"
                            class="block w-full text-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-lg border border-white/20 transition"
                        >
                            Commencer
                        </Link>
                    </div>

                    <!-- FEA Graduate Pricing Card (Highlighted) -->
                    <div class="relative p-8 bg-gradient-to-br from-purple-900/60 to-pink-900/60 rounded-2xl border-2 border-purple-500/50 backdrop-blur-sm shadow-xl shadow-purple-500/20 flex flex-col">
                        <!-- Badge -->
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <div class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full border border-purple-400/50 shadow-lg">
                                <span class="text-white font-bold text-sm">RECOMMANDÉ</span>
                            </div>
                        </div>

                        <!-- Spacer for badge -->
                        <div class="h-6 mb-4"></div>

                        <div class="text-center mb-6 h-20 flex flex-col justify-center">
                            <h3 class="text-2xl font-bold text-white mb-2">Diplômé FEA</h3>
                            <p class="text-purple-200">Pour les diplômés de Fitness Education Academy</p>
                        </div>
                        
                        <div class="text-center mb-8 h-24 flex flex-col justify-center">
                            <div class="flex items-baseline justify-center">
                                <span class="text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-300 to-pink-300">20€</span>
                                <span class="text-purple-200 ml-2">HTVA/mois</span>
                            </div>
                            <div class="mt-2">
                                <span class="inline-block px-3 py-1 bg-green-500/20 border border-green-400/30 rounded-full text-green-300 text-sm font-medium">
                                    Économisez 33%
                                </span>
                            </div>
                        </div>

                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-purple-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-white font-medium">Toutes les fonctionnalités incluses</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-purple-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-white font-medium">Tarif préférentiel à vie</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-purple-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-white font-medium">Support prioritaire</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-purple-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-white font-medium">Badge "Diplômé FEA" sur votre site</span>
                            </li>
                        </ul>

                        <Link
                            :href="route('register')"
                            class="block w-full text-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-lg shadow-lg transition transform hover:scale-105"
                        >
                            Créer mon site
                        </Link>
                    </div>
                </div>

                <div class="mt-12 text-center">
                    <p class="text-gray-400 text-sm">
                        TVA non comprise • Sans engagement • Résiliable à tout moment
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 px-4 bg-gradient-to-r from-purple-900/50 to-pink-900/50 backdrop-blur-sm border-y border-white/10">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    Prêt à lancer votre activité de coaching ?
                </h2>
                <p class="text-xl text-gray-300 mb-8">
                    Rejoignez les diplômés FEA qui ont déjà créé leur site professionnel et commencez à développer votre clientèle dès aujourd'hui.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('register')"
                        class="inline-block px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-lg shadow-lg transition transform hover:scale-105"
                    >
                        Créer mon site maintenant
                    </Link>
                    <Link
                        :href="route('login')"
                        class="inline-block px-8 py-4 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-bold rounded-lg border border-white/20 transition"
                    >
                        J'ai déjà un compte
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 px-4 border-t border-white/10">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <img src="/images/unicoach-logo.svg" alt="UNICOACH" class="h-12 w-auto mb-4 brightness-0 invert">
                        <p class="text-gray-400">
                            Solution professionnelle de sites vitrine pour coachs sportifs diplômés de Fitness Education Academy.
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-white font-semibold mb-4">Partenaires</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li>
                                <a href="https://m4entreprise.be/" target="_blank" rel="noopener" class="hover:text-purple-300 transition">
                                    M4 Entreprise
                                </a>
                            </li>
                            <li>
                                <a href="https://fitnesseducation.academy/" target="_blank" rel="noopener" class="hover:text-purple-300 transition">
                                    Fitness Education Academy
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-white font-semibold mb-4">Liens rapides</h3>
                        <ul class="space-y-2">
                            <li>
                                <Link v-if="canLogin" :href="route('login')" class="text-gray-400 hover:text-purple-400 transition">
                                    Connexion
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('register')" class="text-gray-400 hover:text-purple-400 transition">
                                    Je crée mon site
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Informations légales</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li>
                                <Link :href="route('legal.cgvu')" class="hover:text-purple-300 transition">
                                    Conditions générales de vente (CGV)
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('legal.privacy')" class="hover:text-purple-300 transition">
                                    Politique de confidentialité
                                </Link>
                            </li>
                            <li>
                                TVA : BE 103.045.9209
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="pt-8 border-t border-white/10 text-center text-gray-400">
                    <p>&copy; 2026 UNICOACH - Développé par M4 Entreprise en partenariat avec Fitness Education Academy</p>
                </div>
            </div>
        </footer>
    </div>
</template>
