<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ForceSubscriptionSync extends Command
{
    protected $signature = 'subscription:force-sync {user_id} {--status=active} {--subscription-id=}';
    protected $description = 'Force sync subscription status for a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User not found: {$userId}");
            return 1;
        }

        $this->info("Current state:");
        $this->line("Subscription Status: " . ($user->subscription_status ?? 'NULL'));
        $this->line("LS Subscription ID: " . ($user->lemonsqueezy_subscription_id ?? 'NULL'));

        $updates = [
            'subscription_status' => $this->option('status'),
            'trial_ends_at' => null,
            'cancel_at_period_end' => false,
        ];

        if ($this->option('subscription-id')) {
            $updates['lemonsqueezy_subscription_id'] = $this->option('subscription-id');
        }

        $user->update($updates);

        $this->newLine();
        $this->info("✓ Subscription synced successfully!");
        $this->line("New Subscription Status: {$user->subscription_status}");
        
        if ($this->option('subscription-id')) {
            $this->line("New LS Subscription ID: {$user->lemonsqueezy_subscription_id}");
        }

        return 0;
    }
}
