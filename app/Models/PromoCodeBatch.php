<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCodeBatch extends Model
{
    protected $fillable = [
        'created_by',
        'quantity',
        'label',
        'notes',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function codes(): HasMany
    {
        return $this->hasMany(PromoCode::class, 'batch_id');
    }
}
