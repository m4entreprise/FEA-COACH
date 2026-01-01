<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plan extends Model
{
    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'price',
        'order',
        'cta_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the coach that owns the plan.
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }
}
