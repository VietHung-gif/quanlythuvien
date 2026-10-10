<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property int|null $user_id
 * @property string $borrower_name
 * @property string|null $borrower_phone
 * @property string|null $borrower_email
 * @property \Carbon\Carbon|null $borrow_date
 * @property \Carbon\Carbon|null $approved_date
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $return_date
 * @property string $status
 * @property string $borrow_status
 * @property string $delivery_method
 * @property string|null $shipping_address
 * @property float $deposit_fee
 * @property float $deposit_amount
 * @property float $fine_amount
 * @property string $fine_status
 * @property \Carbon\Carbon|null $fine_paid_at
 * @property string|null $notes
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BorrowDetail> $details
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
 */
class Borrow extends Model
{
    use HasFactory;

    protected $table = 'borrows';

    protected $fillable = [
        'code',
        'user_id',
        'borrower_name',
        'borrower_phone',
        'borrower_email',
        'borrow_date',
        'approved_date',
        'due_date',
        'return_date',
        'status',
        'renewal_status',
        'renewed_times',
        'renewal_requested_at',
        'borrow_status',
        'delivery_method',
        'shipping_address',
        'deposit_fee',
        'total_quantity',
        'approved_quantity',
        'deposit_amount',
        'fine_amount',
        'is_fine_paid',
        'fine_status',
        'fine_paid_at',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'approved_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'renewal_requested_at' => 'datetime',
        'renewed_times' => 'integer',
        'deposit_amount' => 'decimal:2',
        'deposit_fee' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'is_fine_paid' => 'boolean',
        'fine_paid_at' => 'datetime',
        'total_quantity' => 'integer',
        'approved_quantity' => 'integer',
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
     * Tự động sinh mã phiếu mượn duy nhất (vd: PDK00001, PDK00002...).
     */
    public static function generateCode(): string
    {
        $lastBorrow = self::orderByDesc('id')->first();
        $nextId = $lastBorrow ? ($lastBorrow->id + 1) : 1;
        return 'PDK' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Phiếu mượn thuộc về một người dùng.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Chi tiết các đầu sách trong phiếu.
     */
    public function details(): HasMany
    {
        return $this->hasMany(BorrowDetail::class, 'borrow_id');
    }

    /**
     * Danh sách các cuốn sách trong phiếu.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'borrow_details', 'borrow_id', 'book_id')
            ->withPivot(['quantity', 'returned_quantity', 'status'])
            ->withTimestamps();
    }

    /**
     * Kiểm tra xem phiếu có bị quá hạn hay không.
     */
    public function isOverdue(): bool
    {
        if (in_array($this->status, ['returned', 'rejected', 'pending']) || empty($this->due_date)) {
            return false;
        }

        return Carbon::now()->startOfDay()->greaterThan(Carbon::parse($this->due_date)->startOfDay());
    }

    /**
     * Tính số ngày quá hạn.
     */
    public function getOverdueDays(): int
    {
        if (empty($this->due_date)) {
            return 0;
        }

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
     * Tính tiền phạt tạm tính theo 5.000đ/ngày.
     */
    public function calculateEstimatedFine(int $finePerDay = 5000): int
    {
        if ($this->status === 'returned') {
            return (int) $this->fine_amount;
        }

        return $this->getOverdueDays() * $finePerDay;
    }

    /**
     * Định dạng tiền phạt VNĐ.
     */
    public function getFormattedFineAttribute(): string
    {
        return number_format($this->fine_amount, 0, ',', '.') . ' đ';
    }
}
