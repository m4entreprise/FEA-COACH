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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('entity_type', ['PP', 'SOC'])->default('PP')->after('legal_address');
            $table->string('legal_name')->nullable()->after('entity_type');
            $table->string('company_number')->nullable()->after('legal_name');
            $table->string('legal_representative')->nullable()->after('company_number');
            $table->string('phone_contact')->nullable()->after('legal_representative');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'entity_type',
                'legal_name',
                'company_number',
                'legal_representative',
                'phone_contact',
            ]);
        });
    }
};
