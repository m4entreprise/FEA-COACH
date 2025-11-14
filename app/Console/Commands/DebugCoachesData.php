<?php

namespace App\Console\Commands;

use App\Models\Coach;
use Illuminate\Console\Command;

class DebugCoachesData extends Command
{
    protected $signature = 'coaches:debug';
    protected $description = 'Debug coaches data to see what is missing';

    public function handle()
    {
        $this->info('Debugging coaches data...');
        $this->newLine();

        $coaches = Coach::with('user')->get();

        foreach ($coaches as $coach) {
            $this->info("Coach: {$coach->name} (ID: {$coach->id})");
            $this->line("  Subdomain: " . ($coach->subdomain ?? 'NULL'));
            
            if ($coach->user) {
                $this->line("  User ID: {$coach->user->id}");
                $this->line("  User Email: {$coach->user->email}");
                $this->line("  User Name: {$coach->user->name}");
                $this->line("  First Name: " . ($coach->user->first_name ?? 'NULL'));
                $this->line("  Last Name: " . ($coach->user->last_name ?? 'NULL'));
                $this->line("  Is FEA Graduate: " . ($coach->user->is_fea_graduate ? 'YES' : 'NO'));
                $this->line("  Subscription Status: " . ($coach->user->subscription_status ?? 'NULL'));
                $this->line("  Trial Ends At: " . ($coach->user->trial_ends_at?->format('Y-m-d H:i:s') ?? 'NULL'));
                $this->line("  FEA Promo Code: " . ($coach->user->fea_promo_code ?? 'NULL'));
            } else {
                $this->error("  No user associated!");
            }
            
            $this->newLine();
        }

        $this->info('Debug complete.');
    }
}
