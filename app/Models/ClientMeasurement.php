<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'weight',
        'body_measurements',
        'body_fat_percentage',
        'photos',
        'measurement_date',
        'notes',
    ];

    protected $casts = [
        'body_measurements' => 'array',
        'photos' => 'array',
        'weight' => 'decimal:2',
        'body_fat_percentage' => 'decimal:2',
        'measurement_date' => 'date',
    ];

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('measurement_date', [$startDate, $endDate]);
    }
}
