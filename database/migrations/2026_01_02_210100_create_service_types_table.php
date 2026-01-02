<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('coaches')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration_minutes');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->boolean('is_active')->default(true);
            $table->boolean('booking_enabled')->default(true);
            $table->integer('max_advance_booking_days')->default(60);
            $table->integer('min_advance_booking_hours')->default(24);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('coach_id');
            $table->index(['coach_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
