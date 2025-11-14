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
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('vat_number')->nullable()->after('last_name');
            $table->text('legal_address')->nullable()->after('vat_number');
            $table->boolean('is_fea_graduate')->default(false)->after('legal_address');
            $table->string('fea_promo_code')->nullable()->after('is_fea_graduate');
            $table->boolean('onboarding_completed')->default(false)->after('fea_promo_code');
            $table->string('stripe_customer_id')->nullable()->after('onboarding_completed');
            $table->string('subscription_status')->nullable()->after('stripe_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'vat_number',
                'legal_address',
                'is_fea_graduate',
                'fea_promo_code',
                'onboarding_completed',
                'stripe_customer_id',
                'subscription_status',
            ]);
        });
    }
};
