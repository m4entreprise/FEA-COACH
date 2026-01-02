<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'super@admin.be'],
            [
                'name' => 'Super Admin',
                'email' => 'super@admin.be',
                'password' => Hash::make('rootroot'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'has_completed_onboarding' => true,
            ]
        );
    }
}
