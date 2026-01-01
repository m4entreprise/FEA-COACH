<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'lemonsqueezy_customer_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('lemonsqueezy_customer_id')->nullable()->after('subscription_status');
            });
        }

        if (! Schema::hasColumn('users', 'lemonsqueezy_subscription_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('lemonsqueezy_subscription_id')->nullable()->after('lemonsqueezy_customer_id');
            });
        }

        if (Schema::hasColumn('users', 'fungies_customer_id')) {
            DB::statement("UPDATE users SET lemonsqueezy_customer_id = fungies_customer_id WHERE lemonsqueezy_customer_id IS NULL AND fungies_customer_id IS NOT NULL");
        }

        if (Schema::hasColumn('users', 'fungies_subscription_id')) {
            DB::statement("UPDATE users SET lemonsqueezy_subscription_id = fungies_subscription_id WHERE lemonsqueezy_subscription_id IS NULL AND fungies_subscription_id IS NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // no-op
    }
};
