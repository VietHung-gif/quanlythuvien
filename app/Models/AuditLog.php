<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Người thực hiện hành động (Thủ thư, Admin, hoặc User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nhãn hiển thị tiếng Việt của hành động.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'APPROVE_BORROW' => 'DUYỆT MƯỢN SÁCH',
            'REJECT_BORROW' => 'TỪ CHỐI MƯỢN',
            'RETURN_BOOK' => 'TRẢ SÁCH',
            'COLLECT_FINE' => 'THU TIỀN PHẠT',
            'UPDATE_STOCK' => 'CẬP NHẬT KHO SÁCH',
            'DELETE_BOOK' => 'XÓA ĐẦU SÁCH',
            'DELETE_CATEGORY' => 'XÓA THỂ LOẠI',
            'UPDATE_USER_ROLE' => 'PHÂN QUYỀN USER',
            default => $this->action,
        };
    }

    /**
     * Màu sắc badge Tailwind theo chuẩn Cyberpunk.
     */
    public function getActionColorClassAttribute(): string
    {
        return match ($this->action) {
            'COLLECT_FINE' => 'bg-amber-950/80 text-amber-300 border-amber-400/60 shadow-[0_0_12px_rgba(251,191,36,0.35)]',
            'APPROVE_BORROW' => 'bg-emerald-950/80 text-emerald-300 border-emerald-400/60 shadow-[0_0_12px_rgba(52,211,153,0.35)]',
            'REJECT_BORROW' => 'bg-rose-950/80 text-rose-300 border-rose-400/60 shadow-[0_0_12px_rgba(244,63,94,0.35)]',
            'RETURN_BOOK' => 'bg-cyan-950/80 text-cyan-300 border-cyan-400/60 shadow-[0_0_12px_rgba(6,182,212,0.35)]',
            'UPDATE_STOCK' => 'bg-purple-950/80 text-purple-300 border-purple-400/60 shadow-[0_0_12px_rgba(168,85,247,0.35)]',
            'DELETE_BOOK', 'DELETE_CATEGORY' => 'bg-red-950/90 text-red-300 border-red-500/80 shadow-[0_0_12px_rgba(239,68,68,0.45)]',
            'UPDATE_USER_ROLE' => 'bg-yellow-950/80 text-yellow-300 border-yellow-400/60 shadow-[0_0_12px_rgba(234,179,8,0.35)]',
            default => 'bg-slate-800 text-slate-300 border-slate-700',
        };
    }

    /**
     * Lấy icon SVG theo hành động (chuẩn Lucide/Heroicons thanh lịch).
     */
    public function getActionIconSvgAttribute(): string
    {
        return match ($this->action) {
            'COLLECT_FINE' => '<svg class="w-3.5 h-3.5 inline text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'APPROVE_BORROW' => '<svg class="w-3.5 h-3.5 inline text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'REJECT_BORROW' => '<svg class="w-3.5 h-3.5 inline text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'RETURN_BOOK' => '<svg class="w-3.5 h-3.5 inline text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
            'UPDATE_STOCK' => '<svg class="w-3.5 h-3.5 inline text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
            'DELETE_BOOK' => '<svg class="w-3.5 h-3.5 inline text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>',
            'DELETE_CATEGORY' => '<svg class="w-3.5 h-3.5 inline text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>',
            'UPDATE_USER_ROLE' => '<svg class="w-3.5 h-3.5 inline text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
            default => '<svg class="w-3.5 h-3.5 inline text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
        };
    }

    /**
     * Biểu tượng icon theo hành động.
     */
    public function getActionIconAttribute(): string
    {
        return $this->action_icon_svg;
    }
}
