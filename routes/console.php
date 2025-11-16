<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('onboarding:reset {email?}', function ($email = null) {
    if ($email) {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ Aucun utilisateur trouvé avec l'email : {$email}");
            return 1;
        }
        
        $user->has_completed_onboarding = false;
        $user->save();
        
        $this->info("✅ Tutoriel réinitialisé pour : {$user->name} ({$user->email})");
    } else {
        if (!$this->confirm('⚠️  Êtes-vous sûr de vouloir réinitialiser le tutoriel pour TOUS les utilisateurs ?')) {
            $this->info('Opération annulée.');
            return 0;
        }
        
        $count = User::query()->update(['has_completed_onboarding' => false]);
        
        $this->info("✅ Tutoriel réinitialisé pour {$count} utilisateur(s)");
    }
    
    return 0;
})->purpose('Réinitialise le statut de complétion du tutoriel d\'onboarding');
