<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('type', 32);
            $table->unsignedInteger('version')->default(1);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('filesize')->default(0);
            $table->string('storage_path');
            $table->uuid('file_uuid');
            $table->timestamp('available_at')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'type', 'version']);
            $table->index(['client_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_documents');
    }
};
