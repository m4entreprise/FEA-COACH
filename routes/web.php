<?php

use App\Http\Controllers\Admin\AdminCoachController;
use App\Http\Controllers\CoachSiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\BrandingController;
use App\Http\Controllers\Dashboard\ContentController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Dashboard\GalleryController;
use App\Http\Controllers\Dashboard\PlansController;
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
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/coaches', [AdminCoachController::class, 'index'])->name('admin.coaches.index');
    Route::get('/coaches/create', [AdminCoachController::class, 'create'])->name('admin.coaches.create');
    Route::post('/coaches', [AdminCoachController::class, 'store'])->name('admin.coaches.store');
    Route::get('/coaches/{coach}/edit', [AdminCoachController::class, 'edit'])->name('admin.coaches.edit');
    Route::patch('/coaches/{coach}', [AdminCoachController::class, 'update'])->name('admin.coaches.update');
    Route::delete('/coaches/{coach}', [AdminCoachController::class, 'destroy'])->name('admin.coaches.destroy');
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Main dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Branding management
    Route::get('/dashboard/branding', [BrandingController::class, 'edit'])->name('dashboard.branding');
    Route::post('/dashboard/branding', [BrandingController::class, 'update'])->name('dashboard.branding.update');

    // Content management
    Route::get('/dashboard/content', [ContentController::class, 'edit'])->name('dashboard.content');
    Route::post('/dashboard/content', [ContentController::class, 'update'])->name('dashboard.content.update');

    // Gallery management
    Route::get('/dashboard/gallery', [GalleryController::class, 'index'])->name('dashboard.gallery');
    Route::post('/dashboard/gallery', [GalleryController::class, 'store'])->name('dashboard.gallery.store');
    Route::delete('/dashboard/gallery/{transformation}', [GalleryController::class, 'destroy'])->name('dashboard.gallery.destroy');

    // Plans management
    Route::get('/dashboard/plans', [PlansController::class, 'index'])->name('dashboard.plans');
    Route::post('/dashboard/plans', [PlansController::class, 'store'])->name('dashboard.plans.store');
    Route::patch('/dashboard/plans/{plan}', [PlansController::class, 'update'])->name('dashboard.plans.update');
    Route::delete('/dashboard/plans/{plan}', [PlansController::class, 'destroy'])->name('dashboard.plans.destroy');

    // FAQ management
    Route::get('/dashboard/faq', [FaqController::class, 'index'])->name('dashboard.faq');
    Route::post('/dashboard/faq', [FaqController::class, 'store'])->name('dashboard.faq.store');
    Route::patch('/dashboard/faq/{faq}', [FaqController::class, 'update'])->name('dashboard.faq.update');
    Route::delete('/dashboard/faq/{faq}', [FaqController::class, 'destroy'])->name('dashboard.faq.destroy');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
