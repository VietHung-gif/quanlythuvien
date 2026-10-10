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
        Schema::create('borrow_records', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại liên kết bảng users (tùy chọn nếu người mượn có tài khoản)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('borrower_name'); // Tên người mượn / độc giả
            $table->string('borrower_phone')->nullable(); // Số điện thoại
            $table->string('borrower_email')->nullable(); // Email liên hệ

            // Khóa ngoại liên kết bảng books
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();

            // Mốc thời gian mượn - trả
            $table->date('borrow_date'); // Ngày mượn sách
            $table->date('due_date'); // Hạn trả sách dự kiến
            $table->date('return_date')->nullable(); // Ngày thực tế trả sách

            // Trạng thái: enum('borrowing', 'returned', 'overdue')
            $table->enum('status', ['borrowing', 'returned', 'overdue'])->default('borrowing');

            // Tiền phạt quá hạn (decimal, mặc định 0)
            $table->decimal('fine_amount', 10, 2)->default(0);

            // Ghi chú (tình trạng sách lúc mượn hoặc khi trả)
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrow_records');
    }
};
