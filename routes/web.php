<?php

use App\Http\Controllers\CoachSiteController;
use App\Http\Controllers\Dashboard\BrandingController;
use App\Http\Controllers\Dashboard\ContentController;
use App\Http\Controllers\Dashboard\GalleryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Coach Public Sites (Wildcard Subdomain)
|--------------------------------------------------------------------------
*/

Route::domain('{coach_slug}.' . config('app.domain', 'localhost'))
    ->middleware(['web', 'resolve.coach'])
    ->group(function () {
        Route::get('/', [CoachSiteController::class, 'show'])->name('coach.site');
    });

/*
|--------------------------------------------------------------------------
| Main Application Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Main dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Branding management
    Route::get('/dashboard/branding', [BrandingController::class, 'edit'])->name('dashboard.branding');
    Route::put('/dashboard/branding', [BrandingController::class, 'update']);

    // Content management
    Route::get('/dashboard/content', [ContentController::class, 'edit'])->name('dashboard.content');
    Route::put('/dashboard/content', [ContentController::class, 'update']);

    // Gallery management
    Route::get('/dashboard/gallery', [GalleryController::class, 'index'])->name('dashboard.gallery');
    Route::post('/dashboard/gallery', [GalleryController::class, 'store']);
    Route::delete('/dashboard/gallery/{transformation}', [GalleryController::class, 'destroy']);

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
