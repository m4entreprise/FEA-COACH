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
