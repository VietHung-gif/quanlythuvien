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
        Schema::table('borrows', function (Blueprint $table) {
            if (!Schema::hasColumn('borrows', 'renewal_status')) {
                $table->string('renewal_status')->default('none')->after('status');
            }
            if (!Schema::hasColumn('borrows', 'renewed_times')) {
                $table->integer('renewed_times')->default(0)->after('renewal_status');
            }
            if (!Schema::hasColumn('borrows', 'renewal_requested_at')) {
                $table->timestamp('renewal_requested_at')->nullable()->after('renewed_times');
            }
        });

        Schema::table('borrow_records', function (Blueprint $table) {
            if (!Schema::hasColumn('borrow_records', 'renewal_status')) {
                $table->string('renewal_status')->default('none')->after('status');
            }
            if (!Schema::hasColumn('borrow_records', 'renewed_times')) {
                $table->integer('renewed_times')->default(0)->after('renewal_status');
            }
            if (!Schema::hasColumn('borrow_records', 'renewal_requested_at')) {
                $table->timestamp('renewal_requested_at')->nullable()->after('renewed_times');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->dropColumn(['renewal_status', 'renewed_times', 'renewal_requested_at']);
        });

        Schema::table('borrow_records', function (Blueprint $table) {
            $table->dropColumn(['renewal_status', 'renewed_times', 'renewal_requested_at']);
        });
    }
};
