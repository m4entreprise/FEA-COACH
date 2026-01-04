<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->boolean('is_featured')->default(false)->after('booking_enabled');
        });

        DB::table('service_types')
            ->where('booking_enabled', true)
            ->update(['is_featured' => true]);
    }

    public function down(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });
    }
};
