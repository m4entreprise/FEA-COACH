<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ActivatePaymentsModule extends Command
{
    protected $signature = 'payments:activate {user_id}';
    protected $description = 'Activer le module paiements pour un utilisateur';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("Utilisateur #{$userId} introuvable");
            return 1;
        }

        if ($user->has_payments_module) {
            $this->info("Le module paiements est déjà activé pour {$user->email}");
            return 0;
        }

        $user->update([
            'has_payments_module' => true,
            'payments_module_activated_at' => now(),
        ]);

        $this->info("✅ Module paiements activé pour {$user->email}");
        $this->info("Date d'activation: " . $user->payments_module_activated_at);

        return 0;
    }
}
