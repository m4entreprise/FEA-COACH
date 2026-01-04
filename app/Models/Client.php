<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'vat_number',
        'share_code',
        'share_token',
        'allergies',
        'dislikes',
        'general_comments',
        'injuries',
        'stress_level',
        'sleep_quality',
        'menstrual_tracking',
        'last_period',
        'grocery_budget',
        'kitchen_equipment',
        'supplements',
        'available_equipment',
        'training_frequency',
        'session_duration',
        'daily_activity',
        'main_goal',
        'deep_motivation',
        'coaching_style',
    ];

    protected $casts = [
        'menstrual_tracking' => 'boolean',
        'last_period' => 'date',
        'kitchen_equipment' => 'array',
        'available_equipment' => 'array',
    ];

    /**
     * Relation avec le coach
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    /**
     * Relation avec les notes
     */
    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(ClientMeasurement::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function latestMeasurement()
    {
        return $this->hasOne(ClientMeasurement::class)->latestOfMany();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ClientMessage::class);
    }

    /**
     * Nom complet du client
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Compter les messages non lus pour ce client
     */
    public function unreadMessagesCount(): int
    {
        return $this->messages()->unread()->fromSender('coach')->count();
    }
}
