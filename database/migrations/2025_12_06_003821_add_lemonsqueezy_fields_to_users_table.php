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
        Schema::table('users', function (Blueprint $table) {
            $table->string('fungies_customer_id')->nullable()->after('subscription_status');
            $table->string('fungies_subscription_id')->nullable()->after('fungies_customer_id');
            $table->timestamp('trial_ends_at')->nullable()->after('fungies_subscription_id');
            $table->timestamp('subscription_current_period_end')->nullable()->after('trial_ends_at');
            $table->timestamp('subscription_current_period_start')->nullable()->after('subscription_current_period_end');
            $table->boolean('cancel_at_period_end')->default(false)->after('subscription_current_period_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'fungies_customer_id',
                'fungies_subscription_id',
                'trial_ends_at',
                'subscription_current_period_end',
                'subscription_current_period_start',
                'cancel_at_period_end',
            ]);
        });
    }
};
