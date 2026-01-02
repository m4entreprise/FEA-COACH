<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckSubscriptionStatus extends Command
{
    protected $signature = 'subscription:check {user_id}';
    protected $description = 'Check subscription status for a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error("User not found: {$userId}");
            return 1;
        }

        $this->info("User Information:");
        $this->line("ID: {$user->id}");
        $this->line("Email: {$user->email}");
        $this->line("Subscription Status: " . ($user->subscription_status ?? 'NULL'));
        $this->line("Trial Ends At: " . ($user->trial_ends_at ?? 'NULL'));
        $this->line("Cancel At Period End: " . ($user->cancel_at_period_end ? 'true' : 'false'));
        $this->line("LS Subscription ID: " . ($user->lemonsqueezy_subscription_id ?? 'NULL'));
        $this->line("LS Customer ID: " . ($user->lemonsqueezy_customer_id ?? 'NULL'));
        $this->line("Period End: " . ($user->subscription_current_period_end ?? 'NULL'));

        // Check if subscription is considered active
        $isOnTrial = $user->trial_ends_at && now()->isBefore($user->trial_ends_at);
        $activeStatuses = ['active', 'on_trial', 'active_promo'];
        $isActive = $isOnTrial || in_array($user->subscription_status, $activeStatuses, true);

        $this->newLine();
        $this->info("Computed Status:");
        $this->line("Is On Trial: " . ($isOnTrial ? 'YES' : 'NO'));
        $this->line("Is Subscription Active: " . ($isActive ? 'YES' : 'NO'));

        if (!$isActive) {
            $this->newLine();
            $this->warn("⚠ Subscription is INACTIVE - site will show unavailable page");
        } else {
            $this->newLine();
            $this->info("✓ Subscription is ACTIVE - site should be available");
        }

        return 0;
    }
}
