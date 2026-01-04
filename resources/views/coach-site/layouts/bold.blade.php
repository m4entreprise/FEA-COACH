@extends('layouts.coach-site')

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

        <div class="grid grid-cols-3 gap-8 max-w-3xl mx-auto mb-12">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-5xl font-black mb-2">{{ isset($transformations) ? $transformations->count() : 0 }}+</div>
                <div class="text-sm uppercase tracking-wider">Clients</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-5xl font-black mb-2">{{ $coach->satisfaction_rate ?? 100 }}%</div>
                <div class="text-sm uppercase tracking-wider">Satisfaction</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-5xl font-black mb-2">{{ $coach->average_rating ?? 5.0 }}</div>
                <div class="text-sm uppercase tracking-wider">Note</div>
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
                        <div class="relative bg-white rounded-3xl shadow-xl border-4 border-gray-200 hover:border-primary transition-all p-10 h-full flex flex-col transform hover:scale-105">
                            @if($service->booking_enabled)
                                <span class="absolute -top-4 right-6 inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-black uppercase tracking-wide px-4 py-1 shadow-xl">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17l-5.5 3 1.5-6.5L3 8.5l6.6-.5L12 2l2.4 6 6.6.5-5 4.9 1.5 6.5z" />
                                    </svg>
                                    Populaire
                                </span>
                            @endif

                            <h3 class="text-3xl font-black text-gray-900 mb-4">{{ $service->name }}</h3>
                            
                            <div class="mb-6">
                                <div class="flex items-baseline">
                                    <span class="text-6xl font-black text-primary">{{ number_format($service->price, 0, ',', ' ') }}</span>
                                    <span class="text-2xl font-bold text-gray-600 ml-2">€</span>
                                </div>
                                @if($service->duration_minutes)
                                    <p class="text-sm text-gray-500 mt-2">⏱️ {{ $service->duration_minutes }} minutes</p>
                                @endif
                            </div>

                            <p class="text-lg text-gray-600 mb-8 flex-grow">{{ $service->description }}</p>
                            
                            @if(optional($coach->user)->has_payments_module)
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
