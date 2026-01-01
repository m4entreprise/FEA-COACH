<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('order')->default(0)->after('price');
        });

        $plans = DB::table('plans')
            ->select('id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        foreach ($plans as $index => $plan) {
            DB::table('plans')
                ->where('id', $plan->id)
                ->update(['order' => $index]);
        }
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
