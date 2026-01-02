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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('service_type')->nullable()->after('phone');
        });

        Schema::table('coaches', function (Blueprint $table) {
            $table->string('desired_custom_domain')->nullable()->after('subdomain');
            $table->timestamp('custom_contact_locked_until')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });

        Schema::table('coaches', function (Blueprint $table) {
            $table->dropColumn('desired_custom_domain');
            $table->dropColumn('custom_contact_locked_until');
        });
    }
};
