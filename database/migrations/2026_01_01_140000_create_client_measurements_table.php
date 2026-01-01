<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            
            // Métriques physiques historisées
            $table->decimal('weight', 5, 2)->nullable()->comment('Poids en kg');
            $table->decimal('height', 5, 2)->nullable()->comment('Taille en cm');
            
            // Mensurations en cm
            $table->decimal('chest', 5, 2)->nullable()->comment('Tour de poitrine en cm');
            $table->decimal('waist', 5, 2)->nullable()->comment('Tour de taille en cm');
            $table->decimal('hips', 5, 2)->nullable()->comment('Tour de hanches en cm');
            $table->decimal('arm', 5, 2)->nullable()->comment('Tour de bras en cm');
            $table->decimal('thigh', 5, 2)->nullable()->comment('Tour de cuisse en cm');
            
            // Photos d'évolution (stockage sécurisé)
            $table->string('photo_front')->nullable()->comment('Photo de face');
            $table->string('photo_side')->nullable()->comment('Photo de profil');
            $table->string('photo_back')->nullable()->comment('Photo de dos');
            
            $table->text('notes')->nullable()->comment('Notes optionnelles');
            $table->timestamps();
            
            $table->index(['client_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_measurements');
    }
};
