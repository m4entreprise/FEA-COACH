<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('has_payments_module')->default(false)->after('subscription_status');
            $table->timestamp('payments_module_activated_at')->nullable()->after('has_payments_module');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['has_payments_module', 'payments_module_activated_at']);
        });
    }
};
