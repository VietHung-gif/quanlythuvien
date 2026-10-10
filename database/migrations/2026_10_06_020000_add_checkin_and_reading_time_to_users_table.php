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
        // 1. Xóa bảng câu hỏi quiz nếu có
        Schema::dropIfExists('quiz_questions');

        // 2. Thêm cột last_checkin_at và reading_seconds vào bảng users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_checkin_at')) {
                $table->timestamp('last_checkin_at')->nullable()->after('title_badge');
            }
            if (!Schema::hasColumn('users', 'reading_seconds')) {
                $table->unsignedInteger('reading_seconds')->default(0)->after('last_checkin_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'reading_seconds')) {
                $table->dropColumn('reading_seconds');
            }
            if (Schema::hasColumn('users', 'last_checkin_at')) {
                $table->dropColumn('last_checkin_at');
            }
        });
    }
};
