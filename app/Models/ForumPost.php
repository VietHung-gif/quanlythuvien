<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'book_id',
        'title',
        'content',
        'views_count',
        'likes_count',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'likes_count' => 'integer',
            'views_count' => 'integer',
        ];
    }

    /**
     * Tác giả bài viết.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Chuyên mục của bài viết.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    /**
     * Sách được gắn thẻ (Book Tagging).
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Danh sách bình luận.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class, 'post_id')->latest();
    }

    /**
     * Danh sách lượt thích.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(ForumPostLike::class, 'post_id');
    }

    /**
     * Kiểm tra một người dùng đã like bài viết chưa.
     */
    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Tối ưu nếu quan hệ đã eager loaded
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $user->id);
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Trích dẫn ngắn nội dung bài viết.
     */
    public function getSnippetAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 180, '...');
    }
}
