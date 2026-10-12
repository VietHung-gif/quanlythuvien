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
        // 1. CẬP NHẬT BẢNG USERS
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'membership_type')) {
                $table->enum('membership_type', ['standard', 'vip'])->default('standard')->after('role');
            }
            if (! Schema::hasColumn('users', 'vip_expires_at')) {
                $table->dateTime('vip_expires_at')->nullable()->after('membership_type');
            }
            if (! Schema::hasColumn('users', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('vip_expires_at');
            }
            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 50)->nullable()->after('delivery_address');
            }
        });

        // Đồng bộ dữ liệu hiện có từ membership_tier / membership_expires_at
        DB::statement("UPDATE users SET membership_type = 'vip', vip_expires_at = membership_expires_at WHERE membership_tier = 'vip'");

        // 2. CẬP NHẬT BẢNG BORROWS
        Schema::table('borrows', function (Blueprint $table) {
            if (! Schema::hasColumn('borrows', 'delivery_method')) {
                $table->enum('delivery_method', ['counter_priority', 'home_delivery', 'counter_normal'])
                    ->default('counter_normal')
                    ->after('borrow_status');
            }
            if (! Schema::hasColumn('borrows', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('delivery_method');
            }
            if (! Schema::hasColumn('borrows', 'deposit_fee')) {
                $table->decimal('deposit_fee', 10, 2)->default(0)->after('shipping_address');
            }
        });

        // 3. CẬP NHẬT BẢNG BORROW_RECORDS (Legacy single-book records)
        Schema::table('borrow_records', function (Blueprint $table) {
            if (! Schema::hasColumn('borrow_records', 'delivery_method')) {
                $table->enum('delivery_method', ['counter_priority', 'home_delivery', 'counter_normal'])
                    ->default('counter_normal')
                    ->after('status');
            }
            if (! Schema::hasColumn('borrow_records', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('delivery_method');
            }
            if (! Schema::hasColumn('borrow_records', 'deposit_fee')) {
                $table->decimal('deposit_fee', 10, 2)->default(0)->after('shipping_address');
            }
        });

        // Đồng bộ deposit_fee từ deposit_amount nếu có
        DB::statement('UPDATE borrows SET deposit_fee = deposit_amount WHERE deposit_amount IS NOT NULL');
        DB::statement('UPDATE borrow_records SET deposit_fee = deposit_amount WHERE deposit_amount IS NOT NULL');

        // 4. MỞ RỘNG ENUM BẢNG TRANSACTIONS ĐỂ HỖ TRỢ 'membership_upgrade'
        try {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('deposit','fine_payment','membership_fee','membership_upgrade','book_deposit_hold','book_deposit_refund','grant_donation') NOT NULL");
        } catch (Throwable $e) {
            // Trường hợp driver không phải MySQL hoặc đã là varchar
        }

        // 5. ĐIỀU CHỈNH DOANH THU CÁC GÓI VÀ QUỸ VỀ MỨC THỰC TẾ HỌC ĐƯỜNG (HÀNG TRIỆU THAY VÌ HÀNG TRĂM TRIỆU)
        // ID 1: 150tr -> 15tr
        // ID 2: 80tr -> 8tr
        // ID 3: 45tr -> 4.5tr
        // ID 4: 25tr -> 2.5tr
        DB::statement('UPDATE library_funds SET amount = 15000000.00 WHERE id = 1 AND amount >= 100000000.00');
        DB::statement('UPDATE library_funds SET amount = 8000000.00 WHERE id = 2 AND amount >= 50000000.00');
        DB::statement('UPDATE library_funds SET amount = 4500000.00 WHERE id = 3 AND amount >= 30000000.00');
        DB::statement('UPDATE library_funds SET amount = 2500000.00 WHERE id = 4 AND amount >= 20000000.00');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'membership_type')) {
                $table->dropColumn('membership_type');
            }
            if (Schema::hasColumn('users', 'vip_expires_at')) {
                $table->dropColumn('vip_expires_at');
            }
            if (Schema::hasColumn('users', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }
            if (Schema::hasColumn('users', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
        });

        Schema::table('borrows', function (Blueprint $table) {
            if (Schema::hasColumn('borrows', 'delivery_method')) {
                $table->dropColumn('delivery_method');
            }
            if (Schema::hasColumn('borrows', 'shipping_address')) {
                $table->dropColumn('shipping_address');
            }
            if (Schema::hasColumn('borrows', 'deposit_fee')) {
                $table->dropColumn('deposit_fee');
            }
        });

        Schema::table('borrow_records', function (Blueprint $table) {
            if (Schema::hasColumn('borrow_records', 'delivery_method')) {
                $table->dropColumn('delivery_method');
            }
            if (Schema::hasColumn('borrow_records', 'shipping_address')) {
                $table->dropColumn('shipping_address');
            }
            if (Schema::hasColumn('borrow_records', 'deposit_fee')) {
                $table->dropColumn('deposit_fee');
            }
        });
    }
};
