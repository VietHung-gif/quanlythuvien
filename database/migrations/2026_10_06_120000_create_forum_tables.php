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
        // 1. Chuyên mục diễn đàn (Hội quán Tri Thức)
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->string('color')->default('cyan'); // cyan, pink, amber, emerald, purple
            $table->timestamps();
        });

        // 2. Bài viết diễn đàn
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('forum_categories')->onDelete('cascade');
            $table->foreignId('book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->string('title');
            $table->longText('content');
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        // 3. Bình luận bài viết
        Schema::create('forum_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('forum_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });

        // 4. Lượt thích (Upvote / Thả tim) bài viết
        Schema::create('forum_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('forum_posts')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_post_likes');
        Schema::dropIfExists('forum_comments');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_categories');
    }
};
