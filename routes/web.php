<?php

use App\Http\Controllers\Admin\AdminCoachController;
use App\Http\Controllers\Admin\PromoCodeRequestController;
use App\Http\Controllers\Admin\SupportTicketController as AdminSupportController;
use App\Http\Controllers\CoachSiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dashboard\BrandingController;
use App\Http\Controllers\Dashboard\ContentController;
use App\Http\Controllers\Dashboard\FaqController;
use App\Http\Controllers\Dashboard\GalleryController;
use App\Http\Controllers\Dashboard\PlansController;
use App\Http\Controllers\Dashboard\ContactController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\LegalController;
use App\Http\Controllers\Dashboard\SubscriptionController;
use App\Http\Controllers\Dashboard\SupportTicketController as DashboardSupportController;
use App\Http\Controllers\LemonSqueezyWebhookController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SetupWizardController;
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
        Route::post('/contact', [CoachSiteController::class, 'contact'])->name('coach.contact');
        Route::get('/mentions-legales', [CoachSiteController::class, 'legal'])->name('coach.legal');
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

// Legal pages
Route::get('/cgvu', [App\Http\Controllers\LegalController::class, 'cgvu'])->name('legal.cgvu');
Route::get('/politique-confidentialite', [App\Http\Controllers\LegalController::class, 'privacy'])->name('legal.privacy');

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
    
    // Promo code requests
    Route::get('/promo-requests', [PromoCodeRequestController::class, 'index'])->name('admin.promo-requests.index');
    Route::post('/promo-requests/generate-batch', [PromoCodeRequestController::class, 'generateBatch'])->name('admin.promo-requests.generate-batch');
    Route::post('/promo-requests/{promoCodeRequest}/approve', [PromoCodeRequestController::class, 'approve'])->name('admin.promo-requests.approve');
    Route::post('/promo-requests/{promoCodeRequest}/reject', [PromoCodeRequestController::class, 'reject'])->name('admin.promo-requests.reject');

    // Support tickets management
    Route::get('/support-tickets', [AdminSupportController::class, 'index'])->name('admin.support-tickets.index');
    Route::post('/support-tickets/{supportTicket}/reply', [AdminSupportController::class, 'reply'])->name('admin.support-tickets.reply');
    Route::patch('/support-tickets/{supportTicket}/status', [AdminSupportController::class, 'updateStatus'])->name('admin.support-tickets.status');
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
    Route::post('/request-promo', [OnboardingController::class, 'requestPromoCode'])->name('onboarding.request-promo');
    Route::post('/validate-promo', [OnboardingController::class, 'validatePromoCode'])->name('onboarding.validate-promo');
    Route::post('/process-payment', [OnboardingController::class, 'processPayment'])->name('onboarding.process-payment');
});

/*
|--------------------------------------------------------------------------
| Setup Wizard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'onboarding.completed'])->prefix('setup')->group(function () {
    Route::get('/', [SetupWizardController::class, 'index'])->name('setup.index');
    Route::get('/step/{step}', [SetupWizardController::class, 'showStep'])->name('setup.step');
    Route::post('/check-slug', [SetupWizardController::class, 'checkSlugAvailability'])->name('setup.check-slug');
    Route::post('/step/1', [SetupWizardController::class, 'saveStep1'])->name('setup.step1.save');
    Route::post('/step/2', [SetupWizardController::class, 'saveStep2'])->name('setup.step2.save');
    Route::post('/step/3', [SetupWizardController::class, 'saveStep3'])->name('setup.step3.save');
    Route::post('/step/4', [SetupWizardController::class, 'saveStep4'])->name('setup.step4.save');
    Route::post('/step/5', [SetupWizardController::class, 'saveStep5'])->name('setup.step5.save');
    Route::post('/skip/{step}', [SetupWizardController::class, 'skipStep'])->name('setup.skip');
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'onboarding.completed', 'setup.completed'])->group(function () {
    // Main dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-coach-beta', [DashboardController::class, 'beta'])->name('dashboard.coach.beta');
    Route::post('/dashboard/onboarding/complete', [DashboardController::class, 'completeOnboarding'])->name('dashboard.onboarding.complete');

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

    // Contact messages management
    Route::get('/dashboard/contact', [ContactController::class, 'index'])->name('dashboard.contact');
    Route::patch('/dashboard/contact/{contactMessage}/read', [ContactController::class, 'markAsRead'])->name('dashboard.contact.read');
    Route::delete('/dashboard/contact/{contactMessage}', [ContactController::class, 'destroy'])->name('dashboard.contact.destroy');

    // Support tickets (coach/user)
    Route::get('/dashboard/support', [DashboardSupportController::class, 'index'])->name('dashboard.support');
    Route::post('/dashboard/support', [DashboardSupportController::class, 'store'])->name('dashboard.support.store');
    Route::post('/dashboard/support/{supportTicket}/reply', [DashboardSupportController::class, 'reply'])->name('dashboard.support.reply');
    Route::post('/dashboard/support/{supportTicket}/close', [DashboardSupportController::class, 'close'])->name('dashboard.support.close');

    // FAQ management
    Route::get('/dashboard/faq', [FaqController::class, 'index'])->name('dashboard.faq');
    Route::post('/dashboard/faq', [FaqController::class, 'store'])->name('dashboard.faq.store');
    Route::patch('/dashboard/faq/{faq}', [FaqController::class, 'update'])->name('dashboard.faq.update');
    Route::delete('/dashboard/faq/{faq}', [FaqController::class, 'destroy'])->name('dashboard.faq.destroy');

    // Clients management
    Route::get('/dashboard/clients', [ClientController::class, 'index'])->name('dashboard.clients.index');
    Route::post('/dashboard/clients', [ClientController::class, 'store'])->name('dashboard.clients.store');
    Route::patch('/dashboard/clients/{client}', [ClientController::class, 'update'])->name('dashboard.clients.update');
    Route::delete('/dashboard/clients/{client}', [ClientController::class, 'destroy'])->name('dashboard.clients.destroy');
    
    // Client notes management
    Route::post('/dashboard/clients/{client}/notes', [ClientController::class, 'storeNote'])->name('dashboard.clients.notes.store');
    Route::patch('/dashboard/clients/notes/{note}', [ClientController::class, 'updateNote'])->name('dashboard.clients.notes.update');
    Route::delete('/dashboard/clients/notes/{note}', [ClientController::class, 'destroyNote'])->name('dashboard.clients.notes.destroy');

    // Legal terms management
    Route::get('/dashboard/legal', [LegalController::class, 'edit'])->name('dashboard.legal');
    Route::post('/dashboard/legal', [LegalController::class, 'update'])->name('dashboard.legal.update');

    // Subscription management
    Route::get('/dashboard/subscription', [SubscriptionController::class, 'index'])->name('dashboard.subscription');
    Route::post('/dashboard/subscription/checkout', [SubscriptionController::class, 'createCheckoutSession'])->name('dashboard.subscription.checkout');
    Route::post('/dashboard/subscription/portal', [SubscriptionController::class, 'customerPortal'])->name('dashboard.subscription.portal');
    Route::post('/dashboard/subscription/cancel', [SubscriptionController::class, 'cancelSubscription'])->name('dashboard.subscription.cancel');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Lemon Squeezy Webhook Routes
|--------------------------------------------------------------------------
|
| These routes handle webhooks from Lemon Squeezy for subscription events.
| The webhook is public but protected by signature verification.
|
*/

Route::post('/webhooks/lemonsqueezy', [LemonSqueezyWebhookController::class, 'handle'])
    ->name('webhooks.lemonsqueezy')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| Fungies.io Webhook Routes
|--------------------------------------------------------------------------
|
| Disabled for MVP (Lemon Squeezy only)
|
*/
