<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales - {{ $coach->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $coach->color_primary ?? "#8B5CF6" }}',
                        secondary: '{{ $coach->color_secondary ?? "#EC4899" }}',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Header simple -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @if($coach->getFirstMediaUrl('logo'))
                        <img src="{{ $coach->getFirstMediaUrl('logo') }}" alt="Logo {{ $coach->name }}" class="h-12 w-auto">
                    @endif
                    <h1 class="text-2xl font-bold" style="color: {{ $coach->color_primary ?? '#8B5CF6' }}">
                        {{ $coach->name }}
                    </h1>
                </div>
                <a href="/" class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour au site
                </a>
            </div>
        </div>
    </header>

    <!-- Contenu -->
    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-4xl font-bold mb-8" style="color: {{ $coach->color_primary ?? '#8B5CF6' }}">
                Mentions Légales & CGV
            </h1>

            @if($coach->legal_terms)
                <div class="prose prose-lg max-w-none whitespace-pre-line text-gray-800 leading-relaxed">
                    {{ $coach->legal_terms }}
                </div>
            @else
                <div class="bg-gray-100 border border-gray-300 rounded-lg p-6 text-center">
                    <p class="text-gray-600">
                        Les mentions légales et conditions générales de vente seront bientôt disponibles.
                    </p>
                </div>
            @endif

            <!-- Bouton retour -->
            <div class="mt-8 text-center">
                <a href="/" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg text-white font-semibold shadow-lg hover:shadow-xl transition-all" style="background: linear-gradient(to right, {{ $coach->color_primary ?? '#8B5CF6' }}, {{ $coach->color_secondary ?? '#EC4899' }})">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour au site
                </a>
            </div>
        </div>
    </main>

    <!-- Footer simple -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">
                © {{ date('Y') }} {{ $coach->name }}. Tous droits réservés.
            </p>
        </div>
    </footer>
</body>
</html>
