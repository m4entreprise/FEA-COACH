<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'coach_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'vat_number',
        'date_of_birth',
        'objectives',
        'internal_notes',
        'status',
    ];

    protected $casts = [
        'objectives' => 'array',
        'date_of_birth' => 'date',
    ];

    /**
     * Relation avec le coach
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    /**
     * Relation avec les notes (ancien système)
     */
    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class);
    }

    /**
     * Relation avec les mesures
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(ClientMeasurement::class)->orderBy('measurement_date', 'desc');
    }

    /**
     * Relation avec les documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class)->orderBy('uploaded_at', 'desc');
    }

    /**
     * Relation avec les bilans
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(ClientAssessment::class)->orderBy('assessment_date', 'desc');
    }

    /**
     * Relation avec les activités
     */
    public function activities(): HasMany
    {
        return $this->hasMany(ClientActivity::class)->orderBy('created_at', 'desc');
    }

    /**
     * Nom complet du client
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Âge du client
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
