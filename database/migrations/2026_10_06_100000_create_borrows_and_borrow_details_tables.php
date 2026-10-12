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
        // 1. Bổ sung thông tin Nhà xuất bản & Năm xuất bản cho bảng books (nếu chưa có)
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'publisher')) {
                $table->string('publisher')->nullable()->default('NXB Trẻ')->after('author');
            }
            if (! Schema::hasColumn('books', 'publish_year')) {
                $table->integer('publish_year')->nullable()->default(2024)->after('publisher');
            }
        });

        // 2. Tạo bảng borrows (Phiếu đăng ký mượn sách)
        if (! Schema::hasTable('borrows')) {
            Schema::create('borrows', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // Mã phiếu: PDK00001, PDK00002...
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('borrower_name');
                $table->string('borrower_phone')->nullable();
                $table->string('borrower_email')->nullable();

                // Mốc thời gian
                $table->date('borrow_date'); // Ngày lập phiếu
                $table->date('approved_date')->nullable(); // Ngày duyệt
                $table->date('due_date')->nullable(); // Ngày hẹn trả / hạn phiếu
                $table->date('return_date')->nullable(); // Ngày thực tế trả sách

                // Trạng thái phiếu
                $table->string('status')->default('pending'); // pending, approved, borrowing, returned, rejected, overdue
                $table->string('borrow_status')->default('Chưa mượn'); // Đã mượn / Chưa mượn / Đã trả

                // Số lượng
                $table->integer('total_quantity')->default(1);
                $table->integer('approved_quantity')->nullable();

                // Phạt & Ghi chú
                $table->decimal('fine_amount', 10, 2)->default(0);
                $table->text('notes')->nullable();

                $table->timestamps();
            });
        }

        // 3. Tạo bảng borrow_details (Chi tiết các đầu sách trong mỗi phiếu mượn)
        if (! Schema::hasTable('borrow_details')) {
            Schema::create('borrow_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('borrow_id')->constrained('borrows')->cascadeOnDelete();
                $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->integer('returned_quantity')->default(0);
                $table->string('status')->default('pending'); // pending, approved, borrowing, returned, rejected
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_details');
        Schema::dropIfExists('borrows');

        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'publish_year')) {
                $table->dropColumn('publish_year');
            }
            if (Schema::hasColumn('books', 'publisher')) {
                $table->dropColumn('publisher');
            }
        });
    }
};
