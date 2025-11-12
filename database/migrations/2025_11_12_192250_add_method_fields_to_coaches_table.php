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
            $table->string('method_title')->nullable()->after('method_text');
            $table->string('method_subtitle')->nullable()->after('method_title');
            $table->string('method_step1_title')->nullable()->after('method_subtitle');
            $table->text('method_step1_description')->nullable()->after('method_step1_title');
            $table->string('method_step2_title')->nullable()->after('method_step1_description');
            $table->text('method_step2_description')->nullable()->after('method_step2_title');
            $table->string('method_step3_title')->nullable()->after('method_step2_description');
            $table->text('method_step3_description')->nullable()->after('method_step3_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coaches', function (Blueprint $table) {
            $table->dropColumn([
                'method_title',
                'method_subtitle',
                'method_step1_title',
                'method_step1_description',
                'method_step2_title',
                'method_step2_description',
                'method_step3_title',
                'method_step3_description',
            ]);
        });
    }
};
