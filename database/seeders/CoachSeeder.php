<?php

namespace Database\Seeders;

use App\Models\Coach;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Coach 1: Pierre Martin
        $user1 = User::create([
            'name' => 'Pierre Martin',
            'email' => 'pierre@example.com',
            'password' => Hash::make('password'),
            'role' => 'coach',
        ]);

        $coach1 = Coach::create([
            'user_id' => $user1->id,
            'name' => 'Pierre Martin',
            'slug' => 'pierre-martin',
            'subdomain' => 'pierre-martin',
            'color_primary' => '#3b82f6',
            'color_secondary' => '#8b5cf6',
            'hero_title' => 'Transformez votre corps, transformez votre vie',
            'hero_subtitle' => 'Coach sportif certifié - 10 ans d\'expérience',
            'about_text' => 'Passionné de fitness depuis plus de 15 ans, je suis coach sportif diplômé d\'État. Mon approche personnalisée combine nutrition, entraînement fonctionnel et suivi psychologique pour des résultats durables.',
            'method_text' => 'Ma méthode repose sur 3 piliers : un programme d\'entraînement sur mesure, un plan nutritionnel adapté à vos objectifs, et un accompagnement mental pour maintenir votre motivation.',
            'cta_text' => 'Commencer votre transformation',
            'is_active' => true,
        ]);

        $user1->update(['coach_id' => $coach1->id]);

        // Coach 2: Sophie Dubois
        $user2 = User::create([
            'name' => 'Sophie Dubois',
            'email' => 'sophie@example.com',
            'password' => Hash::make('password'),
            'role' => 'coach',
        ]);

        $coach2 = Coach::create([
            'user_id' => $user2->id,
            'name' => 'Sophie Dubois',
            'slug' => 'sophie-dubois',
            'subdomain' => 'sophie-dubois',
            'color_primary' => '#ec4899',
            'color_secondary' => '#f59e0b',
            'hero_title' => 'Révélez votre potentiel',
            'hero_subtitle' => 'Coaching sportif féminin - Spécialiste transformation corporelle',
            'about_text' => 'Ancienne athlète de haut niveau, je me consacre maintenant à aider les femmes à atteindre leurs objectifs fitness. Mon approche bienveillante et exigeante vous permettra de dépasser vos limites.',
            'method_text' => 'Programme 100% féminin : renforcement musculaire, cardio intelligent, nutrition équilibrée. Chaque programme est adapté à votre niveau et vos contraintes.',
            'cta_text' => 'Démarrer maintenant',
            'is_active' => true,
        ]);

        $user2->update(['coach_id' => $coach2->id]);

        // Coach 3: Thomas Leroy (inactif)
        $user3 = User::create([
            'name' => 'Thomas Leroy',
            'email' => 'thomas@example.com',
            'password' => Hash::make('password'),
            'role' => 'coach',
        ]);

        $coach3 = Coach::create([
            'user_id' => $user3->id,
            'name' => 'Thomas Leroy',
            'slug' => 'thomas-leroy',
            'subdomain' => 'thomas-leroy',
            'color_primary' => '#10b981',
            'color_secondary' => '#3b82f6',
            'hero_title' => 'Performance et bien-être',
            'hero_subtitle' => 'Coach sportif - Préparation physique',
            'about_text' => 'Spécialisé dans la préparation physique et la récupération sportive.',
            'method_text' => 'Approche holistique combinant entraînement, nutrition et récupération.',
            'cta_text' => 'Me contacter',
            'is_active' => false,
        ]);

        $user3->update(['coach_id' => $coach3->id]);

        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@fea-coach.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'coach_id' => null,
        ]);
    }
}
