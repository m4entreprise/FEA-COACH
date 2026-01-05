<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions légales — {{ $coach->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '{{ $coach->color_primary ?? "#7c3aed" }}',
                        secondary: '{{ $coach->color_secondary ?? "#ec4899" }}',
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .legal-content h1 { font-size: 2rem; margin-top: 2rem; font-weight: 700; color: #0f172a; }
        .legal-content h2 { font-size: 1.5rem; margin-top: 1.8rem; font-weight: 600; color: #111827; }
        .legal-content h3 { font-size: 1.25rem; margin-top: 1.5rem; font-weight: 600; color: #1f2937; }
        .legal-content p, .legal-content li { color: #334155; line-height: 1.7; }
        .legal-content ul { list-style: disc; margin-left: 1.5rem; }
        .legal-content ol { list-style: decimal; margin-left: 1.5rem; }
        .legal-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .legal-content table th,
        .legal-content table td { border: 1px solid #e2e8f0; padding: 0.75rem 1rem; text-align: left; }
        .legal-content blockquote { border-left: 4px solid rgba(124,58,237,0.4); padding-left: 1rem; color: #475569; font-style: italic; }
    </style>
</head>
<body class="bg-slate-950 text-white antialiased" x-data="legalNavigation()" x-init="init()">
    <script>
        function legalNavigation() {
            return {
                activeSection: 'cgv',
                observer: null,
                init() {
                    this.observeSections();
                },
                observeSections() {
                    const sections = ['cgv', 'privacy'];
                    const options = { rootMargin: '-40% 0px -40% 0px' };
                    this.observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                this.activeSection = entry.target.id;
                            }
                        });
                    }, options);
                    sections.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) {
                            this.observer.observe(el);
                        }
                    });
                },
                scrollTo(id) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth' });
                        this.activeSection = id;
                    }
                }
            }
        }
    </script>
    @php
        $primary = $coach->color_primary ?? '#7c3aed';
        $secondary = $coach->color_secondary ?? '#ec4899';
        $lastUpdate = optional($coach->updated_at)->format('d/m/Y') ?? now()->format('d/m/Y');
        $contactEmail = $coach->user->email ?? ('contact@' . parse_url(config('app.url'), PHP_URL_HOST));
        $contactPhone = $coach->user->phone_contact ?? null;
    @endphp

    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-slate-950">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-950 to-black"></div>
            <div class="absolute -top-32 -right-20 w-[500px] h-[500px] rounded-full mix-blend-screen blur-[180px]" style="background: radial-gradient(circle, {{ $primary }}40, transparent 65%);"></div>
            <div class="absolute top-32 -left-10 w-[420px] h-[420px] rounded-full mix-blend-screen blur-[140px]" style="background: radial-gradient(circle, {{ $secondary }}35, transparent 70%);"></div>
        </div>

        <div class="relative z-10">
            <header class="px-4 sm:px-6 lg:px-8 pt-10 pb-6">
                <div class="max-w-6xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center overflow-hidden">
                            @if($coach->getFirstMediaUrl('logo'))
                                <img src="{{ $coach->getFirstMediaUrl('logo') }}" alt="Logo {{ $coach->name }}" class="h-full w-full object-contain p-2">
                            @else
                                <span class="text-2xl font-bold">{{ Str::substr($coach->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.35em] text-white/60 mb-1">Mentions légales</p>
                            <h1 class="text-3xl sm:text-4xl font-semibold">{{ $coach->name }}</h1>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="/" class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-5 py-2 text-sm font-medium text-white hover:bg-white/10 transition">
                            <x-lucide-arrow-left class="w-4 h-4" />
                            Retour au site
                        </a>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-5 py-2 text-sm text-white/80">
                            <x-lucide-clock-8 class="w-4 h-4 text-white/60" />
                            Dernière mise à jour : {{ $lastUpdate }}
                        </span>
                    </div>
                </div>
            </header>

            <main class="px-4 sm:px-6 lg:px-8 pb-16">
                <div class="max-w-6xl mx-auto grid lg:grid-cols-[320px,1fr] gap-8">
                    <aside class="space-y-6">
                        <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl p-6">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="h-12 w-12 rounded-2xl bg-white/10 flex items-center justify-center">
                                    <x-lucide-shield-check class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.35em] text-white/60">Conformité</p>
                                    <p class="font-semibold">Document certifié</p>
                                </div>
                            </div>
                            <ul class="space-y-4 text-sm text-white/80">
                                <li class="flex items-start gap-3">
                                    <x-lucide-mail class="w-4 h-4 mt-0.5 text-white/60" />
                                    <div>
                                        <p class="text-white/60 text-xs uppercase tracking-widest">Contact</p>
                                        <a href="mailto:{{ $contactEmail }}" class="font-medium hover:text-white">{{ $contactEmail }}</a>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3">
                                    <x-lucide-phone class="w-4 h-4 mt-0.5 text-white/60" />
                                    <div>
                                        <p class="text-white/60 text-xs uppercase tracking-widest">Téléphone</p>
                                        <span class="font-medium">{{ $contactPhone ?? 'Non communiqué' }}</span>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3">
                                    <x-lucide-scale class="w-4 h-4 mt-0.5 text-white/60" />
                                    <div>
                                        <p class="text-white/60 text-xs uppercase tracking-widest">Juridiction</p>
                                        <span class="font-medium">{{ $coach->tribunal_city ?? 'Tribunal compétent communiqué dans les CGV' }}</span>
                                    </div>
                                </li>
                            </ul>
                            <div class="mt-6 rounded-2xl bg-white/5 p-4 text-xs text-white/70 leading-relaxed border border-white/10">
                                <p>Document automatiquement synchronisé avec les informations légales en vigueur.</p>
                            </div>
                        </div>

                        @if(!empty($legalHtml))
                            <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl p-6 space-y-5 sticky top-8">
                                <p class="text-xs uppercase tracking-[0.35em] text-white/60">Navigation rapide</p>
                                <div class="space-y-4">
                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 transition" x-bind:class="activeSection === 'cgv' ? 'border-white/40 bg-white/10 shadow-lg shadow-black/30' : ''">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/10 text-sm font-semibold">01</span>
                                            <div>
                                                <p class="text-[11px] uppercase tracking-[0.3em] text-white/50">Cadre contractuel</p>
                                                <h3 class="text-base font-semibold">Conditions Générales de Vente</h3>
                                            </div>
                                        </div>
                                        <p class="text-white/60 text-sm mb-3">Services proposés, obligations mutuelles, paiement, annulation.</p>
                                        <button @click="scrollTo('cgv')" class="inline-flex items-center gap-2 text-sm font-semibold text-white hover:text-white/90">
                                            <x-lucide-file-text class="w-4 h-4" />
                                            Consulter la section
                                            <x-lucide-arrow-up-right class="w-4 h-4 text-white/60" />
                                        </button>
                                    </div>
                                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 transition" x-bind:class="activeSection === 'privacy' ? 'border-white/40 bg-white/10 shadow-lg shadow-black/30' : ''">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-white/10 text-sm font-semibold">02</span>
                                            <div>
                                                <p class="text-[11px] uppercase tracking-[0.3em] text-white/50">Données & vie privée</p>
                                                <h3 class="text-base font-semibold">Politique de confidentialité</h3>
                                            </div>
                                        </div>
                                        <p class="text-white/60 text-sm mb-3">Données collectées, finalités, droits RGPD et sécurité appliquée.</p>
                                        <button @click="scrollTo('privacy')" class="inline-flex items-center gap-2 text-sm font-semibold text-white hover:text-white/90">
                                            <x-lucide-lock class="w-4 h-4" />
                                            Consulter la section
                                            <x-lucide-arrow-up-right class="w-4 h-4 text-white/60" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </aside>

                    <section class="space-y-8">
                        <div class="rounded-3xl border border-white/10 bg-white/10 backdrop-blur-2xl p-8 shadow-2xl shadow-black/30">
                            <div class="flex flex-col gap-4 mb-6">
                                <span class="inline-flex items-center gap-2 w-fit rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-white/70">
                                    <x-lucide-badge-check class="w-3.5 h-3.5" />
                                    Document officiel
                                </span>
                                <h2 class="text-3xl sm:text-4xl font-semibold">Mentions légales & Conditions Générales</h2>
                                <p class="text-white/70 text-base leading-relaxed">
                                    Retrouvez ci-dessous l’ensemble des informations légales, conditions générales de vente, politique de confidentialité et cadre juridique applicable aux services proposés par {{ $coach->name }}.
                                </p>
                            </div>

                            @if(!empty($legalHtml))
                                <div class="legal-content bg-white rounded-3xl p-8 text-slate-900 shadow-[0_20px_45px_rgba(15,23,42,0.15)]">
                                    {!! $legalHtml !!}
                                </div>
                            @else
                                <div class="rounded-3xl border border-dashed border-white/30 bg-white/5 p-8 text-center">
                                    <x-lucide-info class="w-10 h-10 mx-auto text-white/50 mb-4" />
                                    <p class="text-white/80 font-medium text-lg mb-2">Mentions légales en cours de rédaction</p>
                                    <p class="text-white/60 text-sm">Cette page sera prochainement mise à jour avec les informations légales complètes du coach.</p>
                                </div>
                            @endif

                            <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="text-sm text-white/60 flex items-center gap-2">
                                    <x-lucide-shield class="w-4 h-4" />
                                    Conformes au RGPD & code de droit économique.
                                </div>
                                <a href="/" class="inline-flex items-center gap-2 rounded-full px-6 py-3 font-semibold text-sm text-slate-900 bg-white hover:bg-slate-100 transition shadow-lg shadow-black/20">
                                    <x-lucide-arrow-left class="w-4 h-4" />
                                    Retourner au site officiel
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
            </main>

            <footer class="px-4 sm:px-6 lg:px-8 pb-10">
                <div class="max-w-6xl mx-auto rounded-3xl border border-white/5 bg-white/5 backdrop-blur-xl px-6 py-6 text-center text-sm text-white/50">
                    © {{ now()->year }} {{ $coach->name }} — Tous droits réservés. Contenus hébergés sur {{ config('app.name', 'Ignite Coach') }}.
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
