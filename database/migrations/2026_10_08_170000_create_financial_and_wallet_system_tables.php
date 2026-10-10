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
        // 1. Bảng wallets (Ví số dư độc giả)
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->unique();
                $table->decimal('balance', 12, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 2. Bảng transactions (Nhật ký giao dịch tài chính nội bộ)
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->enum('type', [
                    'deposit',
                    'fine_payment',
                    'membership_fee',
                    'book_deposit_hold',
                    'book_deposit_refund',
                    'grant_donation'
                ]);
                $table->decimal('amount', 12, 2);
                $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
                $table->text('description')->nullable();
                $table->string('reference_id')->nullable();
                $table->timestamps();
            });
        }

        // 3. Bảng library_funds (Quỹ ngân sách cơ sở hạ tầng & tài trợ)
        if (!Schema::hasTable('library_funds')) {
            Schema::create('library_funds', function (Blueprint $table) {
                $table->id();
                $table->string('source_name');
                $table->decimal('amount', 15, 2);
                $table->enum('allocated_for', [
                    'server_cloud',
                    'book_procurement',
                    'staff_salary',
                    'facility_maintenance'
                ]);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 4. Cập nhật bảng users (Gói thành viên thư viện)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'membership_tier')) {
                $table->enum('membership_tier', ['standard', 'vip'])->default('standard')->after('role');
            }
            if (!Schema::hasColumn('users', 'membership_expires_at')) {
                $table->timestamp('membership_expires_at')->nullable()->after('membership_tier');
            }
        });

        // 5. Cập nhật bảng borrows
        Schema::table('borrows', function (Blueprint $table) {
            if (!Schema::hasColumn('borrows', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->default(0.00)->after('status');
            }
            if (!Schema::hasColumn('borrows', 'is_fine_paid')) {
                $table->boolean('is_fine_paid')->default(false)->after('fine_amount');
            }
        });

        // 6. Cập nhật bảng borrow_records (chi tiết mượn)
        Schema::table('borrow_records', function (Blueprint $table) {
            if (!Schema::hasColumn('borrow_records', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->default(0.00)->after('status');
            }
            if (!Schema::hasColumn('borrow_records', 'is_fine_paid')) {
                $table->boolean('is_fine_paid')->default(false)->after('fine_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrow_records', function (Blueprint $table) {
            if (Schema::hasColumn('borrow_records', 'deposit_amount')) {
                $table->dropColumn('deposit_amount');
            }
            if (Schema::hasColumn('borrow_records', 'is_fine_paid')) {
                $table->dropColumn('is_fine_paid');
            }
        });

        Schema::table('borrows', function (Blueprint $table) {
            if (Schema::hasColumn('borrows', 'deposit_amount')) {
                $table->dropColumn('deposit_amount');
            }
            if (Schema::hasColumn('borrows', 'is_fine_paid')) {
                $table->dropColumn('is_fine_paid');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'membership_expires_at')) {
                $table->dropColumn('membership_expires_at');
            }
            if (Schema::hasColumn('users', 'membership_tier')) {
                $table->dropColumn('membership_tier');
            }
        });

        Schema::dropIfExists('library_funds');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
    }
};
