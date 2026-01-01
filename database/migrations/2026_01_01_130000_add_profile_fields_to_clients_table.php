<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('weight', 5, 2)->nullable()->after('email')->comment('Poids en kg');
            $table->decimal('height', 5, 2)->nullable()->after('weight')->comment('Taille en cm');
            $table->text('allergies')->nullable()->after('height')->comment('Allergies alimentaires');
            $table->text('dislikes')->nullable()->after('allergies')->comment('Aliments non appréciés');
            $table->text('general_comments')->nullable()->after('dislikes')->comment('Commentaires généraux');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['weight', 'height', 'allergies', 'dislikes', 'general_comments']);
        });
    }
};
