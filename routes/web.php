<?php

use App\Http\Controllers\Admin\AdminCoachController;
use App\Http\Controllers\CoachSiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\BrandingController;
use App\Http\Controllers\Dashboard\ContentController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Dashboard\GalleryController;
use App\Http\Controllers\Dashboard\PlansController;
use App\Http\Controllers\OnboardingController;
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
| Onboarding Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('onboarding')->group(function () {
    Route::get('/step1', [OnboardingController::class, 'step1'])->name('onboarding.step1');
    Route::post('/step1', [OnboardingController::class, 'storeStep1'])->name('onboarding.step1.store');
    
    Route::get('/step2', [OnboardingController::class, 'step2'])->name('onboarding.step2');
    Route::post('/step2', [OnboardingController::class, 'storeStep2'])->name('onboarding.step2.store');
    
    Route::get('/step3', [OnboardingController::class, 'step3'])->name('onboarding.step3');
    Route::post('/validate-promo', [OnboardingController::class, 'validatePromoCode'])->name('onboarding.validate-promo');
    Route::post('/process-payment', [OnboardingController::class, 'processPayment'])->name('onboarding.process-payment');
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
    Route::post('/dashboard/content/profile-photo', [ContentController::class, 'uploadProfilePhoto'])->name('dashboard.content.profile-photo.upload');
    Route::delete('/dashboard/content/profile-photo', [ContentController::class, 'deleteProfilePhoto'])->name('dashboard.content.profile-photo.delete');

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
