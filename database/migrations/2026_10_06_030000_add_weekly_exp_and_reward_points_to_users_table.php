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
            if (! Schema::hasColumn('users', 'reward_points')) {
                $table->unsignedInteger('reward_points')->default(0)->after('exp');
            }
            if (! Schema::hasColumn('users', 'weekly_exp')) {
                $table->unsignedInteger('weekly_exp')->default(0)->after('reward_points');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'reward_points')) {
                $columnsToDrop[] = 'reward_points';
            }
            if (Schema::hasColumn('users', 'weekly_exp')) {
                $columnsToDrop[] = 'weekly_exp';
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
