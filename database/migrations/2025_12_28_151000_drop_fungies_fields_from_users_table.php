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
        if (! Schema::hasTable('users')) {
            return;
        }

        $columnsToDrop = [];

        if (Schema::hasColumn('users', 'fungies_customer_id')) {
            $columnsToDrop[] = 'fungies_customer_id';
        }

        if (Schema::hasColumn('users', 'fungies_subscription_id')) {
            $columnsToDrop[] = 'fungies_subscription_id';
        }

        if ($columnsToDrop === []) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($columnsToDrop) {
            $table->dropColumn($columnsToDrop);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'fungies_customer_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('fungies_customer_id')->nullable()->after('subscription_status');
            });
        }

        if (! Schema::hasColumn('users', 'fungies_subscription_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('fungies_subscription_id')->nullable()->after('fungies_customer_id');
            });
        }
    }
};
