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
        Schema::create('client_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('weight', 5, 2)->nullable();
            $table->json('body_measurements')->nullable(); // tour de taille, hanches, bras, cuisses, etc.
            $table->decimal('body_fat_percentage', 4, 2)->nullable(); // IMG (Indice de Masse Grasse)
            $table->json('photos')->nullable(); // URLs des photos avant/après
            $table->date('measurement_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_measurements');
    }
};
