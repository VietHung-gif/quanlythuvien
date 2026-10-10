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
     * Biểu tượng icon theo hành động.
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'COLLECT_FINE' => '💵',
            'APPROVE_BORROW' => '✅',
            'REJECT_BORROW' => '❌',
            'RETURN_BOOK' => '🔄',
            'UPDATE_STOCK' => '📦',
            'DELETE_BOOK' => '🗑️',
            'DELETE_CATEGORY' => '📁',
            'UPDATE_USER_ROLE' => '👑',
            default => '📝',
        };
    }
}
