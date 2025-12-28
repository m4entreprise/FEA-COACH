<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'lemonsqueezy_customer_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('lemonsqueezy_customer_id')->nullable()->after('subscription_status');
            });
        }

        if (!Schema::hasColumn('users', 'lemonsqueezy_subscription_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('lemonsqueezy_subscription_id')->nullable()->after('lemonsqueezy_customer_id');
            });
        }

        if (!Schema::hasColumn('users', 'trial_ends_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('trial_ends_at')->nullable()->after('lemonsqueezy_subscription_id');
            });
        }

        if (!Schema::hasColumn('users', 'subscription_current_period_end')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('subscription_current_period_end')->nullable()->after('trial_ends_at');
            });
        }

        if (!Schema::hasColumn('users', 'subscription_current_period_start')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('subscription_current_period_start')->nullable()->after('subscription_current_period_end');
            });
        }

        if (!Schema::hasColumn('users', 'cancel_at_period_end')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('cancel_at_period_end')->default(false)->after('subscription_current_period_start');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'lemonsqueezy_customer_id',
                'lemonsqueezy_subscription_id',
                'subscription_current_period_end',
                'subscription_current_period_start',
                'cancel_at_period_end',
            ]);
        });
    }
};
