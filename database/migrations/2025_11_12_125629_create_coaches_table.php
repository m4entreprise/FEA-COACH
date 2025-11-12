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
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subdomain')->unique()->nullable();
            $table->string('color_primary')->default('#3b82f6');
            $table->string('color_secondary')->default('#8b5cf6');
            $table->text('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->longText('about_text')->nullable();
            $table->longText('method_text')->nullable();
            $table->string('cta_text')->default('Commencer maintenant');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
