<?php

namespace Database\Seeders;

use App\Models\Coach;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coaches = Coach::all();

        foreach ($coaches as $coach) {
            // Plan Découverte
            Plan::create([
                'coach_id' => $coach->id,
                'name' => 'Découverte',
                'description' => 'Séance d\'essai pour découvrir ma méthode de coaching. Bilan forme complet et objectifs personnalisés.',
                'price' => 49.99,
                'cta_url' => 'https://calendly.com/coach/decouverte',
                'is_active' => true,
            ]);

            // Plan Suivi Mensuel
            Plan::create([
                'coach_id' => $coach->id,
                'name' => 'Suivi Mensuel',
                'description' => '4 séances par mois + programme nutritionnel + suivi quotidien via application.',
                'price' => 199.99,
                'cta_url' => 'https://calendly.com/coach/mensuel',
                'is_active' => true,
            ]);

            // Plan Transformation 3 mois
            Plan::create([
                'coach_id' => $coach->id,
                'name' => 'Transformation 3 mois',
                'description' => 'Programme complet sur 3 mois : 12 séances, plan nutritionnel personnalisé, suivi quotidien, coaching mental.',
                'price' => 549.99,
                'cta_url' => 'https://calendly.com/coach/transformation',
                'is_active' => true,
            ]);

            // Plan Premium (inactif pour certains coachs)
            Plan::create([
                'coach_id' => $coach->id,
                'name' => 'Premium VIP',
                'description' => 'Accompagnement ultra-personnalisé : séances illimitées, disponibilité 7j/7, suivi nutritionnel avancé.',
                'price' => 999.99,
                'cta_url' => 'https://calendly.com/coach/premium',
                'is_active' => $coach->id === 1, // Actif uniquement pour le premier coach
            ]);
        }
    }
}
