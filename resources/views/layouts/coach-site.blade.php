<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $coach->name }} - Coach Sportif</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    @if($coach->hasMedia('logo'))
        <link rel="icon" type="image/png" href="{{ $coach->getFirstMediaUrl('logo') }}">
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/coach-site.js'])

    <!-- Dynamic Theme Colors -->
    <style>
        :root {
            --color-primary: {{ $coach->color_primary ?? '#3B82F6' }};
            --color-secondary: {{ $coach->color_secondary ?? '#10B981' }};
            --color-primary-dark: color-mix(in srgb, var(--color-primary) 80%, black);
            --color-primary-light: color-mix(in srgb, var(--color-primary) 90%, white);
        }

        .bg-primary {
            background-color: var(--color-primary);
        }

        .bg-secondary {
            background-color: var(--color-secondary);
        }

        .text-primary {
            color: var(--color-primary);
        }

        .text-secondary {
            color: var(--color-secondary);
        }

        .border-primary {
            border-color: var(--color-primary);
        }

        .hover\:bg-primary:hover {
            background-color: var(--color-primary);
        }

        .hover\:bg-primary-dark:hover {
            background-color: var(--color-primary-dark);
        }

        .hover\:text-primary:hover {
            color: var(--color-primary);
        }

        .ring-primary {
            --tw-ring-color: var(--color-primary);
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-gray-900">
    
    <!-- Navigation -->
    <nav x-data="{ mobileMenuOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    @if($coach->hasMedia('logo'))
                        <img src="{{ $coach->getFirstMediaUrl('logo') }}" alt="{{ $coach->name }}" class="h-10 w-auto">
                    @else
                        <span class="text-xl font-bold text-primary">{{ $coach->name }}</span>
                    @endif
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex space-x-8">
                    <a href="#accueil" class="text-gray-700 hover:text-primary transition-colors font-medium">Accueil</a>
                    <a href="#a-propos" class="text-gray-700 hover:text-primary transition-colors font-medium">À propos</a>
                    <a href="#methode" class="text-gray-700 hover:text-primary transition-colors font-medium">Ma méthode</a>
                    <a href="#tarifs" class="text-gray-700 hover:text-primary transition-colors font-medium">Tarifs</a>
                    <a href="#resultats" class="text-gray-700 hover:text-primary transition-colors font-medium">Résultats</a>
                    <a href="#contact" class="text-gray-700 hover:text-primary transition-colors font-medium">Contact</a>
                </div>

                <!-- CTA Button -->
                <div class="hidden md:block">
                    <a href="#tarifs" class="inline-flex items-center px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-all shadow-md hover:shadow-lg">
                        {{ $coach->cta_text ?? 'Commencer' }}
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-primary focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="md:hidden border-t border-gray-200 bg-white"
             style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#accueil" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Accueil</a>
                <a href="#a-propos" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">À propos</a>
                <a href="#methode" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Ma méthode</a>
                <a href="#tarifs" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Tarifs</a>
                <a href="#resultats" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Résultats</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">Contact</a>
                <div class="pt-2">
                    <a href="#tarifs" class="block w-full text-center px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-all">
                        {{ $coach->cta_text ?? 'Commencer' }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Logo & Description -->
                <div>
                    @if($coach->hasMedia('logo'))
                        <img src="{{ $coach->getFirstMediaUrl('logo') }}" alt="{{ $coach->name }}" class="h-12 w-auto mb-4 brightness-0 invert">
                    @else
                        <span class="text-2xl font-bold text-white">{{ $coach->name }}</span>
                    @endif
                    <p class="text-gray-400 mt-4">
                        Coach sportif professionnel, spécialisé dans la transformation physique et le bien-être.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Liens rapides</h3>
                    <ul class="space-y-2">
                        <li><a href="#a-propos" class="text-gray-400 hover:text-white transition-colors">À propos</a></li>
                        <li><a href="#methode" class="text-gray-400 hover:text-white transition-colors">Ma méthode</a></li>
                        <li><a href="#tarifs" class="text-gray-400 hover:text-white transition-colors">Tarifs</a></li>
                        <li><a href="#resultats" class="text-gray-400 hover:text-white transition-colors">Résultats</a></li>
                        <li><a href="/mentions-legales" class="text-gray-400 hover:text-white transition-colors">Mentions légales & CGV</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <p class="text-gray-400">
                        Prêt à commencer votre transformation ?
                    </p>
                    <a href="#tarifs" class="inline-block mt-4 px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-all">
                        {{ $coach->cta_text ?? 'Commencer maintenant' }}
                    </a>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $coach->name }}. Tous droits réservés.</p>
                @if($coach->user && $coach->user->vat_number)
                    <p class="mt-2 text-sm">N° TVA : {{ $coach->user->vat_number }}</p>
                @endif
                <p class="mt-2 text-sm">
                    <a href="/mentions-legales" class="hover:text-white transition-colors">Mentions légales & CGV</a>
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
