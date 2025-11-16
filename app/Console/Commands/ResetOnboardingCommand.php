<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetOnboardingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onboarding:reset {email? : L\'email de l\'utilisateur (optionnel, tous si omis)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Réinitialise le statut de complétion du tutoriel d\'onboarding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            // Réinitialiser pour un utilisateur spécifique
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("❌ Aucun utilisateur trouvé avec l'email : {$email}");
                return 1;
            }

            $user->has_completed_onboarding = false;
            $user->save();

            $this->info("✅ Tutoriel réinitialisé pour : {$user->name} ({$user->email})");
        } else {
            // Réinitialiser pour tous les utilisateurs
            if (!$this->confirm('⚠️  Êtes-vous sûr de vouloir réinitialiser le tutoriel pour TOUS les utilisateurs ?')) {
                $this->info('Opération annulée.');
                return 0;
            }

            $count = User::query()->update(['has_completed_onboarding' => false]);

            $this->info("✅ Tutoriel réinitialisé pour {$count} utilisateur(s)");
        }

        return 0;
    }
}
