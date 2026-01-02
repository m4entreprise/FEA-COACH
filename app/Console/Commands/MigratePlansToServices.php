<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plan;
use App\Models\ServiceType;

class MigratePlansToServices extends Command
{
    protected $signature = 'migrate:plans-to-services {--force : Force migration even if services exist}';
    protected $description = 'Migrate existing Plans to ServiceTypes';

    public function handle()
    {
        $plans = Plan::all();
        
        if ($plans->isEmpty()) {
            $this->info('No plans to migrate.');
            return 0;
        }

        $this->info("Found {$plans->count()} plan(s) to migrate.");

        $migrated = 0;
        $skipped = 0;

        foreach ($plans as $plan) {
            // Check if service already exists for this plan
            $exists = ServiceType::where('coach_id', $plan->coach_id)
                ->where('name', $plan->name)
                ->exists();

            if ($exists && !$this->option('force')) {
                $this->warn("Service '{$plan->name}' already exists for coach {$plan->coach_id}, skipping...");
                $skipped++;
                continue;
            }

            try {
                $service = ServiceType::create([
                    'coach_id' => $plan->coach_id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'duration_minutes' => 60, // Default 1 hour
                    'price' => $plan->price,
                    'currency' => 'EUR',
                    'is_active' => $plan->is_active,
                    'booking_enabled' => true, // Enable booking by default
                    'order' => $plan->order,
                ]);

                $this->info("✓ Migrated: '{$plan->name}' → Service ID {$service->id}");
                $migrated++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to migrate '{$plan->name}': {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Migration completed:");
        $this->info("  - Migrated: {$migrated}");
        $this->info("  - Skipped: {$skipped}");

        if ($migrated > 0) {
            $this->newLine();
            $this->comment('Next steps:');
            $this->comment('1. Verify services in dashboard: /dashboard/services');
            $this->comment('2. Check your coach site to see services displayed');
            $this->comment('3. Once verified, you can optionally delete old plans');
        }

        return 0;
    }
}
