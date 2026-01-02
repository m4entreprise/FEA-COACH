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
        Schema::table('coaches', function (Blueprint $table) {
            // Typologie des services
            $table->boolean('is_coaching_presentiel')->default(false)->after('legal_terms');
            $table->boolean('is_coaching_online')->default(false)->after('is_coaching_presentiel');
            $table->boolean('has_digital_products')->default(false)->after('is_coaching_online');
            $table->boolean('has_subscriptions')->default(false)->after('has_digital_products');
            $table->boolean('use_client_photos')->default(false)->after('has_subscriptions');
            
            // Règles métier
            $table->enum('vat_regime', ['ASSUJETTI', 'FRANCHISE'])->default('ASSUJETTI')->after('use_client_photos');
            $table->integer('cancellation_delay')->default(24)->after('vat_regime');
            $table->string('tribunal_city')->default('Bruxelles')->after('cancellation_delay');
            $table->string('insurance_company')->nullable()->after('tribunal_city');
            $table->string('insurance_policy_number')->nullable()->after('insurance_company');
            
            // Mode de génération
            $table->enum('legal_generation_mode', ['AUTO', 'MANUAL'])->default('AUTO')->after('insurance_policy_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            $table->dropColumn([
                'is_coaching_presentiel',
                'is_coaching_online',
                'has_digital_products',
                'has_subscriptions',
                'use_client_photos',
                'vat_regime',
                'cancellation_delay',
                'tribunal_city',
                'insurance_company',
                'insurance_policy_number',
                'legal_generation_mode',
            ]);
        });
    }
};
