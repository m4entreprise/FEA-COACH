<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'coach_id',
        'first_name',
        'last_name',
        'vat_number',
        'legal_address',
        'is_fea_graduate',
        'fea_promo_code',
        'onboarding_completed',
        'setup_completed',
        'setup_step',
        'stripe_customer_id',
        'subscription_status',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_fea_graduate' => 'boolean',
            'onboarding_completed' => 'boolean',
            'setup_completed' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the coach profile associated with the user.
     */
    public function coach(): HasOne
    {
        return $this->hasOne(Coach::class, 'user_id');
    }
}
