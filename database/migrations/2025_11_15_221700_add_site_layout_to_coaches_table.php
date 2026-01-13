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
        if (! Schema::hasColumn('coaches', 'site_layout')) {
            Schema::table('coaches', function (Blueprint $table) {
                $table->string('site_layout')
                    ->default('classic')
                    ->after('subdomain');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('coaches', 'site_layout')) {
            Schema::table('coaches', function (Blueprint $table) {
                $table->dropColumn('site_layout');
            });
        }
    }
};
