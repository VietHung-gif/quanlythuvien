<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mở rộng enum cột status để hỗ trợ 'pending' (chờ duyệt) và 'rejected' (từ chối)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE borrow_records MODIFY COLUMN status ENUM('pending', 'borrowing', 'returned', 'overdue', 'rejected') NOT NULL DEFAULT 'borrowing'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE borrow_records MODIFY COLUMN status ENUM('borrowing', 'returned', 'overdue') NOT NULL DEFAULT 'borrowing'");
        }
    }
};
