<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->unique()->constrained('coaches')->onDelete('cascade');
            $table->string('stripe_account_id')->unique();
            $table->boolean('onboarding_completed')->default(false);
            $table->boolean('charges_enabled')->default(false);
            $table->boolean('payouts_enabled')->default(false);
            $table->boolean('details_submitted')->default(false);
            $table->string('country', 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->string('business_type', 50)->nullable();
            $table->timestamps();

            $table->index('coach_id');
            $table->index('stripe_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stripe_accounts');
    }
};
