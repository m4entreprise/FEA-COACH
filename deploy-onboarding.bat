@echo off
REM Script de déploiement du tutoriel d'onboarding

echo.
echo ======================================
echo Déploiement du tutoriel d'onboarding
echo ======================================
echo.

REM 1. Exécuter la migration
echo [1/4] Migration de la base de données...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERREUR: La migration a échoué !
    pause
    exit /b %errorlevel%
)

REM 2. Vider les caches
echo [2/4] Nettoyage des caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM 3. Recompiler les assets
echo [3/4] Compilation des assets...
call npm run build
if %errorlevel% neq 0 (
    echo AVERTISSEMENT: La compilation des assets a échoué !
    echo Continuez quand même...
)

REM 4. Optimiser pour la production
echo [4/4] Optimisation...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo ======================================
echo Déploiement terminé avec succès !
echo ======================================
echo.
echo Pour tester le tutoriel :
echo - Connectez-vous au Dashboard
echo - Le tutoriel s'affichera automatiquement
echo - Ou cliquez sur le bouton "Tutoriel" en haut à droite
echo.
echo Pour réinitialiser le tutoriel pour un utilisateur :
echo UPDATE users SET has_completed_onboarding = FALSE WHERE email = 'email@example.com';
echo.
pause
