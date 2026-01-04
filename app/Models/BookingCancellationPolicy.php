<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCancellationPolicy extends Model
{
    protected $fillable = [
        'coach_id',
        'hours_before_free_cancellation',
        'refund_percentage_before_deadline',
        'refund_percentage_after_deadline',
        'no_show_refund_percentage',
        'policy_text',
    ];

    protected $casts = [
        'hours_before_free_cancellation' => 'integer',
        'refund_percentage_before_deadline' => 'integer',
        'refund_percentage_after_deadline' => 'integer',
        'no_show_refund_percentage' => 'integer',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function getDefaultPolicyText(): string
    {
        return "Annulation gratuite jusqu'à {$this->hours_before_free_cancellation}h avant la séance. " .
               "Après ce délai, remboursement de {$this->refund_percentage_after_deadline}%. " .
               "En cas d'absence sans prévenir, aucun remboursement.";
    }
}
