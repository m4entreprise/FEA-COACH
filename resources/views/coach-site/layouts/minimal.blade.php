@extends('layouts.coach-site')

@php
    use Illuminate\Support\Str;
    use Stevebauman\Purify\Facades\Purify;
@endphp

@section('content')

{{-- Layout Minimal - Version épurée et focalisée sur le texte et les CTA --}}

<!-- Hero Section - Minimal -->
<section id="accueil" class="relative overflow-hidden py-28 bg-white">
    <div class="absolute inset-0 bg-gradient-to-br from-gray-50 via-white to-gray-100"></div>
    <div class="absolute -top-32 -right-10 w-72 h-72 rounded-full bg-primary/10 blur-3xl"></div>
    <div class="absolute top-24 -left-16 w-64 h-64 rounded-full bg-primary/5 blur-2xl"></div>
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] items-center">
            <div>
                <p class="text-xs font-semibold tracking-[0.45em] text-gray-500 uppercase mb-4">Coach minimaliste</p>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-6">
                    {{ $coach->hero_title ?? 'Transformez votre corps, transformez votre vie' }}
                </h1>
                <p class="text-xl text-gray-600 mb-10 max-w-2xl">
                    {{ $coach->hero_subtitle ?? 'Coaching sportif personnalisé pour atteindre vos objectifs' }}
                </p>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <a href="#tarifs" class="inline-flex items-center justify-center px-10 py-4 bg-primary text-white text-lg font-semibold rounded-full hover:bg-primary-dark transition-all shadow-lg hover:shadow-xl">
                        {{ $coach->cta_text ?? 'Commencer maintenant' }}
                        <x-lucide-arrow-right class="ml-2 w-5 h-5" />
                    </a>
                    <a href="#a-propos" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 transition">
                        Voir la méthode
                        <x-lucide-arrow-up-right class="w-4 h-4" />
                    </a>
                </div>
            </div>
            <div class="bg-white/90 border border-gray-200 rounded-3xl p-6 shadow-sm space-y-6">
                <p class="text-sm uppercase tracking-[0.35em] text-gray-500">Repères</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-4xl font-bold text-gray-900">{{ isset($transformations) ? $transformations->count() : 0 }}+</p>
                        <p class="text-sm text-gray-500">clients suivis</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold text-gray-900">{{ $coach->satisfaction_rate ?? 100 }}%</p>
                        <p class="text-sm text-gray-500">satisfaction</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold text-gray-900">{{ $coach->average_rating ?? 5.0 }}★</p>
                        <p class="text-sm text-gray-500">note moyenne</p>
                    </div>
                    <div class="text-sm text-gray-500">
                        <p>Programmes épurés, focus sur l’essentiel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section - Minimal -->
<section id="a-propos" class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs font-semibold tracking-[0.45em] text-gray-500 uppercase mb-4">À propos</p>
        <h2 class="text-4xl font-bold text-gray-900 mb-6">
            {{ $coach->name }}
        </h2>
        <div class="text-lg text-gray-600 leading-relaxed space-y-4 mb-12 max-w-3xl mx-auto">
            {!! nl2br(e($coach->about_text ?? 'Coach sportif certifié avec plusieurs années d\'expérience dans l\'accompagnement personnalisé.')) !!}
        </div>
        <div class="grid gap-6 sm:grid-cols-3">
            <div class="border border-gray-200 rounded-2xl p-6">
                <p class="text-sm uppercase tracking-wide text-gray-500 mb-2">Clients suivis</p>
                <p class="text-4xl font-bold text-gray-900">{{ isset($transformations) ? $transformations->count() : 0 }}+</p>
            </div>
            <div class="border border-gray-200 rounded-2xl p-6">
                <p class="text-sm uppercase tracking-wide text-gray-500 mb-2">Satisfaction</p>
                <p class="text-4xl font-bold text-gray-900">{{ $coach->satisfaction_rate ?? 100 }}%</p>
            </div>
            <div class="border border-gray-200 rounded-2xl p-6">
                <p class="text-sm uppercase tracking-wide text-gray-500 mb-2">Note moyenne</p>
                <p class="text-4xl font-bold text-gray-900">{{ $coach->average_rating ?? 5.0 }}★</p>
            </div>
        </div>
    </div>
</section>

<!-- Method Section - Minimal -->
<section id="methode" class="py-20 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="text-xs font-semibold tracking-[0.45em] text-gray-500 uppercase mb-4">Processus</p>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                {{ $coach->method_title ?? 'Ma méthode' }}
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                {{ $coach->method_subtitle ?? 'Une approche simple et efficace' }}
            </p>
        </div>

        @if($coach->method_text)
            <div class="text-base sm:text-lg text-gray-600 leading-relaxed bg-white rounded-2xl border border-gray-200 px-6 py-5 mb-12 shadow-sm">
                {!! nl2br(e($coach->method_text)) !!}
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ([
                [
                    'number' => '01',
                    'title' => $coach->method_step1_title ?? 'Évaluation',
                    'description' => $coach->method_step1_description ?? 'Bilan complet pour identifier vos priorités immédiates.',
                    'icon' => 'clipboard-check',
                ],
                [
                    'number' => '02',
                    'title' => $coach->method_step2_title ?? 'Programme',
                    'description' => $coach->method_step2_description ?? 'Plan minimaliste, actionnable et compatible avec votre agenda.',
                    'icon' => 'layers',
                ],
                [
                    'number' => '03',
                    'title' => $coach->method_step3_title ?? 'Suivi',
                    'description' => $coach->method_step3_description ?? 'Points réguliers, ajustements précis, nouvelles priorités.',
                    'icon' => 'refresh-ccw',
                ],
            ] as $step)
                <div class="relative overflow-hidden rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <span class="absolute top-6 right-6 text-4xl font-bold text-gray-100">{{ $step['number'] }}</span>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.35em] text-gray-400">
                        Étape {{ $step['number'] }}
                    </span>
                    <div class="mt-4 flex items-center gap-3">
                        @php $icon = $step['icon']; @endphp
                        <x-dynamic-component :component="'lucide-' . $icon" class="w-6 h-6 text-gray-900" />
                        <h3 class="text-xl font-semibold text-gray-900">{{ $step['title'] }}</h3>
                    </div>
                    <p class="mt-3 text-gray-600 leading-relaxed">{{ $step['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Pricing Section - Minimal -->
<section id="tarifs" class="py-20 bg-white overflow-hidden">
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
            <div class="space-y-8">
                @foreach($services as $service)
                    <div class="relative rounded-2xl border border-gray-200 bg-white px-6 sm:px-8 pt-10 sm:pt-12 pb-6 sm:pb-8 transition hover:border-gray-900/30">
                        @if($service->is_featured)
                            <span class="absolute -top-4 sm:-top-5 right-4 sm:right-6 inline-flex items-center gap-2 rounded-full border border-primary/20 bg-white shadow-lg shadow-primary/30 px-3.5 py-1.5 text-xs font-semibold uppercase tracking-wide text-primary">
                                <x-lucide-star class="w-3.5 h-3.5" />
                                Populaire
                            </span>
                        @endif
                        <div class="flex flex-col gap-6">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                                <div class="space-y-3 lg:max-w-xl">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-2xl font-semibold text-gray-900">{{ $service->name }}</h3>
                                        @if($service->duration_minutes)
                                            <span class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-600">
                                                <x-lucide-clock-3 class="w-4 h-4 text-gray-500" />
                                                {{ $service->duration_minutes }} min
                                            </span>
                                        @endif
                                    </div>
                                    @if($service->description)
                                        <div class="text-gray-600 text-base leading-relaxed space-y-2">
                                            {!! Purify::clean($service->description) !!}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-left lg:text-right">
                                    <p class="text-sm uppercase tracking-widest text-gray-500 mb-1">Investissement</p>
                                    <div class="text-4xl font-bold text-gray-900">{{ number_format($service->price, 0, ',', ' ') }}€</div>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <p class="text-sm text-gray-500">Plan clair, suivi optimisé, résultats mesurables.</p>
                                @if(optional($coach->user)->has_payments_module && $service->booking_enabled)
                                    <a href="{{ route('coach.booking.checkout.form', ['coach_slug' => $coach->slug, 'serviceId' => $service->id]) }}"
                                       class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-900 px-6 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-900 hover:text-white transition-all">
                                        <span>Payer en ligne</span>
                                        <x-lucide-arrow-up-right class="w-4 h-4" />
                                    </a>
                                @else
                                    <a href="#contact"
                                       class="inline-flex items-center justify-center gap-2 rounded-full border border-gray-900 px-6 py-3 text-sm font-semibold text-gray-900 hover:bg-gray-900 hover:text-white transition-all">
                                        <span>Me contacter</span>
                                        <x-lucide-arrow-up-right class="w-4 h-4" />
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
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="text-xs font-semibold tracking-[0.45em] text-gray-500 uppercase mb-4">Résultats</p>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                {{ $coach->transformations_title ?? 'Leurs transformations' }}
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                {{ $coach->transformations_subtitle ?? 'Des résultats concrets et mesurables' }}
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            @foreach($transformations as $transformation)
                @php
                    $beforeUrl = $transformation->hasMedia('before') ? $transformation->getFirstMediaUrl('before') : null;
                    $afterUrl = $transformation->hasMedia('after') ? $transformation->getFirstMediaUrl('after') : null;
                @endphp
                <div class="border border-gray-200 rounded-3xl overflow-hidden bg-white">
                    <div class="grid grid-cols-2">
                        <div class="relative">
                            @if($beforeUrl)
                                <button type="button"
                                        class="block w-full h-48 focus:outline-none"
                                        @click="openLightbox('{{ addslashes($beforeUrl) }}', 'Avant')"
                                        aria-label="Voir la photo avant en grand">
                                    <img src="{{ $beforeUrl }}" alt="Avant" class="w-full h-48 object-cover">
                                    <span class="absolute inset-0 bg-gradient-to-br from-black/50 via-black/10 to-transparent opacity-0 hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold tracking-wide">
                                        Agrandir
                                    </span>
                                </button>
                            @else
                                <div class="w-full h-48 bg-gray-100"></div>
                            @endif
                            <span class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-black/60 backdrop-blur px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.35em] text-white">
                                AVANT
                            </span>
                        </div>
                        <div class="relative">
                            @if($afterUrl)
                                <button type="button"
                                        class="block w-full h-48 focus:outline-none"
                                        @click="openLightbox('{{ addslashes($afterUrl) }}', 'Après')"
                                        aria-label="Voir la photo après en grand">
                                    <img src="{{ $afterUrl }}" alt="Après" class="w-full h-48 object-cover">
                                    <span class="absolute inset-0 bg-gradient-to-bl from-black/50 via-black/10 to-transparent opacity-0 hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold tracking-wide">
                                        Agrandir
                                    </span>
                                </button>
                            @else
                                <div class="w-full h-48 bg-gray-100"></div>
                            @endif
                            <span class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-black/60 backdrop-blur px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.35em] text-white">
                                APRÈS
                            </span>
                        </div>
                    </div>
                    @if($transformation->title || $transformation->description)
                        <div class="p-6 space-y-2">
                            @if($transformation->title)
                                <h3 class="text-xl font-semibold text-gray-900">{{ $transformation->title }}</h3>
                            @endif
                            @if($transformation->description)
                                <p class="text-sm text-gray-600">{{ $transformation->description }}</p>
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
                        <x-lucide-chevron-down class="w-5 h-5 text-gray-500 transition-transform flex-shrink-0 ml-4"
                            x-bind:class="{ 'transform rotate-180': openFaq === {{ $index + 1 }} }" />
                    </button>
                    <div x-show="openFaq === {{ $index + 1 }}" 
                         x-transition
                         class="px-6 pt-4 pb-5 text-gray-600 border-t border-gray-100 leading-relaxed"
                         style="display: none;">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Contact Section -->
<section id="contact" class="py-20 bg-gray-900 text-white">
    <div
        class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="{ submitted: false, successMessage: '', loading: false }"
    >
        <div class="text-center mb-10">
            <p class="text-xs font-semibold uppercase tracking-[0.45em] text-white/60 mb-4">Contact</p>
            <h2 class="text-4xl font-bold text-white mb-4">
                {{ $coach->final_cta_title ?? 'Prêt à commencer ?' }}
            </h2>
            <p class="text-xl text-white/70 max-w-2xl mx-auto">
                {{ $coach->final_cta_subtitle ?? 'Contactez-moi pour discuter de vos objectifs' }}
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3" x-show="!submitted">
                <div class="flex items-center text-green-800">
                    <x-lucide-badge-check class="h-5 w-5 mr-2" />
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-4 py-3" x-show="!submitted">
                <div class="flex items-start gap-3 text-red-800">
                    <x-lucide-alert-triangle class="h-5 w-5 flex-shrink-0 mt-0.5" />
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div
            class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-4"
            x-show="submitted"
            x-transition
        >
            <div class="flex items-center text-green-800">
                <x-lucide-badge-check class="h-6 w-6 mr-3" />
                <div>
                    <p class="font-semibold">Message envoyé !</p>
                    <p class="text-sm" x-text="successMessage || 'Vous serez contacté rapidement.'"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-lg shadow-black/20" x-show="!submitted" x-transition>
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
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-gray-900 focus:ring-gray-900/20"
                        placeholder="Votre nom"
                    >
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">Téléphone *</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        x-ref="phone"
                        required
                        autocomplete="tel"
                        value="{{ old('phone') }}"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-gray-900 focus:ring-gray-900/20"
                        placeholder="+32 4 12 34 56 78"
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
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-gray-900 focus:ring-gray-900/20"
                        placeholder="vous@example.com"
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
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-gray-900 focus:ring-gray-900/20"
                        placeholder="Parlez-moi de vos objectifs..."
                    >{{ old('message') }}</textarea>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <p class="text-sm text-gray-500">Réponse sous 24-48h</p>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="inline-flex items-center px-8 py-3 bg-gray-900 text-white font-semibold rounded-full hover:bg-black transition-all disabled:opacity-50 shadow-lg hover:shadow-xl"
                    >
                        <span x-show="!loading">Envoyer</span>
                        <span x-show="loading">Envoi...</span>
                        <x-lucide-arrow-up-right class="ml-2 w-5 h-5" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@section('coach-site-footer')
    <footer class="bg-white border-t border-gray-200 text-gray-900">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
            <div class="flex flex-col lg:flex-row gap-10">
                <div class="flex-1 space-y-3">
                    <p class="text-xs font-semibold tracking-[0.35em] text-gray-500 uppercase">Coaching Sportif</p>
                    <h3 class="text-2xl font-semibold">{{ $coach->name }}</h3>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $coach->about_text ? Str::limit(strip_tags($coach->about_text), 160) : 'Coaching minimaliste, focalisé sur la progression constante et la clarté des objectifs.' }}
                    </p>

                    @if($coach->facebook_url || $coach->instagram_url || $coach->twitter_url || $coach->linkedin_url || $coach->youtube_url || $coach->tiktok_url)
                        <div class="flex flex-wrap gap-3 pt-2">
                            @foreach ([
                                ['url' => $coach->facebook_url, 'label' => 'Facebook', 'icon' => '<path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>' ],
                                ['url' => $coach->instagram_url, 'label' => 'Instagram', 'icon' => '<path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/>' ],
                                ['url' => $coach->twitter_url, 'label' => 'Twitter', 'icon' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>' ],
                                ['url' => $coach->linkedin_url, 'label' => 'LinkedIn', 'icon' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>' ],
                                ['url' => $coach->youtube_url, 'label' => 'YouTube', 'icon' => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>' ],
                                ['url' => $coach->tiktok_url, 'label' => 'TikTok', 'icon' => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>' ],
                            ] as $social)
                                @if($social['url'])
                                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white hover:border-gray-900 hover:text-gray-900 transition-colors" aria-label="{{ $social['label'] }}">
                                        <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">{!! $social['icon'] !!}</svg>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="grid flex-1 grid-cols-1 sm:grid-cols-2 gap-8">
                    <div>
                        <p class="text-xs font-semibold tracking-[0.35em] text-gray-500 uppercase mb-4">Navigation</p>
                        <ul class="space-y-3 text-sm text-gray-600">
                            @foreach ([
                                ['label' => 'Accueil', 'anchor' => '#accueil'],
                                ['label' => 'À propos', 'anchor' => '#a-propos'],
                                ['label' => 'Méthode', 'anchor' => '#methode'],
                                ['label' => 'Tarifs', 'anchor' => '#tarifs'],
                                ['label' => 'Résultats', 'anchor' => '#resultats'],
                                ['label' => 'Contact', 'anchor' => '#contact'],
                            ] as $link)
                                <li>
                                    <a href="{{ $link['anchor'] }}" class="inline-flex items-center gap-2 font-medium hover:text-gray-900 transition-colors">
                                        <span class="h-px w-4 bg-gray-300"></span>
                                        {{ $link['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="space-y-3">
                        <p class="text-xs font-semibold tracking-[0.35em] text-gray-500 uppercase">Contact rapide</p>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5 space-y-4 text-sm text-gray-600">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Email</p>
                                <a href="mailto:{{ optional($coach->user)->email ?? 'contact@unicoach.app' }}" class="text-lg font-semibold text-gray-900 hover:text-primary transition-colors">
                                    {{ optional($coach->user)->email ?? 'contact@unicoach.app' }}
                                </a>
                            </div>
                            <hr class="border-gray-200">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500">Réserver</p>
                                <a href="#tarifs" class="mt-2 inline-flex items-center justify-center text-center rounded-2xl border border-gray-900 px-4 py-2 text-sm font-semibold hover:bg-gray-900 hover:text-white transition-all">
                                    {{ $coach->cta_text ?? 'Réserver une séance' }}
                                </a>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs text-gray-500">
                            @php
                                if (!$coach->relationLoaded('user')) {
                                    $coach->load('user');
                                }
                                $vatNumber = $coach->user?->vat_number ?? null;
                            @endphp
                            @if($vatNumber)
                                <p>N° TVA — <span class="font-semibold text-gray-700">{{ $vatNumber }}</span></p>
                            @endif
                            <p>
                                <a href="/mentions-legales" class="font-semibold text-gray-700 hover:text-primary transition-colors">Mentions légales</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-4 border-t border-gray-200 pt-6 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ $coach->name }} — Tous droits réservés.</p>
                <p class="text-xs uppercase tracking-widest text-gray-400">
                    Propulsé par <a href="https://unicoach.app" target="_blank" class="font-semibold text-gray-600 hover:text-primary transition-colors">UNICOACH</a>
                </p>
            </div>
        </div>
    </footer>
@endsection
