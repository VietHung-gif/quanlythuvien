<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'status',
        'description',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nhãn tiếng Việt cho loại giao dịch
     */
    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'deposit' => 'Nạp tiền vào ví',
            'fine_payment' => 'Nộp phạt quá hạn',
            'membership_fee', 'membership_upgrade' => 'Nâng cấp Scholar VIP',
            'book_deposit_hold' => 'Đặt cọc mượn sách',
            'book_deposit_refund' => 'Hoàn cọc mượn sách',
            'grant_donation' => 'Tài trợ / Đóng góp',
            default => 'Giao dịch khác',
        };
    }

    /**
     * Dấu hiển thị (+ hoặc -)
     */
    public function getIsPositiveAttribute(): bool
    {
        return in_array($this->type, ['deposit', 'book_deposit_refund', 'grant_donation']);
    }
}
