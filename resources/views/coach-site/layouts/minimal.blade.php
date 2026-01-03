@extends('layouts.coach-site')

@section('content')

{{-- Layout Minimal - Version épurée et focalisée sur le texte et les CTA --}}

<!-- Hero Section - Minimal -->
<section id="accueil" class="relative py-32 bg-gradient-to-b from-white to-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
            {{ $coach->hero_title ?? 'Transformez votre corps, transformez votre vie' }}
        </h1>
        <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-2xl mx-auto">
            {{ $coach->hero_subtitle ?? 'Coaching sportif personnalisé pour atteindre vos objectifs' }}
        </p>
        <a href="#tarifs" class="inline-flex items-center px-10 py-4 bg-primary text-white text-lg font-semibold rounded-full hover:bg-primary-dark transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
            {{ $coach->cta_text ?? 'Commencer maintenant' }}
            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>
</section>

<!-- About Section - Minimal -->
<section id="a-propos" class="py-20 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold text-gray-900 mb-6">
            {{ $coach->name }}
        </h2>
        <div class="text-lg text-gray-600 leading-relaxed space-y-4 mb-10">
            {!! nl2br(e($coach->about_text ?? 'Coach sportif certifié avec plusieurs années d\'expérience dans l\'accompagnement personnalisé.')) !!}
        </div>
        
        <!-- Stats - Minimal -->
        <div class="flex justify-center gap-12 pt-8 border-t border-gray-200">
            <div class="text-center">
                <div class="text-4xl font-bold text-primary mb-2">{{ isset($transformations) ? $transformations->count() : 0 }}+</div>
                <div class="text-sm text-gray-500 uppercase tracking-wide">Clients</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-primary mb-2">{{ $coach->satisfaction_rate ?? 100 }}%</div>
                <div class="text-sm text-gray-500 uppercase tracking-wide">Satisfaction</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-primary mb-2">{{ $coach->average_rating ?? 5.0 }}★</div>
                <div class="text-sm text-gray-500 uppercase tracking-wide">Note</div>
            </div>
        </div>
    </div>
</section>

<!-- Method Section - Minimal -->
<section id="methode" class="py-20 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">
            {{ $coach->method_title ?? 'Ma méthode' }}
        </h2>
        <p class="text-xl text-gray-600 mb-12">
            {{ $coach->method_subtitle ?? 'Une approche simple et efficace' }}
        </p>

        @if($coach->method_text)
            <div class="text-lg text-gray-600 leading-relaxed mb-12">
                {!! nl2br(e($coach->method_text)) !!}
            </div>
        @endif

        <!-- Method Steps - Minimal -->
        <div class="space-y-8">
            <div class="bg-white rounded-lg p-8 text-left border border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white text-xl font-bold">1</div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $coach->method_step1_title ?? 'Évaluation' }}</h3>
                        <p class="text-gray-600">{{ $coach->method_step1_description ?? 'Bilan complet de votre condition physique et définition de vos objectifs.' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-8 text-left border border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white text-xl font-bold">2</div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $coach->method_step2_title ?? 'Programme' }}</h3>
                        <p class="text-gray-600">{{ $coach->method_step2_description ?? 'Plan d\'entraînement sur mesure adapté à votre niveau.' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-8 text-left border border-gray-200">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white text-xl font-bold">3</div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $coach->method_step3_title ?? 'Suivi' }}</h3>
                        <p class="text-gray-600">{{ $coach->method_step3_description ?? 'Accompagnement régulier pour garantir vos progrès.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section - Minimal -->
<section id="tarifs" class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                {{ $coach->pricing_title ?? 'Formules' }}
            </h2>
            <p class="text-xl text-gray-600">
                {{ $coach->pricing_subtitle ?? 'Choisissez la formule adaptée à vos objectifs' }}
            </p>
        </div>

        @if(isset($services) && $services->count() > 0)
            <div class="space-y-6">
                @foreach($services as $service)
                    <div class="bg-white border-2 border-gray-200 rounded-lg p-8 hover:border-primary transition-all">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $service->name }}</h3>
                                @if($service->duration_minutes)
                                    <p class="text-sm text-gray-500 mb-2">⏱️ {{ $service->duration_minutes }} minutes</p>
                                @endif
                                <p class="text-gray-600">{{ $service->description }}</p>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <div class="text-3xl font-bold text-primary">{{ number_format($service->price, 0, ',', ' ') }}€</div>
                                </div>
                                @if($service->booking_enabled && $coach->user->has_payments_module)
                                    <form action="{{ route('coach.booking.checkout', ['coach_slug' => $coach->slug, 'serviceId' => $service->id]) }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="client_email" value="booking@temp.com">
                                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-dark transition-all whitespace-nowrap">
                                            Réserver
                                        </button>
                                    </form>
                                @else
                                    <a href="#contact" 
                                       class="inline-flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-dark transition-all whitespace-nowrap">
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
                <p class="text-gray-500">Les formules seront bientôt disponibles.</p>
            </div>
        @endif
    </div>
</section>

<!-- Transformations Section - Minimal -->
@if(isset($transformations) && $transformations->count() > 0)
<section id="resultats" class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">
            {{ $coach->transformations_title ?? 'Résultats' }}
        </h2>
        <p class="text-xl text-gray-600 mb-12">
            {{ $coach->transformations_subtitle ?? 'Des transformations réelles' }}
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($transformations as $transformation)
                <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                    <div class="grid grid-cols-2">
                        <div class="relative">
                            @if($transformation->hasMedia('before'))
                                <img src="{{ $transformation->getFirstMediaUrl('before') }}" 
                                     alt="Avant" 
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-100"></div>
                            @endif
                            <div class="absolute bottom-2 left-2 bg-black/70 text-white text-xs font-semibold px-2 py-1 rounded">AVANT</div>
                        </div>
                        <div class="relative">
                            @if($transformation->hasMedia('after'))
                                <img src="{{ $transformation->getFirstMediaUrl('after') }}" 
                                     alt="Après" 
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-100"></div>
                            @endif
                            <div class="absolute bottom-2 right-2 bg-primary text-white text-xs font-semibold px-2 py-1 rounded">APRÈS</div>
                        </div>
                    </div>
                    @if($transformation->title || $transformation->description)
                        <div class="p-4 text-left">
                            @if($transformation->title)
                                <h3 class="font-semibold text-gray-900">{{ $transformation->title }}</h3>
                            @endif
                            @if($transformation->description)
                                <p class="text-sm text-gray-600 mt-1">{{ $transformation->description }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- FAQ Section - Minimal -->
@if($faqs && $faqs->count() > 0)
<section id="faq" class="py-20 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                Questions fréquentes
            </h2>
        </div>

        <div x-data="{ openFaq: null }" class="space-y-3">
            @foreach($faqs as $index => $faq)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button @click="openFaq = openFaq === {{ $index + 1 }} ? null : {{ $index + 1 }}" 
                            class="w-full text-left px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition-colors">
                        <span class="font-semibold text-gray-900">{{ $faq->question }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform flex-shrink-0 ml-4" 
                             :class="{ 'transform rotate-180': openFaq === {{ $index + 1 }} }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="openFaq === {{ $index + 1 }}" 
                         x-transition
                         class="px-6 pb-4 text-gray-600 border-t border-gray-100"
                         style="display: none;">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Contact Section - Minimal -->
<section id="contact" class="py-20 bg-gray-50">
    <div
        class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="{ submitted: false, successMessage: '', loading: false }"
    >
        <div class="text-center mb-10">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                {{ $coach->final_cta_title ?? 'Prêt à commencer ?' }}
            </h2>
            <p class="text-xl text-gray-600">
                {{ $coach->final_cta_subtitle ?? 'Contactez-moi pour discuter de vos objectifs' }}
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3" x-show="!submitted">
                <div class="flex items-center text-green-800">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3" x-show="!submitted">
                <ul class="text-red-800 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div
            class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-4"
            x-show="submitted"
            x-transition
        >
            <div class="flex items-center text-green-800">
                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div>
                    <p class="font-semibold">Message envoyé !</p>
                    <p class="text-sm" x-text="successMessage || 'Vous serez contacté rapidement.'"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-8" x-show="!submitted" x-transition>
            <form
                method="POST"
                action="/contact"
                class="space-y-6"
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
                            if (!response.ok) throw new Error('Request failed');
                            const data = await response.json();
                            successMessage = data.message || '';
                            submitted = true;
                        })
                        .catch(() => {
                            loading = false;
                        })
                        .finally(() => {
                            loading = false;
                        });
                "
            >
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Nom complet *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        x-ref="name"
                        required
                        value="{{ old('name') }}"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Votre nom"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        x-ref="email"
                        required
                        value="{{ old('email') }}"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="vous@example.com"
                    >
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">Téléphone</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        x-ref="phone"
                        value="{{ old('phone') }}"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="+33 6 12 34 56 78"
                    >
                </div>

                <div>
                    <label for="message" class="block text-sm font-semibold text-gray-900 mb-2">Message *</label>
                    <textarea
                        id="message"
                        name="message"
                        x-ref="message"
                        rows="5"
                        required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                        placeholder="Parlez-moi de vos objectifs..."
                    >{{ old('message') }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <p class="text-sm text-gray-500">Réponse sous 24-48h</p>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex items-center px-8 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-dark transition-all disabled:opacity-50 shadow-lg hover:shadow-xl"
                    >
                        <span x-show="!loading">Envoyer</span>
                        <span x-show="loading">Envoi...</span>
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
