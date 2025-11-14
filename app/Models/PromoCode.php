<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoCode extends Model
{
    protected $fillable = [
        'batch_id',
        'code',
        'status',
        'assigned_to',
        'assigned_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PromoCodeBatch::class, 'batch_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
