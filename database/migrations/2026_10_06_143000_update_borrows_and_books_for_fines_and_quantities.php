<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Thêm available_copies vào bảng books (nếu chưa có)
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'available_copies')) {
                $table->integer('available_copies')->default(0)->after('quantity');
            }
        });

        // Khởi tạo available_copies = quantity cho tất cả sách
        DB::statement('UPDATE books SET available_copies = quantity WHERE available_copies = 0 OR available_copies IS NULL');

        // 2. Thêm quantity, fine_status, fine_paid_at vào bảng borrow_records (nếu chưa có)
        Schema::table('borrow_records', function (Blueprint $table) {
            if (!Schema::hasColumn('borrow_records', 'quantity')) {
                $table->integer('quantity')->default(1)->after('book_id');
            }
            if (!Schema::hasColumn('borrow_records', 'fine_status')) {
                $table->string('fine_status')->default('none')->after('fine_amount');
            }
            if (!Schema::hasColumn('borrow_records', 'fine_paid_at')) {
                $table->timestamp('fine_paid_at')->nullable()->after('fine_status');
            }
        });

        // 3. Thêm fine_status, fine_paid_at vào bảng borrows (nếu chưa có)
        Schema::table('borrows', function (Blueprint $table) {
            if (!Schema::hasColumn('borrows', 'fine_status')) {
                $table->string('fine_status')->default('none')->after('fine_amount');
            }
            if (!Schema::hasColumn('borrows', 'fine_paid_at')) {
                $table->timestamp('fine_paid_at')->nullable()->after('fine_status');
            }
        });

        // Cập nhật fine_status = 'unpaid' cho các phiếu có fine_amount > 0
        DB::statement("UPDATE borrow_records SET fine_status = 'unpaid' WHERE fine_amount > 0 AND (fine_status IS NULL OR fine_status = 'none')");
        DB::statement("UPDATE borrows SET fine_status = 'unpaid' WHERE fine_amount > 0 AND (fine_status IS NULL OR fine_status = 'none')");
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'available_copies')) {
                $table->dropColumn('available_copies');
            }
        });

        Schema::table('borrow_records', function (Blueprint $table) {
            if (Schema::hasColumn('borrow_records', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('borrow_records', 'fine_status')) {
                $table->dropColumn('fine_status');
            }
            if (Schema::hasColumn('borrow_records', 'fine_paid_at')) {
                $table->dropColumn('fine_paid_at');
            }
        });

        Schema::table('borrows', function (Blueprint $table) {
            if (Schema::hasColumn('borrows', 'fine_status')) {
                $table->dropColumn('fine_status');
            }
            if (Schema::hasColumn('borrows', 'fine_paid_at')) {
                $table->dropColumn('fine_paid_at');
            }
        });
    }
};
