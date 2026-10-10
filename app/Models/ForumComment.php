<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
    ];

    /**
     * Bài viết được bình luận.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'post_id');
    }

    /**
     * Người bình luận.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
