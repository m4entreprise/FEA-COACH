@extends('layouts.coach-site')

@section('content')

<!-- Hero Section -->
<section id="accueil" class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    @if($coach->hasMedia('hero'))
        <div class="absolute inset-0 z-0">
            <img src="{{ $coach->getFirstMediaUrl('hero') }}" 
                 alt="Hero" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/50"></div>
        </div>
    @else
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900"></div>
    @endif

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 leading-tight">
            {{ $coach->hero_title ?? 'Transformez votre corps, transformez votre vie' }}
        </h1>
        <p class="text-xl sm:text-2xl md:text-3xl mb-8 text-gray-200 max-w-3xl mx-auto">
            {{ $coach->hero_subtitle ?? 'Coaching sportif personnalisé pour atteindre vos objectifs' }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#tarifs" class="inline-flex items-center justify-center px-8 py-4 bg-primary text-white text-lg font-bold rounded-lg hover:bg-primary-dark transition-all shadow-xl hover:shadow-2xl transform hover:scale-105">
                {{ $coach->cta_text ?? 'Commencer maintenant' }}
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <a href="#a-propos" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white text-lg font-bold rounded-lg hover:bg-white/20 transition-all border-2 border-white/30">
                En savoir plus
            </a>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
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
                        <div class="text-3xl font-bold text-primary mb-1">{{ $coach->average_rating ?? 5.0 }}★</div>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">1. {{ $coach->method_step1_title ?? 'Évaluation' }}</h3>
                <p class="text-gray-600">{{ $coach->method_step1_description ?? 'Bilan complet de votre condition physique et définition de vos objectifs personnalisés.' }}</p>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">2. {{ $coach->method_step2_title ?? 'Programme' }}</h3>
                <p class="text-gray-600">{{ $coach->method_step2_description ?? 'Plan d\'entraînement sur mesure adapté à votre niveau et vos disponibilités.' }}</p>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">3. {{ $coach->method_step3_title ?? 'Suivi' }}</h3>
                <p class="text-gray-600">{{ $coach->method_step3_description ?? 'Accompagnement régulier et ajustements constants pour garantir vos progrès.' }}</p>
            </div>
        </div>
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

        @if(isset($plans) && $plans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min($plans->count(), 4) }} gap-8">
                @foreach($plans as $plan)
                    <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-100 hover:border-primary transition-all p-8 flex flex-col">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ $plan->name }}</h3>
                        <div class="text-4xl font-extrabold text-primary mb-4">
                            @if($plan->price)
                                {{ number_format($plan->price, 2, ',', ' ') }}€
                            @else
                                <span class="text-2xl">Prix sur demande</span>
                            @endif
                        </div>
                        <p class="text-gray-600 mb-6 flex-grow">{{ $plan->description }}</p>
                        
                        @if($plan->cta_url)
                            <a href="{{ $plan->cta_url }}" 
                               target="_blank"
                               class="block w-full text-center px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition-all">
                                Choisir cette formule
                            </a>
                        @else
                            <a href="#contact" 
                               class="block w-full text-center px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition-all">
                                Me contacter
                            </a>
                        @endif
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
                    <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                        <!-- Before/After Images -->
                        <div class="grid grid-cols-2">
                            <div class="relative group">
                                @if($transformation->hasMedia('before'))
                                    <img src="{{ $transformation->getFirstMediaUrl('before') }}" 
                                         alt="Avant" 
                                         class="w-full h-64 object-cover">
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
                                @if($transformation->hasMedia('after'))
                                    <img src="{{ $transformation->getFirstMediaUrl('after') }}" 
                                         alt="Après" 
                                         class="w-full h-64 object-cover">
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
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
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
                            <svg class="w-5 h-5 text-gray-500 transition-transform" 
                                 :class="{ 'transform rotate-180': openFaq === {{ $index + 1 }} }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openFaq === {{ $index + 1 }}" 
                             x-transition
                             class="px-6 pb-4 text-gray-600"
                             style="display: none;">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-4 text-gray-600">Aucune question fréquente pour le moment.</p>
            </div>
        @endif
    </div>
</section>

<!-- Contact/CTA Section -->
<section id="contact" class="py-20 text-white" style="background: linear-gradient(to bottom right, {{ $coach->color_primary ?? '#3B82F6' }}, {{ $coach->color_secondary ?? '#10B981' }});">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold mb-6 text-white">
            {{ $coach->final_cta_title ?? 'Prêt à commencer votre transformation ?' }}
        </h2>
        <p class="text-xl mb-8 text-white opacity-90">
            {{ $coach->final_cta_subtitle ?? 'Ne laissez pas vos objectifs être de simples rêves. Agissez maintenant !' }}
        </p>
        <a href="#tarifs" class="inline-flex items-center px-8 py-4 bg-white text-lg font-bold rounded-lg hover:bg-gray-100 transition-all shadow-xl" style="color: {{ $coach->color_primary ?? '#3B82F6' }};">
            {{ $coach->cta_text ?? 'Découvrir les formules' }}
            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</section>

@endsection
