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
        // 1. Thêm cột exp và title_badge vào bảng users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'exp')) {
                $table->unsignedInteger('exp')->default(0)->after('role');
            }
            if (!Schema::hasColumn('users', 'title_badge')) {
                $table->string('title_badge')->default('Tân Binh Giang Hồ')->after('exp');
            }
        });

        // 2. Tạo bảng câu hỏi đố vui tri thức quiz_questions
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->string('correct_answer', 5); // 'a', 'b', 'c', 'd'
            $table->unsignedInteger('exp_points')->default(15);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'title_badge')) {
                $table->dropColumn('title_badge');
            }
            if (Schema::hasColumn('users', 'exp')) {
                $table->dropColumn('exp');
            }
        });
    }
};
