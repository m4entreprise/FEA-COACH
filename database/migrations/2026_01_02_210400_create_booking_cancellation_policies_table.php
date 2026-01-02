<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->unique()->constrained('coaches')->onDelete('cascade');
            $table->integer('hours_before_free_cancellation')->default(24);
            $table->integer('refund_percentage_before_deadline')->default(100);
            $table->integer('refund_percentage_after_deadline')->default(0);
            $table->integer('no_show_refund_percentage')->default(0);
            $table->text('policy_text')->nullable();
            $table->timestamps();

            $table->index('coach_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_cancellation_policies');
    }
};
