<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'color',
    ];

    /**
     * Danh sách bài viết thuộc chuyên mục này.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class, 'category_id');
    }

    /**
     * Lấy class style màu Neon Cyberpunk cho badge chuyên mục.
     */
    public function getColorBadgeClassAttribute(): string
    {
        return match ($this->color) {
            'pink' => 'bg-pink-950/70 text-[#ff007f] border border-[#ff007f]/40 shadow-[0_0_8px_rgba(255,0,127,0.3)]',
            'amber' => 'bg-amber-950/70 text-[#ffb800] border border-[#ffb800]/40 shadow-[0_0_8px_rgba(255,184,0,0.3)]',
            'emerald' => 'bg-emerald-950/70 text-[#00ff66] border border-[#00ff66]/40 shadow-[0_0_8px_rgba(0,255,102,0.3)]',
            'purple' => 'bg-purple-950/70 text-[#b026ff] border border-[#b026ff]/40 shadow-[0_0_8px_rgba(176,38,255,0.3)]',
            default => 'bg-cyan-950/70 text-[#00f0ff] border border-[#00f0ff]/40 shadow-[0_0_8px_rgba(0,240,255,0.3)]',
        };
    }
}
