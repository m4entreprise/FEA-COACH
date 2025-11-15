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
        Schema::create('client_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->integer('energy_level')->nullable(); // 1-10
            $table->integer('difficulty_level')->nullable(); // 1-10
            $table->text('progress_notes')->nullable();
            $table->text('coach_comments')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->date('assessment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_assessments');
    }
};
