<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomDomain extends Model
{
    protected $fillable = [
        'coach_id',
        'domain',
        'status',
        'requested_domain',
        'purchased_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the coach that owns the custom domain.
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }
}
