<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'energy_level',
        'difficulty_level',
        'progress_notes',
        'coach_comments',
        'status',
        'assessment_date',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    /**
     * Relation avec le client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Marquer le bilan comme complet
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }
}
