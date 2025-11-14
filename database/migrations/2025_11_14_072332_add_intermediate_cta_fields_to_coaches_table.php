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
            $table->string('intermediate_cta_title')->nullable()->after('cta_text');
            $table->string('intermediate_cta_subtitle', 500)->nullable()->after('intermediate_cta_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            $table->dropColumn(['intermediate_cta_title', 'intermediate_cta_subtitle']);
        });
    }
};
