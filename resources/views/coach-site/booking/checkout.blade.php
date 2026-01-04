@extends('layouts.coach-site')

@section('content')
<section class="min-h-screen bg-gray-50 py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-10">
            <a href="{{ $backUrl ?? url()->previous() }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Retour
            </a>
            <span class="text-xs text-gray-400 uppercase tracking-wide">Paiement sécurisé</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- Informations de paiement -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <div class="mb-8">
                        <p class="text-sm font-semibold text-primary uppercase tracking-wide mb-2">Paiement en ligne</p>
                        <h1 class="text-3xl font-bold text-gray-900">Complétez vos informations</h1>
                        <p class="text-gray-500 mt-2">Ces informations seront transmises à {{ $coach->name }} pour votre réservation.</p>
                    </div>

                    @if(session('error'))
                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ $formAction }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                                <input type="text" name="client_first_name" value="{{ old('client_first_name') }}" required maxlength="100"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary"
                                       placeholder="Ex : Marie">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                                <input type="text" name="client_last_name" value="{{ old('client_last_name') }}" required maxlength="100"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary"
                                       placeholder="Ex : Dupont">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="client_email" value="{{ old('client_email') }}" required maxlength="255"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary"
                                       placeholder="vous@email.com">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                <input type="text" name="client_phone" value="{{ old('client_phone') }}" maxlength="30"
                                       class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary"
                                       placeholder="06 12 34 56 78">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message pour le coach (optionnel)</label>
                            <textarea name="client_notes" rows="4" maxlength="2000"
                                      class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:ring-2 focus:ring-primary focus:border-primary"
                                      placeholder="Indiquez vos objectifs, disponibilités, etc.">{{ old('client_notes') }}</textarea>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Paiement sécurisé par Stripe. Aucune carte n'est stockée sur nos serveurs.
                            </p>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-primary text-white font-semibold rounded-xl shadow-lg hover:bg-primary-dark transition-all">
                                Continuer vers le paiement
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Récapitulatif -->
            <div class="lg:col-span-2">
                <div class="bg-gray-900 text-white rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                    <div class="absolute inset-0 opacity-20" style="background: radial-gradient(circle at top right, {{ $coach->color_secondary ?? '#10B981' }}, transparent 55%);"></div>
                    <div class="relative">
                        <p class="uppercase text-xs tracking-[0.25em] text-white/70 mb-4">Votre séance</p>
                        <h2 class="text-3xl font-bold mb-3">{{ $service->name }}</h2>
                        <p class="text-white/80 leading-relaxed mb-6">{{ $service->description }}</p>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-white/70">Coach</span>
                                <span class="font-semibold">{{ $coach->name }}</span>
                            </div>
                            @if($service->duration_minutes)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-white/70">Durée estimée</span>
                                    <span class="font-semibold">{{ $service->duration_minutes }} min</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-white/70">Tarif</span>
                                <div class="text-3xl font-bold">{{ number_format($service->price, 2, ',', ' ') }} €</div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-black/30 border border-white/10 p-5">
                            <p class="text-sm font-semibold text-white mb-2">Comment ça marche ?</p>
                            <ul class="text-sm text-white/80 space-y-2 list-disc list-inside">
                                <li>Remplissez vos informations pour créer la réservation.</li>
                                <li>Vous serez redirigé(e) vers Stripe pour régler en ligne.</li>
                                <li>Dès le paiement validé, la réservation apparaît dans l'espace coach.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
