<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_document_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_document_id')->constrained()->cascadeOnDelete();
            $table->enum('action', ['uploaded', 'downloaded', 'deleted']);
            $table->enum('actor', ['coach', 'student']);
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_document_logs');
    }
};
