#!/bin/bash

# Script de déploiement du tutoriel d'onboarding

echo "🚀 Déploiement du tutoriel d'onboarding..."

# 1. Mettre à jour l'autoloader
echo "🔄 Mise à jour de l'autoloader Composer..."
composer dump-autoload

# 2. Exécuter la migration
echo "📊 Migration de la base de données..."
php artisan migrate --force

# 3. Vider les caches
echo "🧹 Nettoyage des caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Recompiler les assets si nécessaire
echo "📦 Compilation des assets..."
npm run build

# 5. Optimiser pour la production
echo "⚡ Optimisation..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Déploiement terminé !"
echo ""
echo "ℹ️  Pour tester le tutoriel, connectez-vous et le tutoriel devrait s'afficher automatiquement."
echo "ℹ️  Vous pouvez aussi cliquer sur le bouton 'Tutoriel' en haut à droite du Dashboard."
echo ""
echo "🔧 Pour réinitialiser le tutoriel pour un utilisateur :"
echo "   UPDATE users SET has_completed_onboarding = FALSE WHERE email = 'email@example.com';"
