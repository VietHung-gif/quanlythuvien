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

    /**
     * Lấy icon SVG chuẩn Heroicons học thuật sang trọng cho chuyên mục.
     */
    public function getIconSvgAttribute(): string
    {
        return match ($this->slug) {
            'review-sach' => '<svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>',
            'hoi-dap-tim-tai-lieu' => '<svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'luan-ban-tri-thuc' => '<svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
            'thong-bao-thu-vien' => '<svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>',
            default => '<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>',
        };
    }
}
