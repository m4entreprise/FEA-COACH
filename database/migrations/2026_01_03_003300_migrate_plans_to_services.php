<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Plan;
use App\Models\ServiceType;

return new class extends Migration
{
    public function up(): void
    {
        // Migrer les Plans existants vers ServiceTypes
        $plans = Plan::all();
        
        foreach ($plans as $plan) {
            ServiceType::create([
                'coach_id' => $plan->coach_id,
                'name' => $plan->name,
                'description' => $plan->description,
                'duration_minutes' => 60, // Durée par défaut 1h
                'price' => $plan->price,
                'currency' => 'EUR',
                'is_active' => $plan->is_active,
                'booking_enabled' => true, // Tous réservables par défaut
                'order' => $plan->order,
            ]);
        }
    }

    public function down(): void
    {
        // Optionnel : supprimer les services créés par la migration
        // ServiceType::whereIn('coach_id', Plan::pluck('coach_id'))->delete();
    }
};
