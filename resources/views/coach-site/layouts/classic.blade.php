@extends('layouts.coach-site')

@php
    use Illuminate\Support\Str;
    use Stevebauman\Purify\Facades\Purify;
@endphp

@push('styles')
    <style>
        @keyframes heroBackgroundReveal {
            0% {
                opacity: 0;
                transform: scale(1.08);
                filter: blur(6px);
            }
            100% {
                opacity: 1;
                transform: scale(1);
                filter: blur(0);
            }
        }

        @keyframes heroFadeUp {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes heroPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.25);
            }
            70% {
                box-shadow: 0 0 0 12px rgba(255, 255, 255, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        .hero-background-animate {
            animation: heroBackgroundReveal 1.4s ease forwards;
        }

        .hero-fade-seq {
            opacity: 0;
            transform: translateY(40px);
            animation: heroFadeUp 0.9s ease forwards;
        }

        .hero-fade-seq.delay-1 {
            animation-delay: 0.15s;
        }

        .hero-fade-seq.delay-2 {
            animation-delay: 0.35s;
        }

        .hero-fade-seq.delay-3 {
            animation-delay: 0.5s;
        }

        .hero-cta-animated {
            position: relative;
            overflow: hidden;
        }

        .hero-cta-animated::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 1px solid rgba(255, 255, 255, 0.3);
            opacity: 0;
            animation: heroPulse 2.4s ease infinite;
        }

        @media (prefers-reduced-motion: reduce) {
            .hero-background-animate,
            .hero-fade-seq,
            .hero-cta-animated::after {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
            }
        }
    </style>
@endpush

@section('content')

<!-- Hero Section -->
<section id="accueil" class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    @if($coach->hasMedia('hero'))
        <div class="absolute inset-0 z-0 hero-background-animate">
            <img src="{{ $coach->getFirstMediaUrl('hero') }}"
                 alt="Hero"
                 class="w-full h-full object-cover hero-background-animate">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/50 hero-background-animate"></div>
        </div>
    @else
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 hero-background-animate"></div>
    @endif

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 leading-tight hero-fade-seq">
            {{ $coach->hero_title ?? 'Transformez votre corps, transformez votre vie' }}
        </h1>
        <p class="text-xl sm:text-2xl md:text-3xl mb-8 text-gray-200 max-w-3xl mx-auto hero-fade-seq delay-1">
            {{ $coach->hero_subtitle ?? 'Coaching sportif personnalisé pour atteindre vos objectifs' }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center hero-fade-seq delay-2">
            <a href="#tarifs" class="inline-flex items-center justify-center px-8 py-4 bg-primary text-white text-lg font-bold rounded-lg hover:bg-primary-dark transition-all shadow-xl hover:shadow-2xl transform hover:scale-105 hero-cta-animated">
                {{ $coach->cta_text ?? 'Commencer maintenant' }}
                <x-lucide-arrow-right class="ml-2 w-5 h-5" />
            </a>
            <a href="#a-propos" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white text-lg font-bold rounded-lg hover:bg-white/20 transition-all border-2 border-white/30 hero-cta-animated">
                En savoir plus
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce z-20">
        <x-lucide-chevrons-down class="w-6 h-6 text-white" />
    </div>
</section>

<!-- About Section -->
<section id="a-propos" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Image -->
            <div class="relative">
                @if($coach->hasMedia('profile'))
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                        <img src="{{ $coach->getFirstMediaUrl('profile') }}" 
                             alt="{{ $coach->name }}" 
                             class="w-full h-auto object-cover aspect-square">
                    </div>
                @else
                    <div class="aspect-square bg-gradient-to-br from-primary to-secondary rounded-2xl shadow-2xl flex items-center justify-center">
                        <span class="text-6xl font-bold text-white">{{ substr($coach->name, 0, 1) }}</span>
                    </div>
                @endif
                
                <!-- Decorative Element -->
                <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-secondary rounded-full opacity-20 blur-2xl"></div>
            </div>

            <!-- Content -->
            <div>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                    À propos de {{ $coach->name }}
                </h2>
                <div class="prose prose-lg text-gray-600 mb-6">
                    {!! nl2br(e($coach->about_text ?? 'Coach sportif certifié avec plusieurs années d\'expérience dans l\'accompagnement personnalisé. Spécialisé dans la transformation physique, la perte de poids et le renforcement musculaire.')) !!}
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 mt-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary mb-1">{{ isset($transformations) ? $transformations->count() : 0 }}+</div>
                        <div class="text-sm text-gray-600">Transformations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary mb-1">{{ $coach->satisfaction_rate ?? 100 }}%</div>
                        <div class="text-sm text-gray-600">Satisfaits</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary mb-1">{{ $coach->average_rating ?? 5.0 }} ★</div>
                        <div class="text-sm text-gray-600">Note moyenne</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Method Section -->
<section id="methode" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                {{ $coach->method_title ?? 'Ma méthode de coaching' }}
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                {{ $coach->method_subtitle ?? 'Une approche personnalisée et scientifique pour des résultats durables' }}
            </p>
        </div>

        @if($coach->method_text)
            <div class="prose prose-lg max-w-4xl mx-auto text-gray-600 mb-12">
                {!! nl2br(e($coach->method_text)) !!}
            </div>
        @endif

        <!-- Method Steps -->
        <div class="relative mt-12">
            <div class="pointer-events-none absolute inset-0 mx-auto max-w-5xl rounded-3xl bg-gradient-to-r from-primary/10 via-white to-secondary/10 blur-3xl opacity-70"></div>

            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="relative bg-white/90 border border-gray-100 rounded-2xl p-8 shadow-lg shadow-gray-200/60 backdrop-blur-sm transition-transform hover:-translate-y-1">
                    <span class="hidden md:block absolute top-12 -right-6 w-12 h-px bg-gradient-to-r from-primary/40 to-transparent"></span>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-primary/10 text-primary text-2xl font-bold flex items-center justify-center">
                            1
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-primary/70">Étape 1</p>
                            <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $coach->method_step1_title ?? 'Évaluation' }}</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">{{ $coach->method_step1_description ?? 'Bilan complet de votre condition physique et définition de vos objectifs personnalisés.' }}</p>
                    <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-3 py-1 text-xs font-semibold text-primary">
                        <x-lucide-badge-check class="w-4 h-4" />
                        Diagnostic précis
                    </div>
                </div>

                <div class="relative bg-white/90 border border-gray-100 rounded-2xl p-8 shadow-lg shadow-gray-200/60 backdrop-blur-sm transition-transform hover:-translate-y-1">
                    <span class="hidden md:block absolute top-12 -right-6 w-12 h-px bg-gradient-to-r from-primary/40 to-transparent"></span>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-primary/10 text-primary text-2xl font-bold flex items-center justify-center">
                            2
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-primary/70">Étape 2</p>
                            <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $coach->method_step2_title ?? 'Programme' }}</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">{{ $coach->method_step2_description ?? 'Plan d\'entraînement sur mesure adapté à votre niveau et vos disponibilités.' }}</p>
                    <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-3 py-1 text-xs font-semibold text-primary">
                        <x-lucide-route class="w-4 h-4" />
                        Feuille de route claire
                    </div>
                </div>

                <div class="relative bg-white/90 border border-gray-100 rounded-2xl p-8 shadow-lg shadow-gray-200/60 backdrop-blur-sm transition-transform hover:-translate-y-1">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-primary/10 text-primary text-2xl font-bold flex items-center justify-center">
                            3
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-primary/70">Étape 3</p>
                            <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $coach->method_step3_title ?? 'Suivi' }}</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">{{ $coach->method_step3_description ?? 'Accompagnement régulier et ajustements constants pour garantir vos progrès.' }}</p>
                    <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/5 px-3 py-1 text-xs font-semibold text-primary">
                        <x-lucide-refresh-cw class="w-4 h-4" />
                        Ajustements continus
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 text-white" style="background: linear-gradient(to bottom right, {{ $coach->color_primary ?? '#3B82F6' }}, {{ $coach->color_secondary ?? '#10B981' }});">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold mb-6">
            {{ $coach->intermediate_cta_title ?? 'Prêt à transformer votre corps et votre vie ?' }}
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            {{ $coach->intermediate_cta_subtitle ?? 'Ne restez pas seul face à vos objectifs. Bénéficiez d\'un accompagnement personnalisé qui vous mènera au succès.' }}
        </p>
        <a href="#tarifs" class="inline-flex items-center justify-center px-8 py-4 bg-white text-lg font-bold rounded-lg hover:bg-gray-100 transition-all shadow-xl hover:shadow-2xl transform hover:scale-105" style="color: {{ $coach->color_primary ?? '#3B82F6' }};">
            {{ $coach->cta_text ?? 'Commencer maintenant' }}
            <x-lucide-arrow-right class="ml-2 w-5 h-5" />
        </a>
    </div>
</section>

<!-- Pricing Section -->
<section id="tarifs" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                {{ $coach->pricing_title ?? 'Mes formules de coaching' }}
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                {{ $coach->pricing_subtitle ?? 'Choisissez la formule qui correspond le mieux à vos objectifs' }}
            </p>
        </div>

        @if(isset($services) && $services->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min($services->count(), 4) }} gap-8">
                @foreach($services as $service)
                    <div class="relative bg-white rounded-2xl shadow-xl border-2 border-gray-100 hover:border-primary transition-all flex flex-col overflow-hidden">
                        @if($service->is_featured)
                            <span class="absolute top-6 right-6 inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-semibold uppercase tracking-wide px-3 py-1 shadow-lg">
                                <x-lucide-star class="w-3.5 h-3.5" />
                                Populaire
                            </span>
                        @endif
                        <div class="relative">
                            @if($service->image_url)
                                <div class="h-48 w-full overflow-hidden">
                                    <img
                                        src="{{ $service->image_url }}"
                                        alt="Illustration {{ $service->name }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    >
                                </div>
                            @else
                                <div class="h-48 w-full bg-gray-100 border border-dashed border-gray-200 flex items-center justify-center text-gray-400 text-sm">
                                    Image à venir
                                </div>
                            @endif
                            <div class="absolute inset-x-6 -bottom-8 bg-white rounded-2xl shadow-lg border border-gray-100 p-4 flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Tarif</p>
                                    <p class="text-2xl font-extrabold text-gray-900">
                                        {{ number_format($service->price, 2, ',', ' ') }}
                                        <span class="text-sm text-gray-500">{{ $service->currency }}</span>
                                    </p>
                                </div>
                                @if($service->duration_minutes)
                                    <div class="text-right">
                                        <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Durée</p>
                                        <p class="text-lg font-bold text-primary">{{ $service->duration_minutes }} min</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="p-8 pt-12 flex-1 flex flex-col">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ $service->name }}</h3>

                            @if($service->description)
                                <div class="prose prose-sm text-gray-600 mb-6 flex-grow leading-relaxed [&_ul]:list-disc [&_ul]:pl-5 [&_li]:my-1">
                                    {!! Purify::clean($service->description) !!}
                                </div>
                            @endif
                        
                            @if(optional($coach->user)->has_payments_module && $service->booking_enabled)
                                <a href="{{ route('coach.booking.checkout.form', ['coach_slug' => $coach->slug, 'serviceId' => $service->id]) }}"
                                   class="block w-full text-center px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition-all">
                                    Payer en ligne
                                </a>
                            @else
                                <a href="#contact" 
                                   class="block w-full text-center px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition-all">
                                    Me contacter
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-600">Les formules de coaching seront bientôt disponibles.</p>
            </div>
        @endif
    </div>
</section>

<!-- Results/Transformations Section -->
<section id="resultats" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                {{ $coach->transformations_title ?? 'Leurs transformations' }}
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                {{ $coach->transformations_subtitle ?? 'Des résultats réels de personnes comme vous' }}
            </p>
        </div>

        @if(isset($transformations) && $transformations->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($transformations as $transformation)
                    @php
                        $beforeUrl = $transformation->hasMedia('before') ? $transformation->getFirstMediaUrl('before') : null;
                        $afterUrl = $transformation->hasMedia('after') ? $transformation->getFirstMediaUrl('after') : null;
                    @endphp
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                        <!-- Before/After Images -->
                        <div class="grid grid-cols-2">
                            <div class="relative group">
                                @if($beforeUrl)
                                    <button
                                        type="button"
                                        class="relative block w-full h-64 focus:outline-none cursor-zoom-in"
                                        @click="openLightbox('{{ addslashes($beforeUrl) }}', 'Avant')"
                                        aria-label="Voir la photo avant en grand"
                                    >
                                        <img src="{{ $beforeUrl }}" 
                                             alt="Avant" 
                                             class="w-full h-64 object-cover">
                                        <span class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold tracking-wide">
                                            Cliquer pour agrandir
                                        </span>
                                    </button>
                                @else
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">Avant</span>
                                    </div>
                                @endif
                                <div class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                    AVANT
                                </div>
                            </div>
                            <div class="relative group">
                                @if($afterUrl)
                                    <button
                                        type="button"
                                        class="relative block w-full h-64 focus:outline-none cursor-zoom-in"
                                        @click="openLightbox('{{ addslashes($afterUrl) }}', 'Après')"
                                        aria-label="Voir la photo après en grand"
                                    >
                                        <img src="{{ $afterUrl }}" 
                                             alt="Après" 
                                             class="w-full h-64 object-cover">
                                        <span class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold tracking-wide">
                                            Cliquer pour agrandir
                                        </span>
                                    </button>
                                @else
                                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">Après</span>
                                    </div>
                                @endif
                                <div class="absolute top-2 right-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                                    APRÈS
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $transformation->title ?? 'Transformation' }}</h3>
                            @if($transformation->description)
                                <p class="text-gray-600 text-sm">{{ $transformation->description }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-600">Les transformations seront bientôt disponibles.</p>
            </div>
        @endif

        <!-- CTA -->
        <div class="text-center mt-12">
            <p class="text-xl text-gray-900 mb-6 font-semibold">Prêt à obtenir les mêmes résultats ?</p>
            <a href="#tarifs" class="inline-flex items-center px-8 py-4 bg-primary text-white text-lg font-bold rounded-lg hover:bg-primary-dark transition-all shadow-xl">
                {{ $coach->cta_text ?? 'Commencer maintenant' }}
                <x-lucide-arrow-right class="ml-2 w-5 h-5" />
            </a>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Questions fréquentes
            </h2>
            <p class="text-xl text-gray-600">
                Tout ce que vous devez savoir avant de commencer
            </p>
        </div>

        @if($faqs && $faqs->count() > 0)
            <div x-data="{ openFaq: null }" class="space-y-4">
                @foreach($faqs as $index => $faq)
                    <!-- FAQ Item {{ $index + 1 }} -->
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <button @click="openFaq = openFaq === {{ $index + 1 }} ? null : {{ $index + 1 }}" 
                                class="w-full text-left px-6 py-4 flex justify-between items-center hover:bg-gray-100 transition-colors">
                            <span class="font-semibold text-gray-900">{{ $faq->question }}</span>
                            <x-lucide-chevron-down class="w-5 h-5 text-gray-500 transition-transform"
                                x-bind:class="{ 'transform rotate-180': openFaq === {{ $index + 1 }} }" />
                        </button>
                        <div x-show="openFaq === {{ $index + 1 }}" 
                             x-transition
                             class="px-6 pb-4 pt-4 text-gray-600"
                             style="display: none;">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <x-lucide-circle-help class="mx-auto h-12 w-12 text-gray-400" />
                <p class="mt-4 text-gray-600">Aucune question fréquente pour le moment.</p>
            </div>
        @endif
    </div>
</section>

<!-- Contact/CTA Section -->
<section id="contact" class="py-20 text-white" style="background: linear-gradient(to bottom right, {{ $coach->color_primary ?? '#3B82F6' }}, {{ $coach->color_secondary ?? '#10B981' }});">
    <div
        class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="{ submitted: false, successMessage: '', loading: false }"
    >
        <div class="mb-10 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4 text-white">
                {{ $coach->final_cta_title ?? 'Prêt à commencer votre transformation ?' }}
            </h2>
            <p class="text-lg sm:text-xl mb-4 text-white/90">
                {{ $coach->final_cta_subtitle ?? 'Ne laissez pas vos objectifs être de simples rêves. Agissez maintenant !' }}
            </p>
            <p class="text-sm text-white/80">
                Remplissez le formulaire ci-dessous, le coach recevra votre message directement dans son tableau de bord.
            </p>
        </div>

        @if (session('success'))
            <div
                class="mb-6 rounded-lg bg-white/10 border border-white/20 px-4 py-3 text-sm"
                x-show="!submitted"
            >
                <div class="flex items-center">
                    <x-lucide-badge-check class="h-5 w-5 text-emerald-200" />
                    <span class="ml-3 text-emerald-50">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-white/10 border border-red-300/40 px-4 py-3 text-sm" x-show="!submitted">
                <div class="flex items-start">
                    <x-lucide-alert-triangle class="h-5 w-5 text-red-200 mt-0.5" />
                    <ul class="ml-3 space-y-1 text-red-50">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Message de succès en AJAX (remplace le formulaire après envoi) -->
        <div
            class="mb-6 rounded-2xl bg-white/10 border border-emerald-300/60 px-4 py-4 text-sm sm:text-base"
            x-show="submitted"
            x-transition
        >
            <div class="flex items-start">
                <x-lucide-badge-check class="h-6 w-6 text-emerald-200 mt-0.5" />
                <div class="ml-3">
                    <p class="font-semibold text-emerald-50">Message envoyé avec succès</p>
                    <p class="mt-1 text-emerald-100" x-text="successMessage || 'Votre message a bien été envoyé. Le coach vous répondra au plus vite.'"></p>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden bg-white/10 backdrop-blur-sm rounded-2xl shadow-2xl p-6 sm:p-8 border border-white/20" x-show="!submitted" x-transition>
            <div class="pointer-events-none absolute -top-12 -right-6 w-48 h-48 rounded-full bg-white/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-10 -left-10 w-64 h-64 rounded-full bg-gradient-to-tr from-white/5 via-transparent to-white/10 blur-2xl"></div>
            <form
                method="POST"
                action="/contact"
                class="relative space-y-8"
                @submit.prevent="
                    loading = true;
                    fetch('/contact', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            name: $refs.name.value,
                            email: $refs.email.value,
                            phone: $refs.phone.value,
                            message: $refs.message.value,
                        }),
                    })
                        .then(async (response) => {
                            if (!response.ok) {
                                throw new Error('Request failed');
                            }
                            const data = await response.json();
                            successMessage = data.message || '';
                            submitted = true;
                        })
                        .catch(() => {
                            // En cas d'erreur, garder le fallback : le coach sera contacté via la soumission classique
                            loading = false;
                        })
                        .finally(() => {
                            loading = false;
                        });
                "
            >
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto_1fr] gap-8">
                    <div class="space-y-5">
                        <div class="flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.32em] text-white/70">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 bg-white/10 text-white text-sm leading-none tracking-normal">
                                1
                            </span>
                            <span>Vos coordonnées</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label for="name" class="flex items-center text-sm font-semibold text-white/90 gap-2">
                                    <x-lucide-user class="w-4 h-4 text-white/70" />
                                    Nom complet *
                                </label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        x-ref="name"
                                        required
                                        autocomplete="name"
                                        value="{{ old('name') }}"
                                        class="peer block w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 pl-11 text-white placeholder-white/50 shadow-[0_10px_25px_rgba(0,0,0,0.15)] backdrop-blur focus:border-white/80 focus:outline-none focus:ring-2 focus:ring-white/80"
                                        placeholder="Votre nom complet"
                                    >
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/50">
                                        <x-lucide-id-card class="w-5 h-5" />
                                    </span>
                                </div>
                                <p class="text-xs text-white/60">Utilisez votre prénom et nom pour une réponse personnalisée.</p>
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="flex items-center text-sm font-semibold text-white/90 gap-2">
                                    <x-lucide-phone class="w-4 h-4 text-white/70" />
                                    Téléphone *
                                </label>
                                <div class="relative">
                                    <input
                                        type="tel"
                                        id="phone"
                                        name="phone"
                                        x-ref="phone"
                                        required
                                        autocomplete="tel"
                                        value="{{ old('phone') }}"
                                        class="peer block w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 pl-11 text-white placeholder-white/50 shadow-[0_10px_25px_rgba(0,0,0,0.15)] backdrop-blur focus:border-white/80 focus:outline-none focus:ring-2 focus:ring-white/80"
                                        placeholder="+32 4 12 34 56 78"
                                    >
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/50">
                                        <x-lucide-phone-call class="w-5 h-5" />
                                    </span>
                                </div>
                                <p class="text-xs text-white/60">Indiquez un numéro joignable afin que le coach puisse vous rappeler rapidement.</p>
                            </div>

                            <div class="sm:col-span-2 space-y-2">
                                <label for="email" class="flex items-center text-sm font-semibold text-white/90 gap-2">
                                    <x-lucide-mail class="w-4 h-4 text-white/70" />
                                    Email *
                                </label>
                                <div class="relative">
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        x-ref="email"
                                        required
                                        autocomplete="email"
                                        value="{{ old('email') }}"
                                        class="peer block w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 pl-11 text-white placeholder-white/50 shadow-[0_10px_25px_rgba(0,0,0,0.15)] backdrop-blur focus:border-white/80 focus:outline-none focus:ring-2 focus:ring-white/80"
                                        placeholder="vous@example.com"
                                    >
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/50">
                                        <x-lucide-inbox class="w-5 h-5" />
                                    </span>
                                </div>
                                <p class="text-xs text-white/60">Votre email restera confidentiel, il sert uniquement à la prise de contact.</p>
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:flex items-stretch justify-center">
                        <span class="w-px bg-gradient-to-b from-white/10 via-white/40 to-white/10"></span>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.32em] text-white/70">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/30 bg-white/10 text-white text-sm leading-none tracking-normal">
                                2
                            </span>
                            <span>Votre message</span>
                        </div>
                        <label for="message" class="sr-only">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            x-ref="message"
                            rows="6"
                            required
                            class="w-full rounded-2xl border border-white/25 bg-white/10 px-4 py-4 text-base text-white placeholder-white/60 shadow-[0_20px_45px_rgba(0,0,0,0.25)] focus:border-white/80 focus:outline-none focus:ring-2 focus:ring-white/80"
                            placeholder="Parlez de vos objectifs, de votre niveau actuel, de vos disponibilités..."
                        >{{ old('message') }}</textarea>
                        <div class="flex flex-wrap gap-3 text-[11px] uppercase tracking-wide text-white/70">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-3 py-1">
                                <x-lucide-mail-check class="w-3.5 h-3.5 text-white/70" />
                                Réponse sous 24h
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/5 px-3 py-1">
                                <x-lucide-sparkles class="w-3.5 h-3.5 text-white/70" />
                                Session découverte offerte
                            </span>
                        </div>
                        <p class="text-xs text-white/70">
                            En envoyant ce message, vous serez recontacté(e) par le coach pour discuter de votre situation et des prochaines étapes.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center px-8 py-3 bg-white text-sm sm:text-base font-bold rounded-xl shadow-lg shadow-black/20 hover:-translate-y-0.5 hover:bg-gray-100 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
                        style="color: {{ $coach->color_primary ?? '#3B82F6' }};"
                        :disabled="loading"
                    >
                        <span x-show="!loading">Envoyer mon message</span>
                        <span x-show="loading">Envoi en cours...</span>
                        <x-lucide-send class="ml-2 w-5 h-5" />
                    </button>
                    <p class="text-xs text-white/70 sm:text-right">
                        Réponse généralement sous 24 à 48h.
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@section('coach-site-footer')
    <footer class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-black text-white">
        <div class="absolute inset-0 opacity-50" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08), transparent 40%), radial-gradient(circle at 80% 0%, rgba(255,255,255,0.04), transparent 35%);"></div>
        <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-transparent via-white/5 to-transparent blur-2xl opacity-70"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <div class="lg:col-span-5 space-y-6">
                    <div class="space-y-3">
                        <p class="text-sm uppercase tracking-[0.3em] text-white/70">Coaching sportif</p>
                        <h3 class="text-3xl font-extrabold leading-tight">
                            {{ $coach->name }}
                        </h3>
                    </div>
                    <p class="text-white/70 leading-relaxed">
                        {{ $coach->about_text ? Str::limit(strip_tags($coach->about_text), 180) : 'Accompagnement complet mêlant entraînement, nutrition et mindset pour atteindre vos objectifs avec constance et sérénité.' }}
                    </p>

                    @if($coach->facebook_url || $coach->instagram_url || $coach->twitter_url || $coach->linkedin_url || $coach->youtube_url || $coach->tiktok_url)
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            @foreach ([
                                ['url' => $coach->facebook_url, 'label' => 'Facebook', 'icon' => '<path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>' ],
                                ['url' => $coach->instagram_url, 'label' => 'Instagram', 'icon' => '<rect x="2" y="2" width="20" height="20" rx="6" ry="6" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 8.5a3.5 3.5 0 1 0 3.5 3.5A3.5 3.5 0 0 0 12 8.5z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.25" fill="currentColor"/>' ],
                                ['url' => $coach->twitter_url, 'label' => 'Twitter', 'icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>' ],
                                ['url' => $coach->linkedin_url, 'label' => 'LinkedIn', 'icon' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>' ],
                                ['url' => $coach->youtube_url, 'label' => 'YouTube', 'icon' => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>' ],
                                ['url' => $coach->tiktok_url, 'label' => 'TikTok', 'icon' => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>' ],
                            ] as $social)
                                @if($social['url'])
                                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="group inline-flex items-center justify-center h-11 w-11 rounded-full border border-white/20 bg-white/5 hover:bg-white/15 transition-all" aria-label="{{ $social['label'] }}">
                                        <svg class="h-5 w-5 text-white/80 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">{!! $social['icon'] !!}</svg>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                    <div class="space-y-4">
                        <p class="text-sm uppercase tracking-[0.35em] text-white/60">Navigation</p>
                        <ul class="space-y-2 text-white/80">
                            @foreach ([
                                ['label' => 'Accueil', 'anchor' => '#accueil'],
                                ['label' => 'À propos', 'anchor' => '#a-propos'],
                                ['label' => 'Méthode', 'anchor' => '#methode'],
                                ['label' => 'Tarifs', 'anchor' => '#tarifs'],
                                ['label' => 'Résultats', 'anchor' => '#resultats'],
                                ['label' => 'Contact', 'anchor' => '#contact'],
                            ] as $link)
                                <li>
                                    <a href="{{ $link['anchor'] }}" class="inline-flex items-center gap-2 text-sm font-medium hover:text-white transition-colors">
                                        <span class="h-1 w-1 rounded-full bg-white/50"></span>
                                        {{ $link['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <p class="text-sm uppercase tracking-[0.35em] text-white/60">Contact express</p>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 space-y-3 text-white/80">
                            <p class="text-sm text-white/60">Email</p>
                            <a href="mailto:{{ optional($coach->user)->email ?? 'contact@unicoach.app' }}" class="text-lg font-semibold text-white hover:text-primary transition-colors">
                                {{ optional($coach->user)->email ?? 'contact@unicoach.app' }}
                            </a>
                            <hr class="border-white/10">
                            <p class="text-sm text-white/60">Réservations</p>
                            <a href="#tarifs" class="inline-flex items-center justify-center rounded-2xl border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white hover:text-gray-900 transition-all">
                                {{ $coach->cta_text ?? 'Je réserve ma séance' }}
                            </a>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-sm uppercase tracking-[0.35em] text-white/60">Légal</p>
                        <ul class="space-y-3 text-white/80">
                            @php
                                if (!$coach->relationLoaded('user')) {
                                    $coach->load('user');
                                }
                                $vatNumber = $coach->user?->vat_number ?? null;
                            @endphp
                            @if($vatNumber)
                                <li class="space-y-1">
                                    <span class="text-xs uppercase tracking-wide text-white/50">N° TVA</span>
                                    <p class="font-semibold">{{ $vatNumber }}</p>
                                </li>
                            @endif
                            <li>
                                <a href="/mentions-legales" class="inline-flex items-center gap-2 text-sm font-semibold hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Mentions légales
                                </a>
                            </li>
                            <li class="text-sm text-white/60 leading-relaxed">
                                Propulsé par <a href="https://unicoach.app" target="_blank" class="font-semibold text-white hover:text-primary transition-colors">UNICOACH</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-center justify-between gap-4 border-t border-white/10 pt-8 text-sm text-white/60">
                <p>&copy; {{ date('Y') }} {{ $coach->name }} — Tous droits réservés.</p>
                <div class="inline-flex items-center gap-3 text-white/70">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Coaching sur-mesure • Suivi nutritionnel • Mindset & performance
                </div>
            </div>
        </div>
    </footer>
@endsection
