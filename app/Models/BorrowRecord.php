<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $borrower_name
 * @property string|null $borrower_phone
 * @property string|null $borrower_email
 * @property int $book_id
 * @property int $quantity
 * @property \Carbon\Carbon|null $borrow_date
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $return_date
 * @property string $status
 * @property string $renewal_status
 * @property int $renewed_times
 * @property \Carbon\Carbon|null $renewal_requested_at
 * @property string $delivery_method
 * @property string|null $shipping_address
 * @property float $deposit_fee
 * @property float $deposit_amount
 * @property float $fine_amount
 * @property bool $is_fine_paid
 * @property string $fine_status
 * @property \Carbon\Carbon|null $fine_paid_at
 * @property string|null $notes
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Book|null $book
 */
class BorrowRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'borrower_name',
        'borrower_phone',
        'borrower_email',
        'book_id',
        'quantity',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'renewal_status',
        'renewed_times',
        'renewal_requested_at',
        'delivery_method',
        'shipping_address',
        'deposit_fee',
        'deposit_amount',
        'fine_amount',
        'is_fine_paid',
        'fine_status',
        'fine_paid_at',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'renewal_requested_at' => 'datetime',
        'renewed_times' => 'integer',
        'deposit_amount' => 'decimal:2',
        'deposit_fee' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'is_fine_paid' => 'boolean',
        'fine_paid_at' => 'datetime',
        'quantity' => 'integer',
    ];

    /**
     * Kiểm tra phiếu mượn thuộc độc giả Scholar VIP
     */
    public function isVipBorrow(): bool
    {
        return ($this->user && $this->user->isVip()) || ($this->deposit_fee == 0 && in_array($this->delivery_method, ['counter_priority', 'home_delivery']));
    }

    /**
     * Nhãn hiển thị phương thức nhận sách
     */
    public function getDeliveryMethodLabelAttribute(): string
    {
        return match ($this->delivery_method) {
            'home_delivery' => 'Giao sách tận nhà / KTX',
            'counter_priority' => 'Làn ưu tiên tại quầy',
            default => 'Quầy tiếp nhận thông thường',
        };
    }

    /**
     * Bản ghi mượn thuộc về một cuốn sách.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Bản ghi mượn có thể liên kết với một tài khoản người dùng trong hệ thống.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra xem phiếu mượn hiện tại có đang bị quá hạn hay không.
     */
    public function isOverdue(): bool
    {
        if (in_array($this->status, ['returned', 'pending', 'rejected'])) {
            return false;
        }

        return Carbon::now()->startOfDay()->greaterThan(Carbon::parse($this->due_date)->startOfDay());
    }

    /**
     * Tính số ngày quá hạn (nếu chưa trả thì so với hiện tại, nếu đã trả thì so với ngày trả).
     */
    public function getOverdueDays(): int
    {
        $dueDate = Carbon::parse($this->due_date)->startOfDay();
        $targetDate = $this->return_date
            ? Carbon::parse($this->return_date)->startOfDay()
            : Carbon::now()->startOfDay();

        if ($targetDate->greaterThan($dueDate)) {
            return $dueDate->diffInDays($targetDate);
        }

        return 0;
    }

    /**
     * Tính số tiền phạt dự kiến theo số ngày quá hạn (mặc định 5.000 VNĐ / ngày).
     */
    public function calculateEstimatedFine(int $finePerDay = 5000): int
    {
        if ($this->status === 'returned') {
            return $this->fine_amount;
        }

        return $this->getOverdueDays() * $finePerDay;
    }

    /**
     * Định dạng tiền phạt sang dạng VNĐ.
     */
    public function getFormattedFineAttribute(): string
    {
        return number_format($this->fine_amount, 0, ',', '.') . ' đ';
    }
}
