<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeAccount extends Model
{
    protected $fillable = [
        'coach_id',
        'stripe_account_id',
        'onboarding_completed',
        'charges_enabled',
        'payouts_enabled',
        'details_submitted',
        'country',
        'currency',
        'business_type',
    ];

    protected $casts = [
        'onboarding_completed' => 'boolean',
        'charges_enabled' => 'boolean',
        'payouts_enabled' => 'boolean',
        'details_submitted' => 'boolean',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function isFullyActivated(): bool
    {
        return $this->onboarding_completed 
            && $this->charges_enabled 
            && $this->payouts_enabled 
            && $this->details_submitted;
    }

    public function canAcceptPayments(): bool
    {
        return $this->charges_enabled && $this->details_submitted;
    }
}
