<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site temporairement indisponible</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full text-center fade-in">
        <!-- Icon -->
        <div class="mb-8 flex justify-center">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full blur-2xl opacity-30"></div>
                <div class="relative bg-slate-900 rounded-full p-8 border border-slate-800">
                    <svg class="w-20 h-20 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-slate-50 mb-4">
            Site temporairement indisponible
        </h1>

        <!-- Message -->
        <p class="text-lg text-slate-300 mb-8 leading-relaxed">
            Ce site web n'est actuellement plus disponible.
        </p>

        <!-- CTA Section -->
        <div class="bg-slate-900/80 backdrop-blur-sm rounded-2xl border border-slate-800 p-8 mb-6">
            <p class="text-slate-200 mb-6">
                Si vous cherchez un coach sportif, découvrez notre annuaire de coachs certifiés :
            </p>
            
            <a 
                href="https://www.fitnesseducation.academy/annuaire" 
                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all shadow-lg hover:shadow-xl transform hover:scale-105"
            >
                <span>Consulter l'annuaire FEA</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>

        <!-- Footer -->
        <div class="text-sm text-slate-500">
            <p>
                <a href="https://www.fitnesseducation.academy" class="hover:text-slate-300 transition-colors">
                    Fitness Education Academy
                </a>
            </p>
        </div>
    </div>
</body>
</html>
