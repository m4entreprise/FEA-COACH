<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Coach extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'subdomain',
        'desired_custom_domain',
        'site_layout',
        'color_primary',
        'color_secondary',
        'hero_title',
        'hero_subtitle',
        'about_text',
        'method_text',
        'method_title',
        'method_subtitle',
        'method_step1_title',
        'method_step1_description',
        'method_step2_title',
        'method_step2_description',
        'method_step3_title',
        'method_step3_description',
        'pricing_title',
        'pricing_subtitle',
        'transformations_title',
        'transformations_subtitle',
        'final_cta_title',
        'final_cta_subtitle',
        'cta_text',
        'intermediate_cta_title',
        'intermediate_cta_subtitle',
        'satisfaction_rate',
        'average_rating',
        'legal_terms',
        'is_coaching_presentiel',
        'is_coaching_online',
        'has_digital_products',
        'has_subscriptions',
        'use_client_photos',
        'vat_regime',
        'cancellation_delay',
        'tribunal_city',
        'insurance_company',
        'insurance_policy_number',
        'legal_generation_mode',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'linkedin_url',
        'youtube_url',
        'tiktok_url',
        'is_active',
        'custom_contact_locked_until',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'satisfaction_rate' => 'integer',
        'average_rating' => 'decimal:1',
        'custom_contact_locked_until' => 'datetime',
        'is_coaching_presentiel' => 'boolean',
        'is_coaching_online' => 'boolean',
        'has_digital_products' => 'boolean',
        'has_subscriptions' => 'boolean',
        'use_client_photos' => 'boolean',
        'cancellation_delay' => 'integer',
    ];

    /**
     * Get the user that owns the coach profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transformations for the coach.
     */
    public function transformations(): HasMany
    {
        return $this->hasMany(CoachTransformation::class)->orderBy('order');
    }

    /**
     * Get the plans for the coach.
     */
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Get the FAQs for the coach.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    /**
     * Get the contact messages for the coach.
     */
    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    /**
     * Get the clients for the coach.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get the custom domain for the coach.
     */
    public function customDomain()
    {
        return $this->hasOne(CustomDomain::class);
    }

    /**
     * Get the Stripe account for the coach.
     */
    public function stripeAccount()
    {
        return $this->hasOne(StripeAccount::class);
    }

    /**
     * Get the service types for the coach.
     */
    public function serviceTypes(): HasMany
    {
        return $this->hasMany(ServiceType::class);
    }

    /**
     * Get the availability slots for the coach.
     */
    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class);
    }

    /**
     * Get the bookings for the coach.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the cancellation policy for the coach.
     */
    public function cancellationPolicy()
    {
        return $this->hasOne(BookingCancellationPolicy::class);
    }

    /**
     * Get the site layout with fallback to default if invalid.
     */
    public function getSiteLayoutOrDefaultAttribute(): string
    {
        $key = $this->site_layout ?: config('coach_site.default_layout', 'classic');
        $layouts = config('coach_site.layouts', []);

        return array_key_exists($key, $layouts)
            ? $key
            : config('coach_site.default_layout', 'classic');
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
            ->singleFile()
            ->useFallbackUrl('/images/default-logo.png');

        $this->addMediaCollection('hero')
            ->singleFile()
            ->useFallbackUrl('/images/default-hero.jpg');

        $this->addMediaCollection('profile')
            ->singleFile()
            ->useFallbackUrl('/images/default-profile.svg');
    }
}
