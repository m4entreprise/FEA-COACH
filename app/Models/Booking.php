<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Booking extends Model
{
    protected $fillable = [
        'coach_id',
        'client_id',
        'service_type_id',
        'client_first_name',
        'client_last_name',
        'client_email',
        'client_phone',
        'booking_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'amount',
        'currency',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'payment_status',
        'paid_at',
        'status',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'client_notes',
        'coach_notes',
        'reminder_sent_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'duration_minutes' => 'integer',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function getClientFullNameAttribute(): string
    {
        if ($this->client) {
            return $this->client->full_name;
        }
        return trim($this->client_first_name . ' ' . $this->client_last_name);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getBookingDateTimeAttribute(): ?Carbon
    {
        if (!$this->booking_date || !$this->start_time) {
            return null;
        }

        return Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->start_time);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'succeeded';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeCancelled(): bool
    {
        if (!in_array($this->status, ['pending', 'confirmed'])) {
            return false;
        }

        if (!$this->booking_date_time) {
            return true;
        }

        return $this->booking_date_time->isFuture();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date')
            ->orderBy('start_time');
    }

    public function scopePast($query)
    {
        return $query->where('booking_date', '<', now()->toDateString())
            ->orWhereIn('status', ['completed', 'no_show'])
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled')
            ->orderByDesc('cancelled_at');
    }
}
