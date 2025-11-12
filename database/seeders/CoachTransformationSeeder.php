<?php

namespace Database\Seeders;

use App\Models\Coach;
use App\Models\CoachTransformation;
use Illuminate\Database\Seeder;

class CoachTransformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coaches = Coach::where('is_active', true)->get();

        foreach ($coaches as $coach) {
            // Transformation 1
            CoachTransformation::create([
                'coach_id' => $coach->id,
                'title' => 'Transformation -15kg en 3 mois',
                'description' => 'Programme intensif combinant musculation et cardio, avec suivi nutritionnel personnalisé.',
                'order' => 1,
            ]);

            // Transformation 2
            CoachTransformation::create([
                'coach_id' => $coach->id,
                'title' => 'Prise de masse musculaire',
                'description' => '+8kg de muscle en 6 mois grâce à un programme de force et une alimentation optimisée.',
                'order' => 2,
            ]);

            // Transformation 3
            CoachTransformation::create([
                'coach_id' => $coach->id,
                'title' => 'Remise en forme post-grossesse',
                'description' => 'Retour à la forme en 4 mois avec un programme adapté et progressif.',
                'order' => 3,
            ]);

            // Transformation 4 (uniquement pour certains coachs)
            if ($coach->id <= 2) {
                CoachTransformation::create([
                    'coach_id' => $coach->id,
                    'title' => 'Préparation marathon',
                    'description' => 'Programme de 12 semaines pour préparer et réussir son premier marathon.',
                    'order' => 4,
                ]);
            }
        }
    }
}
