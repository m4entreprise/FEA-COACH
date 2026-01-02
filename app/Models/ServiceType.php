<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'max_advance_booking_days',
        'min_advance_booking_hours',
        'order',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'booking_enabled' => 'boolean',
        'max_advance_booking_days' => 'integer',
        'min_advance_booking_hours' => 'integer',
        'order' => 'integer',
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
}
