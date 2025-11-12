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
        'cta_text',
        'satisfaction_rate',
        'average_rating',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'satisfaction_rate' => 'integer',
        'average_rating' => 'decimal:1',
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
            ->useFallbackUrl('/images/default-profile.jpg');
    }
}
