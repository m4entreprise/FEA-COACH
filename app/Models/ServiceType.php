<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ServiceType extends Model
{
    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'duration_minutes',
        'price',
        'currency',
        'is_active',
        'booking_enabled',
        'is_featured',
        'max_advance_booking_days',
        'min_advance_booking_hours',
        'order',
        'image_path',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'booking_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'max_advance_booking_days' => 'integer',
        'min_advance_booking_hours' => 'integer',
        'order' => 'integer',
    ];

    protected $appends = [
        'image_url',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isBookable(): bool
    {
        return $this->is_active && $this->booking_enabled;
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return url(Storage::url($this->image_path));
    }
}
