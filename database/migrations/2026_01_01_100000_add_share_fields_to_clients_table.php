<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('share_code', 6)->nullable()->after('vat_number');
            $table->uuid('share_token')->nullable()->after('share_code');
        });

        $clients = DB::table('clients')->select('id')->get();

        foreach ($clients as $client) {
            DB::table('clients')
                ->where('id', $client->id)
                ->update([
                    'share_code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                    'share_token' => (string) Str::uuid(),
                ]);
        }

        Schema::table('clients', function (Blueprint $table) {
            $table->string('share_code', 6)->nullable(false)->change();
            $table->uuid('share_token')->nullable(false)->change();
            $table->unique('share_code');
            $table->unique('share_token');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['share_code']);
            $table->dropUnique(['share_token']);
            $table->dropColumn(['share_code', 'share_token']);
        });
    }
};
