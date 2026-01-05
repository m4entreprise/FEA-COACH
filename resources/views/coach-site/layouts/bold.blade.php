@extends('layouts.coach-site')

@php
    use Illuminate\Support\Str;
    use Stevebauman\Purify\Facades\Purify;
@endphp

@section('content')

{{-- Layout Bold/Impact - Version très visuelle avec de grosses sections hero --}}

<!-- Hero Section - Bold/Impact -->
<section id="accueil" class="relative min-h-screen flex items-center justify-center overflow-hidden">
    @if($coach->hasMedia('hero'))
        <div class="absolute inset-0 z-0">
            <img src="{{ $coach->getFirstMediaUrl('hero') }}" alt="Hero" class="w-full h-full object-cover scale-110">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/90 via-primary/70 to-secondary/80"></div>
        </div>
    @else
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-primary via-primary-dark to-secondary"></div>
    @endif

    <div class="absolute inset-0 z-[1] bg-gradient-to-b from-slate-950/40 via-slate-950/65 to-slate-950/80"></div>

    <div class="absolute inset-0 z-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 text-center text-white">
        <div class="mb-8 inline-block px-6 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold uppercase tracking-wide">
            {{ $coach->name }}
        </div>
        
        <h1 class="text-6xl sm:text-7xl md:text-8xl font-black mb-8 leading-none">
            {{ $coach->hero_title ?? 'Transformez votre corps, transformez votre vie' }}
        </h1>
        
        <p class="text-2xl sm:text-3xl md:text-4xl mb-12 text-white/90 font-bold max-w-4xl mx-auto">
            {{ $coach->hero_subtitle ?? 'Coaching sportif personnalisé' }}
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-8 max-w-3xl mx-auto mb-12">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 sm:p-6 border border-white/20 text-center sm:text-left">
                <div class="text-4xl sm:text-5xl font-black mb-1 sm:mb-2">{{ isset($transformations) ? $transformations->count() : 0 }}+</div>
                <div class="text-xs sm:text-sm uppercase tracking-wider">Clients</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 sm:p-6 border border-white/20 text-center sm:text-left">
                <div class="text-4xl sm:text-5xl font-black mb-1 sm:mb-2">{{ $coach->satisfaction_rate ?? 100 }}%</div>
                <div class="text-xs sm:text-sm uppercase tracking-wider">Satisfaction</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-5 sm:p-6 border border-white/20 text-center sm:text-left">
                <div class="text-4xl sm:text-5xl font-black mb-1 sm:mb-2">{{ $coach->average_rating ?? 5.0 }}</div>
                <div class="text-xs sm:text-sm uppercase tracking-wider">Note</div>
            </div>
        </div>

        <a href="#tarifs" class="inline-flex items-center px-12 py-5 bg-white text-primary text-xl font-black rounded-full hover:bg-gray-100 transition-all shadow-2xl transform hover:scale-110">
            {{ $coach->cta_text ?? 'Commencer maintenant' }}
            <svg class="ml-3 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-12 left-1/2 transform -translate-x-1/2 animate-bounce z-20">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</section>

<!-- About Section - Bold -->
<section id="a-propos" class="py-32 bg-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-1/3 h-full opacity-5" style="background: linear-gradient(to bottom right, {{ $coach->color_primary ?? '#3B82F6' }}, {{ $coach->color_secondary ?? '#10B981' }});"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="relative">
                @if($coach->hasMedia('profile'))
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl transform hover:scale-105 transition-transform duration-500">
                        <img src="{{ $coach->getFirstMediaUrl('profile') }}" alt="{{ $coach->name }}" class="w-full h-auto object-cover aspect-square">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    </div>
                @else
                    <div class="aspect-square bg-gradient-to-br from-primary to-secondary rounded-3xl shadow-2xl flex items-center justify-center transform hover:scale-105 transition-transform duration-500">
                        <span class="text-9xl font-black text-white">{{ substr($coach->name, 0, 1) }}</span>
                    </div>
                @endif
                
                <div class="absolute -top-8 -left-8 w-32 h-32 bg-secondary rounded-full opacity-20 blur-2xl"></div>
                <div class="absolute -bottom-8 -right-8 w-40 h-40 bg-primary rounded-full opacity-20 blur-3xl"></div>
            </div>

            <div>
                <div class="inline-block px-4 py-2 bg-primary/10 rounded-full text-primary font-bold uppercase text-sm tracking-wide mb-6">
                    Votre coach
                </div>
                
                <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6 leading-tight">
                    {{ $coach->name }}
                </h2>
                
                <div class="text-xl text-gray-700 leading-relaxed space-y-4 mb-8">
                    {!! nl2br(e($coach->about_text ?? 'Coach sportif certifié avec plusieurs années d\'expérience.')) !!}
                </div>

                <a href="#contact" class="inline-flex items-center px-8 py-4 bg-primary text-white text-lg font-bold rounded-full hover:bg-primary-dark transition-all shadow-lg">
                    Me contacter
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Method Section - Bold -->
<section id="methode" class="py-32 relative overflow-hidden" style="background: linear-gradient(135deg, {{ $coach->color_primary ?? '#3B82F6' }} 0%, {{ $coach->color_secondary ?? '#10B981' }} 100%);">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-20 text-white">
            <div class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full font-bold uppercase text-sm tracking-wide mb-6">
                Ma méthode
            </div>
            <h2 class="text-5xl md:text-6xl font-black mb-6">
                {{ $coach->method_title ?? 'Ma méthode' }}
            </h2>
            <p class="text-2xl text-white/90 max-w-3xl mx-auto font-semibold">
                {{ $coach->method_subtitle ?? 'Une approche personnalisée' }}
            </p>
        </div>

        @if($coach->method_text)
            <div class="text-xl text-white/90 leading-relaxed text-center max-w-4xl mx-auto mb-16">
                {!! nl2br(e($coach->method_text)) !!}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border-2 border-white/20 hover:border-white/40 transition-all transform hover:scale-105">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-8 shadow-xl">
                    <span class="text-4xl font-black text-primary">1</span>
                </div>
                <h3 class="text-3xl font-black text-white mb-4">{{ $coach->method_step1_title ?? 'Évaluation' }}</h3>
                <p class="text-lg text-white/80">{{ $coach->method_step1_description ?? 'Bilan complet' }}</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border-2 border-white/20 hover:border-white/40 transition-all transform hover:scale-105">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-8 shadow-xl">
                    <span class="text-4xl font-black text-primary">2</span>
                </div>
                <h3 class="text-3xl font-black text-white mb-4">{{ $coach->method_step2_title ?? 'Programme' }}</h3>
                <p class="text-lg text-white/80">{{ $coach->method_step2_description ?? 'Plan sur mesure' }}</p>
            </div>

            <div class="bg-white/10 backdrop-blur-md rounded-3xl p-10 border-2 border-white/20 hover:border-white/40 transition-all transform hover:scale-105">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-8 shadow-xl">
                    <span class="text-4xl font-black text-primary">3</span>
                </div>
                <h3 class="text-3xl font-black text-white mb-4">{{ $coach->method_step3_title ?? 'Suivi' }}</h3>
                <p class="text-lg text-white/80">{{ $coach->method_step3_description ?? 'Accompagnement régulier' }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section - Bold -->
<section id="tarifs" class="py-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <div class="inline-block px-4 py-2 bg-primary/10 rounded-full text-primary font-bold uppercase text-sm tracking-wide mb-6">
                Formules
            </div>
            <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6">
                {{ $coach->pricing_title ?? 'Mes formules' }}
            </h2>
            <p class="text-2xl text-gray-600 max-w-3xl mx-auto font-semibold">
                {{ $coach->pricing_subtitle ?? 'Choisissez votre formule' }}
            </p>
        </div>

        @if(isset($services) && $services->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min($services->count(), 3) }} gap-8">
                @foreach($services as $service)
                    <div class="relative group">
                        <div class="relative bg-white rounded-3xl shadow-xl border-4 border-gray-200 hover:border-primary transition-all h-full flex flex-col transform hover:scale-105">
                            @if($service->is_featured)
                                <span class="absolute -top-4 right-6 inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-black uppercase tracking-wide px-4 py-1 shadow-xl">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17l-5.5 3 1.5-6.5L3 8.5l6.6-.5L12 2l2.4 6 6.6.5-5 4.9 1.5 6.5z" />
                                    </svg>
                                    Populaire
                                </span>
                            @endif

                            <div class="mb-8 rounded-3xl overflow-hidden mx-10 mt-10 border-2 border-gray-100">
                                @if($service->image_url)
                                    <img
                                        src="{{ $service->image_url }}"
                                        alt="Illustration {{ $service->name }}"
                                        class="w-full h-64 object-cover"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="h-64 bg-gradient-to-br from-gray-100 to-gray-200 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm font-semibold">
                                        Aucune image pour ce service
                                    </div>
                                @endif
                            </div>

                            <div class="px-10 pb-10 flex-1 flex flex-col">
                                <h3 class="text-3xl font-black text-gray-900 mb-4">{{ $service->name }}</h3>
                            
                                <div class="mb-6">
                                    <div class="flex items-baseline flex-wrap gap-2">
                                        <span class="text-6xl font-black text-primary leading-none">
                                            {{ number_format($service->price, 0, ',', ' ') }}
                                        </span>
                                        <span class="text-2xl font-bold text-gray-600">€</span>
                                        <span class="text-sm font-semibold text-gray-400 uppercase tracking-wide">{{ $service->currency }}</span>
                                    </div>
                                    @if($service->duration_minutes)
                                        <p class="text-sm text-gray-500 mt-2">⏱️ {{ $service->duration_minutes }} minutes</p>
                                    @endif
                                </div>

                                @if($service->description)
                                    <div class="prose prose-lg text-gray-600 mb-8 flex-grow leading-relaxed [&_ul]:list-disc [&_ul]:pl-5 [&_li]:my-1">
                                        {!! Purify::clean($service->description) !!}
                                    </div>
                                @endif
                                
                                @if(optional($coach->user)->has_payments_module && $service->booking_enabled)
                                    <a href="{{ route('coach.booking.checkout.form', ['coach_slug' => $coach->slug, 'serviceId' => $service->id]) }}"
                                       class="block w-full text-center px-8 py-4 bg-primary text-white text-lg font-black rounded-full hover:bg-primary-dark transition-all shadow-lg">
                                        Payer en ligne
                                    </a>
                                @else
                                    <a href="#contact" class="block w-full text-center px-8 py-4 bg-primary text-white text-lg font-black rounded-full hover:bg-primary-dark transition-all shadow-lg">
                                        Contacter
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-xl text-gray-600">Les formules seront bientôt disponibles.</p>
            </div>
        @endif
    </div>
</section>

<!-- Transformations Section - Bold -->
@if(isset($transformations) && $transformations->count() > 0)
<section id="resultats" class="py-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <div class="inline-block px-4 py-2 bg-primary/10 rounded-full text-primary font-bold uppercase text-sm tracking-wide mb-6">
                Résultats
            </div>
            <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6">
                {{ $coach->transformations_title ?? 'Transformations' }}
            </h2>
            <p class="text-2xl text-gray-600 max-w-3xl mx-auto font-semibold">
                {{ $coach->transformations_subtitle ?? 'Des résultats réels' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($transformations as $transformation)
                <div class="group relative">
                    <div class="bg-white rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all transform hover:scale-105">
                        <div class="grid grid-cols-2 relative">
                            <div class="relative overflow-hidden">
                                @if($transformation->hasMedia('before'))
                                    <button
                                        type="button"
                                        class="block w-full h-72 focus:outline-none cursor-zoom-in"
                                        @click="openLightbox('{{ addslashes($transformation->getFirstMediaUrl('before')) }}', 'Avant')"
                                        aria-label="Voir la photo avant en grand"
                                    >
                                        <img src="{{ $transformation->getFirstMediaUrl('before') }}" alt="Avant" class="w-full h-72 object-cover group-hover:scale-110 transition-transform duration-500">
                                        <span class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-black tracking-wide">
                                            Cliquer pour agrandir
                                        </span>
                                    </button>
                                @else
                                    <div class="w-full h-72 bg-gray-200"></div>
                                @endif
                                <div class="absolute top-4 left-4 bg-red-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg uppercase">
                                    Avant
                                </div>
                            </div>
                            <div class="relative overflow-hidden">
                                @if($transformation->hasMedia('after'))
                                    <button
                                        type="button"
                                        class="block w-full h-72 focus:outline-none cursor-zoom-in"
                                        @click="openLightbox('{{ addslashes($transformation->getFirstMediaUrl('after')) }}', 'Après')"
                                        aria-label="Voir la photo après en grand"
                                    >
                                        <img src="{{ $transformation->getFirstMediaUrl('after') }}" alt="Après" class="w-full h-72 object-cover group-hover:scale-110 transition-transform durée-500">
                                        <span class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-black tracking-wide">
                                            Cliquer pour agrandir
                                        </span>
                                    </button>
                                @else
                                    <div class="w-full h-72 bg-gray-200"></div>
                                @endif
                                <div class="absolute top-4 right-4 bg-green-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg uppercase">
                                    Après
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-black text-gray-900 mb-2">{{ $transformation->title ?? 'Transformation' }}</h3>
                            @if($transformation->description)
                                <p class="text-gray-600">{{ $transformation->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-16">
            <p class="text-2xl text-gray-900 mb-8 font-black">Prêt pour votre transformation ?</p>
            <a href="#tarifs" class="inline-flex items-center px-12 py-5 bg-primary text-white text-xl font-black rounded-full hover:bg-primary-dark transition-all shadow-2xl transform hover:scale-110">
                {{ $coach->cta_text ?? 'Commencer' }}
                <svg class="ml-3 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

<!-- FAQ Section - Bold -->
@if($faqs && $faqs->count() > 0)
<section id="faq" class="py-32 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <div class="inline-block px-4 py-2 bg-primary/10 rounded-full text-primary font-bold uppercase text-sm tracking-wide mb-6">
                FAQ
            </div>
            <h2 class="text-5xl md:text-6xl font-black text-gray-900 mb-6">
                Questions fréquentes
            </h2>
            <p class="text-2xl text-gray-600 font-semibold">
                Tout ce que vous devez savoir
            </p>
        </div>

        <div x-data="{ openFaq: null }" class="space-y-4">
            @foreach($faqs as $index => $faq)
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all">
                    <button @click="openFaq = openFaq === {{ $index + 1 }} ? null : {{ $index + 1 }}" class="w-full text-left px-8 py-6 flex justify-between items-center hover:bg-gray-50 transition-colors">
                        <span class="font-black text-xl text-gray-900 pr-8">{{ $faq->question }}</span>
                        <svg class="w-6 h-6 text-primary transition-transform flex-shrink-0" :class="{ 'transform rotate-180': openFaq === {{ $index + 1 }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === {{ $index + 1 }}" x-transition class="px-8 pb-6 text-lg text-gray-600 border-t border-gray-100" style="display: none;">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Contact Section - Bold -->
<section id="contact" class="py-32 relative overflow-hidden text-white" style="background: linear-gradient(135deg, {{ $coach->color_primary ?? '#3B82F6' }} 0%, {{ $coach->color_secondary ?? '#10B981' }} 100%);">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10" x-data="{ submitted: false, successMessage: '', loading: false }">
        <div class="text-center mb-12">
            <div class="inline-block px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full font-bold uppercase text-sm tracking-wide mb-6">
                Contact
            </div>
            <h2 class="text-5xl md:text-6xl font-black mb-6">
                {{ $coach->final_cta_title ?? 'Prêt à commencer ?' }}
            </h2>
            <p class="text-2xl text-white/90 font-semibold">
                {{ $coach->final_cta_subtitle ?? 'Contactez-moi maintenant' }}
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-white/20 backdrop-blur-sm border border-white/30 px-6 py-4" x-show="!submitted">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-red-500/20 backdrop-blur-sm border border-red-300/40 px-6 py-4" x-show="!submitted">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="font-semibold">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 rounded-2xl bg-white/20 backdrop-blur-sm border border-white/30 px-6 py-5" x-show="submitted" x-transition>
            <div class="flex items-start">
                <svg class="h-7 w-7 text-white mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <div>
                    <p class="font-black text-lg">Message envoyé !</p>
                    <p class="mt-1" x-text="successMessage || 'Vous serez contacté rapidement.'"></p>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md rounded-3xl border-2 border-white/20 shadow-2xl p-10" x-show="!submitted" x-transition>
            <form method="POST" action="/contact" class="grid grid-cols-1 md:grid-cols-2 gap-6" @submit.prevent="loading = true; fetch('/contact', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ name: $refs.name.value, email: $refs.email.value, phone: $refs.phone.value, message: $refs.message.value }) }).then(async (response) => { if (!response.ok) throw new Error('Request failed'); const data = await response.json(); successMessage = data.message || ''; submitted = true; }).catch(() => { loading = false; }).finally(() => { loading = false; });">
                @csrf

                <div class="md:col-span-1">
                    <label for="name" class="block text-sm font-bold text-white mb-2 uppercase tracking-wide">Nom *</label>
                    <input type="text" id="name" name="name" x-ref="name" required value="{{ old('name') }}" class="block w-full rounded-lg border-white/20 bg-white/20 px-4 py-3 text-white placeholder-white/60 shadow-sm focus:border-white focus:ring-white" placeholder="Votre nom">
                </div>

                <div class="md:col-span-1">
                    <label for="email" class="block text-sm font-bold text-white mb-2 uppercase tracking-wide">Email *</label>
                    <input type="email" id="email" name="email" x-ref="email" required value="{{ old('email') }}" class="block w-full rounded-lg border-white/20 bg-white/20 px-4 py-3 text-white placeholder-white/60 shadow-sm focus:border-white focus:ring-white" placeholder="vous@example.com">
                </div>

                <div class="md:col-span-1">
                    <label for="phone" class="block text-sm font-bold text-white mb-2 uppercase tracking-wide">Téléphone</label>
                    <input type="text" id="phone" name="phone" x-ref="phone" value="{{ old('phone') }}" class="block w-full rounded-lg border-white/20 bg-white/20 px-4 py-3 text-white placeholder-white/60 shadow-sm focus:border-white focus:ring-white" placeholder="+33 6...">
                </div>

                <div class="md:col-span-2">
                    <label for="message" class="block text-sm font-bold text-white mb-2 uppercase tracking-wide">Message *</label>
                    <textarea id="message" name="message" x-ref="message" rows="4" required class="block w-full rounded-lg border-white/20 bg-white/20 px-4 py-3 text-white placeholder-white/60 shadow-sm focus:border-white focus:ring-white" placeholder="Vos objectifs...">{{ old('message') }}</textarea>
                </div>

                <div class="md:col-span-2 flex items-center justify-between pt-4">
                    <p class="text-sm text-white/70">Réponse sous 24-48h</p>
                    <button type="submit" :disabled="loading" class="inline-flex items-center px-10 py-4 bg-white text-primary text-lg font-black rounded-full hover:bg-gray-100 transition-all disabled:opacity-50 shadow-xl">
                        <span x-show="!loading">Envoyer</span>
                        <span x-show="loading">Envoi...</span>
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@section('coach-site-footer')
    <footer class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-black text-white">
        <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle at 10% 20%, rgba(255,255,255,0.1), transparent 40%), radial-gradient(circle at 80% 0%, rgba(255,255,255,0.07), transparent 45%);"></div>
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-primary/30 via-transparent to-transparent blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <div class="lg:col-span-5 space-y-4">
                    <p class="text-xs uppercase tracking-[0.4em] text-white/60">Coaching sportif</p>
                    <h3 class="text-3xl font-black leading-tight">
                        {{ $coach->name }}
                    </h3>
                    <p class="text-white/70 leading-relaxed">
                        {{ $coach->about_text ? Str::limit(strip_tags($coach->about_text), 200) : 'Programmes d’élite, mindset puissant, résultats spectaculaires.' }}
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
                                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="group inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/20 bg-white/5 hover:bg-white text-white transition-all" aria-label="{{ $social['label'] }}">
                                        <svg class="h-5 w-5 group-hover:text-primary" fill="currentColor" viewBox="0 0 24 24">{!! $social['icon'] !!}</svg>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    <div class="space-y-4">
                        <p class="text-xs uppercase tracking-[0.45em] text-white/50">Sections</p>
                        <ul class="space-y-3 text-white/80 text-sm">
                            @foreach ([
                                ['label' => 'Accueil', 'anchor' => '#accueil'],
                                ['label' => 'À propos', 'anchor' => '#a-propos'],
                                ['label' => 'Méthode', 'anchor' => '#methode'],
                                ['label' => 'Tarifs', 'anchor' => '#tarifs'],
                                ['label' => 'Résultats', 'anchor' => '#resultats'],
                                ['label' => 'Contact', 'anchor' => '#contact'],
                            ] as $link)
                                <li>
                                    <a href="{{ $link['anchor'] }}" class="inline-flex items-center gap-2 font-semibold hover:text-primary transition-colors">
                                        <span class="h-px w-6 bg-white/30"></span>
                                        {{ $link['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs uppercase tracking-[0.45em] text-white/50">Contact express</p>
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 space-y-4 text-sm text-white/80">
                            <div>
                                <p class="text-xs text-white/60 uppercase">Email</p>
                                <a href="mailto:{{ optional($coach->user)->email ?? 'contact@unicoach.app' }}" class="text-lg font-semibold text-white hover:text-primary transition-colors">
                                    {{ optional($coach->user)->email ?? 'contact@unicoach.app' }}
                                </a>
                            </div>
                            <hr class="border-white/10">
                            <div>
                                <p class="text-xs text-white/60 uppercase">Réserver</p>
                                <a href="#tarifs" class="mt-2 inline-flex items-center justify-center text-center gap-2 rounded-3xl border border-white/40 px-4 py-2 text-sm font-semibold hover:bg-white hover:text-primary transition-all">
                                    {{ $coach->cta_text ?? 'Choisir ma formule' }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs uppercase tracking-[0.45em] text-white/50">Informations légales</p>
                        <div class="space-y-3 text-sm text-white/70">
                            @php
                                if (!$coach->relationLoaded('user')) {
                                    $coach->load('user');
                                }
                                $vatNumber = $coach->user?->vat_number ?? null;
                            @endphp
                            @if($vatNumber)
                                <div>
                                    <p class="text-xs text-white/50 uppercase">N° TVA</p>
                                    <p class="font-semibold text-white">{{ $vatNumber }}</p>
                                </div>
                            @endif
                            <a href="/mentions-legales" class="inline-flex items-center gap-2 font-semibold hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Mentions légales
                            </a>
                            <p class="text-xs text-white/50">
                                Propulsé par <a href="https://unicoach.app" target="_blank" class="text-white hover:text-primary font-semibold transition-colors">UNICOACH</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-center justify-between gap-4 border-t border-white/10 pt-8 text-xs uppercase tracking-[0.4em] text-white/60">
                <p class="text-white/80">&copy; {{ date('Y') }} {{ $coach->name }} — Tous droits réservés.</p>
                <p class="inline-flex items-center gap-3">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Intensité • Discipline • Résultats
                </p>
            </div>
        </div>
    </footer>
@endsection
