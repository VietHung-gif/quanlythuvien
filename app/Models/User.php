<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property string $password
 * @property string $role
 * @property int $exp
 * @property string|null $title_badge
 * @property Carbon|null $last_checkin_at
 * @property int $reading_seconds
 * @property int $reward_points
 * @property int $weekly_exp
 * @property string|null $membership_tier
 * @property Carbon|null $membership_expires_at
 * @property string|null $membership_type
 * @property Carbon|null $vip_expires_at
 * @property string|null $delivery_address
 * @property string|null $phone_number
 * @property string|null $phone
 * @property string|null $address
 * @property-read float $unpaid_fine
 * @property-read string $role_name
 * @property-read array $current_tier
 * @property-read string $badge_color
 * @property-read string $badge_icon
 * @property-read string $avatar_glow_frame
 * @property-read string $title_badge_class
 * @property-read string $title_formatted
 * @property-read Wallet|null $wallet
 * @property-read Collection<int, Transaction> $transactions
 * @property-read Collection<int, BorrowRecord> $borrowRecords
 * @property-read Collection<int, Borrow> $borrows
 */
#[Fillable(['name', 'email', 'avatar', 'password', 'role', 'exp', 'title_badge', 'last_checkin_at', 'reading_seconds', 'reward_points', 'weekly_exp', 'membership_tier', 'membership_expires_at', 'membership_type', 'vip_expires_at', 'delivery_address', 'phone_number', 'phone', 'address'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'exp' => 'integer',
            'reward_points' => 'integer',
            'weekly_exp' => 'integer',
            'last_checkin_at' => 'datetime',
            'reading_seconds' => 'integer',
            'membership_expires_at' => 'datetime',
            'vip_expires_at' => 'datetime',
        ];
    }

    /**
     * Ví số dư điện tử của người dùng
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Nhật ký giao dịch tài chính
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->latest();
    }

    /**
     * Lấy ví của user, tự động tạo nếu chưa có
     */
    public function getOrCreateWallet(): Wallet
    {
        return $this->wallet()->firstOrCreate([], ['balance' => 0.00]);
    }

    /**
     * Kiểm tra xem độc giả có phải là Scholar VIP còn hạn không
     */
    public function isVip(): bool
    {
        $type = $this->membership_type ?? $this->membership_tier ?? 'standard';
        if ($type === 'vip') {
            $expiresAt = $this->vip_expires_at ?? $this->membership_expires_at;
            if (! $expiresAt || $expiresAt->isFuture()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Lấy số điện thoại độc giả
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->attributes['phone'] ?? $this->attributes['phone_number'] ?? null;
    }

    /**
     * Lấy địa chỉ giao nhận mặc định
     */
    public function getAddressAttribute(): ?string
    {
        return $this->attributes['address'] ?? $this->attributes['delivery_address'] ?? null;
    }

    /**
     * Số lượng mượn tối đa: 15 cuốn cho Scholar VIP, 3 cuốn cho Standard
     */
    public function maxBorrowLimit(): int
    {
        return $this->isVip() ? 15 : 3;
    }

    /**
     * Thời hạn mượn sách: 30 ngày cho Scholar VIP, 14 ngày cho Standard
     */
    public function borrowDurationDays(): int
    {
        return $this->isVip() ? 30 : 14;
    }

    /**
     * Số lần được phép gia hạn sách: tối đa 2 lần cho VIP, 1 lần cho Standard
     */
    public function maxRenewalsAllowed(): int
    {
        return $this->isVip() ? 2 : 1;
    }

    /**
     * Hệ số nhân EXP thưởng: x2 EXP cho VIP, x1 cho Standard
     */
    public function expMultiplier(): int
    {
        return $this->isVip() ? 2 : 1;
    }

    /**
     * Số sách tối đa được mượn cùng lúc theo gói hội viên (Alias tương thích ngược)
     */
    public function getMaxBorrowBooksCount(): int
    {
        return $this->maxBorrowLimit();
    }

    /**
     * Kiểm tra người dùng có thể điểm danh hôm nay hay không (chưa điểm danh hoặc qua ngày mới).
     */
    public function canCheckInToday(): bool
    {
        return ! $this->last_checkin_at || ! $this->last_checkin_at->isToday();
    }

    /**
     * Lịch sử các lần mượn sách của người dùng này (Legacy BorrowRecord).
     */
    public function borrowRecords(): HasMany
    {
        return $this->hasMany(BorrowRecord::class);
    }

    /**
     * Danh sách các phiếu mượn (Bảng borrows mới).
     */
    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    /**
     * Kiểm tra quyền Admin (Quản trị viên tối cao).
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Kiểm tra quyền Thủ thư (Librarian).
     */
    public function isLibrarian(): bool
    {
        return $this->role === 'librarian';
    }

    /**
     * Kiểm tra quyền Độc giả (Reader).
     */
    public function isReader(): bool
    {
        return $this->role === 'reader';
    }

    /**
     * Kiểm tra user có một trong các roles được chỉ định hay không.
     * Hỗ trợ gọi: $user->hasAnyRole('admin', 'librarian') hoặc $user->hasAnyRole(['admin', 'librarian'])
     */
    public function hasAnyRole(string|array ...$roles): bool
    {
        $flattened = [];
        foreach ($roles as $role) {
            if (is_array($role)) {
                $flattened = array_merge($flattened, $role);
            } else {
                $flattened[] = $role;
            }
        }

        return in_array($this->role, $flattened, true);
    }

    /**
     * Alias cho hasAnyRole.
     */
    public function hasRole(string|array ...$roles): bool
    {
        return $this->hasAnyRole(...$roles);
    }

    /**
     * Lấy tên hiển thị tiếng Việt của vai trò.
     */
    public function getRoleNameAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Quản Trị Viên',
            'librarian' => 'Thủ Thư',
            default => 'Độc Giả',
        };
    }

    /**
     * BẢNG PHÂN BẬC ĐỘC GIẢ (6 CẤP BẬC EXP CAO CẤP):
     * - Cấp 1: [TÂN THỦ NHẬP MÔN] (0 - 150 EXP)
     * - Cấp 2: [TẬP SỰ HỌC ĐỒ] (151 - 500 EXP)
     * - Cấp 3: [HỌC GIẢ UYÊN THÂM] (501 - 1,500 EXP)
     * - Cấp 4: [CHIẾN TƯỚNG HACK MAP] (1,501 - 4,000 EXP)
     * - Cấp 5: [THÁCH ĐẤU BUFF BẨN] (4,001 - 10,000 EXP)
     * - Cấp 6: [ĐỘC CÔ CẦU BẠI] (10,000+ EXP)
     */
    public const TIERS = [
        [
            'level' => 1,
            'name' => 'TÂN THỦ NHẬP MÔN',
            'badge' => 'Tân Thủ Nhập Môn',
            'min' => 0,
            'max' => 150,
            'icon' => '<svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
            'badge_class' => 'bg-slate-100 text-slate-700 border-slate-300 font-bold',
            'glow_frame' => 'border-2 border-slate-300 shadow-sm',
        ],
        [
            'level' => 2,
            'name' => 'TẬP SỰ HỌC ĐỒ',
            'badge' => 'Tập Sự Học Đồ',
            'min' => 151,
            'max' => 500,
            'icon' => '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
            'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            'glow_frame' => 'border-2 border-emerald-400 drop-shadow-[0_0_6px_rgba(52,211,153,0.5)] shadow-[0_0_8px_rgba(52,211,153,0.4)]',
        ],
        [
            'level' => 3,
            'name' => 'HỌC GIẢ UYÊN THÂM',
            'badge' => 'Học Giả Uyên Thâm',
            'min' => 501,
            'max' => 1500,
            'icon' => '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>',
            'badge_class' => 'bg-blue-100 text-blue-800 border-blue-300 font-bold',
            'glow_frame' => 'border-2 border-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.6)] shadow-[0_0_10px_rgba(96,165,250,0.5)]',
        ],
        [
            'level' => 4,
            'name' => 'CHIẾN TƯỚNG HACK MAP',
            'badge' => 'Chiến Tướng Hack Map',
            'min' => 1501,
            'max' => 4000,
            'icon' => '<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.286L13 21l-2.286-6.857L5 12l5.714-2.286L13 3z"/></svg>',
            'badge_class' => 'bg-purple-100 text-purple-800 border-purple-300 font-bold',
            'glow_frame' => 'border-2 border-purple-500 drop-shadow-[0_0_10px_rgba(168,85,247,0.7)] shadow-[0_0_14px_rgba(168,85,247,0.6)]',
        ],
        [
            'level' => 5,
            'name' => 'THÁCH ĐẤU BUFF BẨN',
            'badge' => 'Thách Đấu Buff Bẩn',
            'min' => 4001,
            'max' => 10000,
            'icon' => '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/></svg>',
            'badge_class' => 'bg-amber-100 text-amber-900 border-amber-400 font-black',
            'glow_frame' => 'border-2 border-amber-400 drop-shadow-[0_0_12px_rgba(251,191,36,0.8)] shadow-[0_0_16px_rgba(251,191,36,0.6)]',
        ],
        [
            'level' => 6,
            'name' => 'ĐỘC CÔ CẦU BẠI',
            'badge' => 'Độc Cô Cầu Bại',
            'min' => 10001,
            'max' => 999999,
            'icon' => '<svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>',
            'badge_class' => 'bg-gradient-to-r from-red-600 to-amber-600 text-white font-black shadow-md',
            'glow_frame' => 'border-2 border-red-500 ring-2 ring-amber-400/80 drop-shadow-[0_0_14px_rgba(239,68,68,0.9)] shadow-[0_0_20px_rgba(245,158,11,0.8)] animate-pulse',
        ],
    ];

    /**
     * Kiểm tra người dùng có thuộc Ban Quản trị hệ thống (admin hoặc thủ thư).
     */
    public function isManagementStaff(): bool
    {
        return in_array($this->role, ['admin', 'librarian'], true);
    }

    /**
     * Lấy EXP của người dùng. Tài khoản Quản trị viên tự động sở hữu 9,999,999 EXP.
     */
    public function getExpAttribute($value): int
    {
        if ($this->isManagementStaff()) {
            return 9999999;
        }

        return (int) ($value ?? 0);
    }

    /**
     * Cập nhật danh hiệu độc giả theo 6 bậc EXP mới:
     */
    public function updateMartialTitle(): string
    {
        if ($this->isManagementStaff()) {
            $newBadge = 'Độc Cô Cầu Bại';
            $needsSave = false;

            if ((int) ($this->attributes['exp'] ?? 0) !== 9999999) {
                $this->attributes['exp'] = 9999999;
                $needsSave = true;
            }
            if ($this->title_badge !== $newBadge) {
                $this->title_badge = $newBadge;
                $needsSave = true;
            }
            if ($needsSave && $this->exists) {
                $this->save();
            }

            return $newBadge;
        }

        $exp = (int) ($this->exp ?? 0);

        if ($exp <= 150) {
            $newBadge = 'Tân Thủ Nhập Môn';
        } elseif ($exp <= 500) {
            $newBadge = 'Tập Sự Học Đồ';
        } elseif ($exp <= 1500) {
            $newBadge = 'Học Giả Uyên Thâm';
        } elseif ($exp <= 4000) {
            $newBadge = 'Chiến Tướng Hack Map';
        } elseif ($exp <= 10000) {
            $newBadge = 'Thách Đấu Buff Bẩn';
        } else {
            $newBadge = 'Độc Cô Cầu Bại';
        }

        if ($this->title_badge !== $newBadge) {
            $this->title_badge = $newBadge;
            if ($this->exists) {
                $this->save();
            }
        }

        return $newBadge;
    }

    /**
     * Lấy thông tin Tier hiện tại của người dùng.
     */
    public function getCurrentTierAttribute(): array
    {
        if ($this->isManagementStaff()) {
            return self::TIERS[count(self::TIERS) - 1]; // Tier 6: ĐỘC CÔ CẦU BẠI
        }

        $exp = (int) ($this->exp ?? 0);
        $current = self::TIERS[0];
        foreach (self::TIERS as $tier) {
            if ($exp >= $tier['min']) {
                $current = $tier;
            }
        }

        return $current;
    }

    /**
     * Cộng điểm EXP và cập nhật danh hiệu ngay lập tức (Áp dụng x2 EXP cho VIP).
     */
    public function awardExp(int $points): array
    {
        $multiplier = $this->expMultiplier();
        $finalPoints = $points * $multiplier;

        $oldBadge = $this->title_badge ?? 'Tân Thủ Nhập Môn';
        $this->exp = (int) ($this->exp ?? 0) + $finalPoints;
        $this->weekly_exp = (int) ($this->weekly_exp ?? 0) + $finalPoints;
        $newBadge = $this->updateMartialTitle();
        $this->save();

        return [
            'base_points' => $points,
            'multiplier' => $multiplier,
            'added_exp' => $finalPoints,
            'total_exp' => $this->exp,
            'weekly_exp' => $this->weekly_exp,
            'old_badge' => $oldBadge,
            'new_badge' => $newBadge,
            'badge_upgraded' => $oldBadge !== $newBadge,
        ];
    }

    /**
     * Lấy màu sắc badge theo danh hiệu.
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->title_badge) {
            'Tập Sự Học Đồ' => 'bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold',
            'Học Giả Uyên Thâm' => 'bg-blue-100 text-blue-800 border border-blue-300 font-bold',
            'Chiến Tướng Hack Map', 'Đại Khoa Học Sĩ' => 'bg-purple-100 text-purple-800 border border-purple-300 font-bold',
            'Thách Đấu Buff Bẩn', 'Đại Tông Sư Tri Thức' => 'bg-amber-100 text-amber-900 border border-amber-400 font-black',
            'Độc Cô Cầu Bại' => 'bg-gradient-to-r from-red-600 to-amber-600 text-white border border-red-500 font-black shadow-md',
            default => 'bg-slate-100 text-slate-700 border border-slate-300 font-bold',
        };
    }

    /**
     * Lấy icon tượng trưng cho danh hiệu dạng vector SVG sang trọng.
     */
    public function getBadgeIconSvgAttribute(): string
    {
        return match ($this->title_badge) {
            'Tập Sự Học Đồ' => '<svg class="w-3.5 h-3.5 text-emerald-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
            'Học Giả Uyên Thâm' => '<svg class="w-3.5 h-3.5 text-blue-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-5.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>',
            'Chiến Tướng Hack Map', 'Đại Khoa Học Sĩ' => '<svg class="w-3.5 h-3.5 text-purple-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.286L13 21l-2.286-6.857L5 12l5.714-2.286L13 3z"/></svg>',
            'Thách Đấu Buff Bẩn', 'Đại Tông Sư Tri Thức' => '<svg class="w-3.5 h-3.5 text-amber-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/></svg>',
            'Độc Cô Cầu Bại' => '<svg class="w-3.5 h-3.5 text-amber-300 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>',
            default => '<svg class="w-3.5 h-3.5 text-slate-500 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
        };
    }

    /**
     * Lấy icon tượng trưng cho danh hiệu (dạng chuỗi SVG hoặc icon tinh tế).
     */
    public function getBadgeIconAttribute(): string
    {
        return $this->badge_icon_svg;
    }

    /**
     * Khung viền Avatar phát sáng theo cấp bậc độc giả.
     */
    public function getAvatarGlowFrameAttribute(): string
    {
        $exp = (int) ($this->exp ?? 0);

        if ($this->isManagementStaff() || $exp > 10000 || $this->title_badge === 'Độc Cô Cầu Bại') {
            return 'border-2 border-red-500 ring-2 ring-amber-400/80 drop-shadow-[0_0_14px_rgba(239,68,68,0.9)] shadow-[0_0_20px_rgba(245,158,11,0.8)] animate-pulse';
        } elseif ($exp > 4000 || in_array($this->title_badge, ['Thách Đấu Buff Bẩn', 'Đại Tông Sư Tri Thức'])) {
            return 'border-2 border-amber-400 drop-shadow-[0_0_12px_rgba(251,191,36,0.8)] shadow-[0_0_16px_rgba(251,191,36,0.6)]';
        } elseif ($exp > 1500 || in_array($this->title_badge, ['Chiến Tướng Hack Map', 'Đại Khoa Học Sĩ'])) {
            return 'border-2 border-purple-500 drop-shadow-[0_0_10px_rgba(168,85,247,0.7)] shadow-[0_0_14px_rgba(168,85,247,0.6)]';
        } elseif ($exp > 500 || $this->title_badge === 'Học Giả Uyên Thâm') {
            return 'border-2 border-blue-400 drop-shadow-[0_0_8px_rgba(96,165,250,0.6)] shadow-[0_0_10px_rgba(96,165,250,0.5)]';
        } elseif ($exp > 150 || $this->title_badge === 'Tập Sự Học Đồ') {
            return 'border-2 border-emerald-400 drop-shadow-[0_0_6px_rgba(52,211,153,0.5)] shadow-[0_0_8px_rgba(52,211,153,0.4)]';
        }

        return 'border-2 border-slate-300 shadow-sm';
    }

    /**
     * Thẻ Badge Danh Hiệu (Title Tag).
     */
    public function getTitleBadgeClassAttribute(): string
    {
        return $this->badge_color;
    }

    /**
     * Tên danh hiệu được định dạng in hoa chuẩn: [ ĐỘC CÔ CẦU BẠI ]
     */
    public function getTitleFormattedAttribute(): string
    {
        $title = $this->title_badge ?: 'Tân Thủ Nhập Môn';

        return '[ '.mb_strtoupper($title, 'UTF-8').' ]';
    }

    /**
     * Tổng số tiền phạt chưa thanh toán của độc giả.
     * Tính chuẩn từ bảng borrow_records để tránh cộng dồn trùng lặp với bảng borrows mẹ.
     */
    public function getUnpaidFineAttribute(): float
    {
        return (float) BorrowRecord::where('user_id', $this->id)
            ->where('fine_amount', '>', 0)
            ->where('fine_status', 'unpaid')
            ->sum('fine_amount');
    }
}
