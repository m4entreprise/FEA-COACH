# Problème : Route de Réservation 404

## Description du Problème

**URL affectée :** `https://m4-entreprise-1.unicoach.app/reserver/1/checkout`
**Méthode :** POST
**Erreur :** 404 Not Found

Le bouton "Réserver" sur le site public du coach (sous-domaine `m4-entreprise-1.unicoach.app`) envoie une requête POST vers `/reserver/{service}/checkout` mais retourne systématiquement une erreur 404.

## Configuration Actuelle

### Fichier .env (Production)
```env
APP_DOMAIN=unicoach.app
APP_URL=https://unicoach.app
SESSION_DOMAIN=.unicoach.app
```

### Route Définie (routes/web.php)

**Ligne 42-51 :** Route dans groupe de domaine
```php
Route::domain('{coach_slug}.' . config('app.domain', 'localhost'))
    ->middleware(['web', 'resolve.coach'])
    ->group(function () {
        Route::get('/', [CoachSiteController::class, 'show'])->name('coach.site');
        Route::post('/contact', [CoachSiteController::class, 'contact'])->name('coach.contact');
        
        // Direct booking checkout
        Route::post('/reserver/{service}/checkout', [BookingController::class, 'directCheckout'])->name('coach.booking.checkout');
    });
```

**Ligne 54-56 :** Route de fallback (ajoutée pour debug)
```php
Route::middleware(['web', 'resolve.coach'])->group(function () {
    Route::post('/reserver/{service}/checkout', [BookingController::class, 'directCheckout'])->name('booking.checkout.fallback');
});
```

### Contrôleur (app/Http/Controllers/BookingController.php)

```php
public function directCheckout(Request $request, $service)
{
    \Log::info('DirectCheckout called', ['service_id' => $service, 'request_data' => $request->all()]);
    
    $coach = app(Coach::class);
    $service = ServiceType::findOrFail($service);
    
    $validated = $request->validate([
        'client_email' => 'required|email|max:255',
    ]);

    try {
        $booking = $this->bookingService->createBooking([
            'service_type_id' => $service->id,
            'booking_date' => null,
            'start_time' => null,
            'client_email' => $validated['client_email'],
            'client_first_name' => null,
            'client_last_name' => null,
            'client_phone' => null,
            'client_notes' => null,
        ]);

        $checkoutSession = $this->stripeService->createCheckoutSession($booking);

        return redirect($checkoutSession['url']);
    } catch (\Exception $e) {
        \Log::error('DirectCheckout failed', ['error' => $e->getMessage()]);
        return back()->with('error', 'Erreur lors de la création de la réservation: ' . $e->getMessage());
    }
}
```

### Formulaire dans les Vues Blade

**Fichiers concernés :**
- `resources/views/coach-site/layouts/minimal.blade.php`
- `resources/views/coach-site/layouts/classic.blade.php`
- `resources/views/coach-site/layouts/bold.blade.php`

```blade
<form action="/reserver/{{ $service->id }}/checkout" method="POST" class="inline-block">
    @csrf
    <input type="hidden" name="client_email" value="booking@temp.com">
    <button type="submit">Réserver</button>
</form>
```

## Actions Déjà Effectuées

1. ✅ Vérification de la définition de la route dans `routes/web.php`
2. ✅ Vérification de l'existence de la méthode `directCheckout` dans `BookingController`
3. ✅ Remplacement des appels `route()` par des URLs relatives pour éviter les erreurs de génération d'URL
4. ✅ Ajout d'une route de fallback sans contrainte de domaine
5. ✅ Exécution de `php artisan route:cache`, `php artisan view:clear`, `php artisan cache:clear`
6. ✅ Vérification de la configuration `APP_DOMAIN` dans `.env`
7. ✅ Déploiement multiple avec cache clearing automatique

## Ce Qui Fonctionne

- ✅ La page d'accueil du coach (`/`) fonctionne
- ✅ Le formulaire de contact (`POST /contact`) fonctionne
- ✅ Les autres routes du groupe de domaine fonctionnent
- ✅ Le middleware `resolve.coach` s'exécute correctement

## Logs

**Aucun log** dans `storage/logs/laravel.log` indiquant que `DirectCheckout called` n'a été loggué, ce qui suggère que la méthode n'est jamais atteinte.

## Hypothèses à Vérifier

### 1. Problème de Serveur Web (Nginx/Apache)

La route pourrait être bloquée au niveau du serveur web avant d'atteindre Laravel.

**À vérifier :**
- Configuration Nginx/Apache pour les routes POST
- Règles de réécriture d'URL
- Logs d'erreur du serveur web (`/var/log/nginx/error.log` ou `/var/log/apache2/error.log`)

**Commande de diagnostic :**
```bash
php artisan route:list --path=reserver
```

**Résultat attendu :**
```
POST   m4-entreprise-1.unicoach.app/reserver/{service}/checkout
```

### 2. Problème de Cache de Routes Persistant

Malgré les clears, il pourrait y avoir un cache intermédiaire (OPcache, Varnish, Cloudflare).

**À tester :**
```bash
# Sur le serveur
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize:clear

# Redémarrer PHP-FPM
sudo systemctl restart php8.2-fpm

# Si Nginx
sudo systemctl restart nginx
```

### 3. Problème de Contrainte de Domaine Laravel

Le pattern `{coach_slug}.' . config('app.domain')` pourrait ne pas matcher correctement.

**À tester :**
Vérifier la valeur réelle de `config('app.domain')` en production :
```php
// Ajouter temporairement dans routes/web.php
Route::get('/debug-config', function() {
    return [
        'app.domain' => config('app.domain'),
        'app.url' => config('app.url'),
        'request.host' => request()->getHost(),
    ];
});
```

Accéder à : `https://m4-entreprise-1.unicoach.app/debug-config`

### 4. Problème de Middleware

Le middleware `resolve.coach` pourrait bloquer ou ne pas s'exécuter correctement.

**À vérifier dans `app/Http/Middleware/ResolveCoachFromHost.php` :**
```php
// Ajouter des logs
\Log::info('ResolveCoachFromHost middleware', [
    'host' => $request->getHost(),
    'path' => $request->path(),
    'method' => $request->method(),
]);
```

### 5. Conflit de Routes

Une autre route pourrait intercepter la requête avant.

**À vérifier :**
```bash
php artisan route:list --method=POST | grep reserver
```

## Solution de Contournement Temporaire

En attendant de résoudre le problème, créer une route simple sans sous-domaine :

**Dans `routes/web.php` :**
```php
Route::post('/booking-direct/{service}', [BookingController::class, 'directCheckout'])
    ->middleware(['web'])
    ->name('booking.direct');
```

**Dans les vues Blade :**
```blade
<form action="/booking-direct/{{ $service->id }}" method="POST">
```

## Prochaines Étapes Recommandées

1. **Exécuter la commande de diagnostic** : `php artisan route:list --path=reserver` sur le serveur
2. **Vérifier les logs du serveur web** (Nginx/Apache) pour voir si la requête arrive
3. **Tester la route de debug** pour vérifier la configuration
4. **Vérifier les permissions** sur le dossier `storage/framework/cache`
5. **Redémarrer PHP-FPM et Nginx/Apache**
6. **Tester avec un client HTTP** (curl/Postman) pour exclure un problème de formulaire

## Commandes de Diagnostic

```bash
# Sur le serveur de production
cd /home/ploi/unicoach.app

# Vérifier les routes
php artisan route:list --path=reserver

# Vérifier la config
php artisan config:show app

# Vérifier les logs Laravel
tail -f storage/logs/laravel.log

# Vérifier les logs Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log | grep reserver

# Test direct avec curl
curl -X POST https://m4-entreprise-1.unicoach.app/reserver/1/checkout \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "_token=CSRF_TOKEN&client_email=test@test.com" \
  -v
```

## Fichiers Modifiés

- `routes/web.php` (ajout routes de réservation)
- `app/Http/Controllers/BookingController.php` (méthode `directCheckout`)
- `resources/views/coach-site/layouts/minimal.blade.php` (formulaire)
- `resources/views/coach-site/layouts/classic.blade.php` (formulaire)
- `resources/views/coach-site/layouts/bold.blade.php` (formulaire)
- `app/Services/BookingService.php` (support dates nullable)
- `database/migrations/2026_01_03_002600_make_booking_dates_nullable.php` (nouvelle migration)

## Contact

Pour toute question sur cette problématique, se référer aux commits récents sur la branche `stripe` du repository GitHub.
