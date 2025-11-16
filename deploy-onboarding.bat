@echo off
REM Script de déploiement du tutoriel d'onboarding

echo.
echo ======================================
echo Déploiement du tutoriel d'onboarding
echo ======================================
echo.

REM 1. Mettre à jour l'autoloader
echo [1/5] Mise à jour de l'autoloader Composer...
composer dump-autoload
if %errorlevel% neq 0 (
    echo AVERTISSEMENT: Composer dump-autoload a échoué !
    echo Continuez quand même...
)

REM 2. Exécuter la migration
echo [2/5] Migration de la base de données...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERREUR: La migration a échoué !
    pause
    exit /b %errorlevel%
)

REM 3. Vider les caches
echo [3/5] Nettoyage des caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM 4. Recompiler les assets
echo [4/5] Compilation des assets...
call npm run build
if %errorlevel% neq 0 (
    echo AVERTISSEMENT: La compilation des assets a échoué !
    echo Continuez quand même...
)

REM 5. Optimiser pour la production
echo [5/5] Optimisation...
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
