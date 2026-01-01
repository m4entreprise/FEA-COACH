<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Supprimer les anciens champs qui seront dans l'historique
            $table->dropColumn(['weight', 'height']);
            
            // Bloc B : Santé & Physiologie
            $table->text('injuries')->nullable()->after('general_comments')->comment('Blessures et douleurs');
            $table->integer('stress_level')->nullable()->after('injuries')->comment('Niveau de stress (1-10)');
            $table->string('sleep_quality')->nullable()->after('stress_level')->comment('Qualité du sommeil');
            $table->boolean('menstrual_tracking')->default(false)->after('sleep_quality')->comment('Suivi cycle menstruel');
            $table->date('last_period')->nullable()->after('menstrual_tracking')->comment('Date dernières règles');
            
            // Bloc C : Nutrition & Cuisine
            $table->string('grocery_budget')->nullable()->after('last_period')->comment('Budget courses');
            $table->json('kitchen_equipment')->nullable()->after('grocery_budget')->comment('Équipement cuisine');
            $table->text('supplements')->nullable()->after('kitchen_equipment')->comment('Compléments alimentaires');
            
            // Bloc D : Contexte Sportif & Logistique
            $table->json('available_equipment')->nullable()->after('supplements')->comment('Matériel sportif disponible');
            $table->string('training_frequency')->nullable()->after('available_equipment')->comment('Fréquence entraînement');
            $table->string('session_duration')->nullable()->after('training_frequency')->comment('Durée par séance');
            $table->string('daily_activity')->nullable()->after('session_duration')->comment('Activité hors sport');
            
            // Bloc E : Psychologie & Profiling
            $table->text('main_goal')->nullable()->after('daily_activity')->comment('Objectif principal');
            $table->text('deep_motivation')->nullable()->after('main_goal')->comment('Motivation profonde (le pourquoi)');
            $table->string('coaching_style')->nullable()->after('deep_motivation')->comment('Style de coaching préféré');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Remettre les champs weight et height
            $table->decimal('weight', 5, 2)->nullable()->after('email');
            $table->decimal('height', 5, 2)->nullable()->after('weight');
            
            $table->dropColumn([
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
            ]);
        });
    }
};
