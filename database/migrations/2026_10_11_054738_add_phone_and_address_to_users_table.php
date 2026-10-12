<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
        });

        if (Schema::hasColumn('users', 'phone_number') && Schema::hasColumn('users', 'phone')) {
            DB::statement('UPDATE users SET phone = phone_number WHERE phone IS NULL AND phone_number IS NOT NULL');
        }
        if (Schema::hasColumn('users', 'delivery_address') && Schema::hasColumn('users', 'address')) {
            DB::statement('UPDATE users SET address = delivery_address WHERE address IS NULL AND delivery_address IS NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
