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

        [x-cloak] {
            display: none !important;
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
<body x-data="coachSiteLightbox()" x-on:keydown.escape.window="closeLightbox()" class="font-sans antialiased bg-white text-gray-900">
    
    <!-- Navigation -->
    @hasSection('coach-site-nav')
        @yield('coach-site-nav')
    @else
        @php
            $navLinks = [
                ['label' => 'Accueil', 'href' => '#accueil'],
                ['label' => 'À propos', 'href' => '#a-propos'],
                ['label' => 'Ma méthode', 'href' => '#methode'],
                ['label' => 'Tarifs', 'href' => '#tarifs'],
                ['label' => 'Résultats', 'href' => '#resultats'],
                ['label' => 'Contact', 'href' => '#contact'],
            ];
        @endphp
        <nav
            x-data="{ mobileMenuOpen: false, scrolled: false }"
            x-init="
                const onScroll = () => scrolled = window.scrollY > 40;
                window.addEventListener('scroll', onScroll);
                onScroll();
            "
            x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileMenuOpen)"
            x-on:keydown.escape.window="mobileMenuOpen = false"
            :class="scrolled ? 'bg-white/95 shadow-lg border-b border-white/30' : 'bg-white/70 shadow-sm border-b border-white/10'"
            class="fixed top-0 left-0 right-0 z-50 backdrop-blur-xl transition-all duration-300"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        @if($coach->hasMedia('logo'))
                            <img src="{{ $coach->getFirstMediaUrl('logo') }}" alt="{{ $coach->name }}" class="h-10 w-auto">
                        @else
                            <img src="{{ asset('images/unicoach-logo.svg') }}" alt="UNICOACH" class="h-10 w-auto">
                        @endif
                        <div class="hidden lg:flex flex-col ml-3 text-xs uppercase tracking-[0.4em] text-gray-400">
                            <span class="text-gray-900 font-semibold">{{ $coach->name }}</span>
                            <span class="text-primary tracking-[0.3em]">Coaching</span>
                        </div>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center space-x-6">
                        @foreach($navLinks as $link)
                            <a
                                href="{{ $link['href'] }}"
                                class="group relative text-gray-700 hover:text-primary transition-colors font-medium"
                            >
                                {{ $link['label'] }}
                                <span class="absolute left-0 -bottom-1 h-0.5 w-full bg-gradient-to-r from-primary to-secondary scale-x-0 group-hover:scale-x-100 origin-left transition-transform"></span>
                            </a>
                        @endforeach
                    </div>

                    <!-- CTA Button -->
                    <div class="hidden lg:block">
                        <a href="#tarifs" class="inline-flex items-center gap-2 px-6 py-2.5 bg-white text-primary font-semibold rounded-full transition-all shadow-md hover:shadow-2xl hover:-translate-y-0.5 border border-primary/20">
                            {{ $coach->cta_text ?? 'Commencer' }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Mobile menu button -->
                    <button
                        x-on:click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-primary focus:outline-none"
                        :aria-expanded="mobileMenuOpen.toString()"
                        aria-controls="coach-site-mobile-menu"
                        aria-label="Ouvrir le menu"
                    >
                        <svg x-cloak x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-cloak x-show="mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div x-cloak class="lg:hidden">
                <div
                    x-show="mobileMenuOpen"
                    x-transition.opacity
                    class="fixed inset-x-0 top-16 bottom-0 z-40 bg-white/80 backdrop-blur-sm"
                    style="display: none;"
                    x-on:click="mobileMenuOpen = false"
                ></div>
                <div
                    id="coach-site-mobile-menu"
                    x-show="mobileMenuOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="fixed inset-x-0 top-16 bottom-0 z-50 border-t border-gray-200 bg-white"
                    style="display: none;"
                >
                    <div class="overflow-y-auto px-4 pt-4 pb-6 space-y-2 bg-white">
                        @foreach($navLinks as $link)
                            <a
                                href="{{ $link['href'] }}"
                                x-on:click="mobileMenuOpen = false"
                                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50"
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                        <div class="pt-2">
                            <a href="#tarifs" class="block w-full text-center px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-all" x-on:click="mobileMenuOpen = false">
                                {{ $coach->cta_text ?? 'Commencer' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    @hasSection('coach-site-footer')
        @yield('coach-site-footer')
    @else
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-12">
                    <!-- À propos -->
                    <div class="lg:col-span-2">
                        @if($coach->hasMedia('logo'))
                            <img src="{{ $coach->getFirstMediaUrl('logo') }}" alt="{{ $coach->name }}" class="h-12 w-auto mb-4 brightness-0 invert">
                        @else
                            <img src="{{ asset('images/unicoach-logo.svg') }}" alt="UNICOACH" class="h-12 w-auto mb-4 brightness-0 invert">
                        @endif
                        <p class="text-gray-400 leading-relaxed">
                            {{ $coach->about_text ? Str::limit(strip_tags($coach->about_text), 200) : 'Coach sportif professionnel, spécialisé dans la transformation physique et le bien-être. Un accompagnement personnalisé pour vous aider à atteindre vos objectifs.' }}
                        </p>
                        
                        <!-- Social Media -->
                        @if($coach->facebook_url || $coach->instagram_url || $coach->twitter_url || $coach->linkedin_url || $coach->youtube_url || $coach->tiktok_url)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-white mb-3">Suivez-moi</h4>
                                <div class="flex space-x-4">
                                    @if($coach->facebook_url)
                                        <a href="{{ $coach->facebook_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($coach->instagram_url)
                                        <a href="{{ $coach->instagram_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683..566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($coach->twitter_url)
                                        <a href="{{ $coach->twitter_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($coach->linkedin_url)
                                        <a href="{{ $coach->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($coach->youtube_url)
                                        <a href="{{ $coach->youtube_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                            </svg>
                                        </a>
                                    @endif
                                    @if($coach->tiktok_url)
                                        <a href="{{ $coach->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Navigation -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Navigation</h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="#accueil" class="text-gray-400 hover:text-white transition-colors inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    Retourner en haut
                                </a>
                            </li>
                            <li>
                                <a href="#contact" class="text-gray-400 hover:text-white transition-colors inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Me contacter
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Informations légales -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Informations légales</h3>
                        <ul class="space-y-3">
                            @php
                                // S'assurer que la relation user est chargée
                                if (!$coach->relationLoaded('user')) {
                                    $coach->load('user');
                                }
                                $vatNumber = $coach->user?->vat_number ?? null;
                            @endphp
                            
                            @if($vatNumber)
                                <li class="text-gray-400">
                                    <span class="text-sm">N° TVA</span>
                                    <p class="font-medium text-white">{{ $vatNumber }}</p>
                                </li>
                            @endif
                            
                            <li>
                                <a href="/mentions-legales" class="text-gray-400 hover:text-white transition-colors inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Mentions légales
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Call to Action -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Prêt à démarrer ?</h3>
                        <p class="text-gray-400 mb-4 leading-relaxed">
                            Commencez votre transformation dès aujourd'hui avec un accompagnement personnalisé.
                        </p>
                        <a href="#contact" class="inline-flex items-center justify-center w-full px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-all shadow-lg hover:shadow-xl">
                            {{ $coach->cta_text ?? 'Me contacter' }}
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="border-t border-gray-800 mt-12 pt-8">
                    <div class="text-center space-y-2">
                        <!-- Copyright -->
                        <div class="text-gray-400 text-sm">
                            <span>&copy; {{ date('Y') }} {{ $coach->name }}. Tous droits réservés.</span>
                        </div>
                        
                        <!-- Branding -->
                        <p class="text-xs text-gray-500">
                            Propulsé par <a href="https://unicoach.app" target="_blank" class="text-primary hover:text-white transition-colors font-medium">UNICOACH</a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    @endif

    <!-- Image Lightbox -->
    <div
        x-show="lightboxOpen"
        x-transition.opacity
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm px-4 py-8"
        style="display: none;"
        aria-modal="true"
        role="dialog"
    >
        <button
            type="button"
            class="absolute inset-0 w-full h-full cursor-zoom-out"
            @click="closeLightbox"
            aria-label="Fermer l'aperçu"
        ></button>

        <div
            x-transition.scale
            class="relative max-w-5xl w-full"
        >
            <div class="absolute -top-10 right-0 flex items-center gap-3 text-white">
                <span x-text="lightboxLabel" class="text-sm uppercase tracking-wide font-semibold"></span>
                <button
                    type="button"
                    class="p-2 rounded-full bg-white/10 hover:bg-white/20 transition"
                    @click="closeLightbox"
                    aria-label="Fermer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <img
                x-show="lightboxSrc"
                x-transition
                :src="lightboxSrc"
                :alt="`Aperçu ${lightboxLabel}`"
                class="w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/20 bg-black"
            >
        </div>
    </div>

    @stack('scripts')
</body>
</html>
