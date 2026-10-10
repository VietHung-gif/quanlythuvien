<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryFund extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_name',
        'amount',
        'allocated_for',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Tên danh mục phân bổ quỹ
     */
    public function getAllocatedNameAttribute(): string
    {
        return match ($this->allocated_for) {
            'server_cloud' => 'Cơ sở hạ tầng, Server Cloud & AI',
            'book_procurement' => 'Mua sắm & Nhập sách mới',
            'staff_salary' => 'Thù lao Thủ thư & Kỹ thuật',
            'facility_maintenance' => 'Bảo trì trang thiết bị & Tu bổ sách',
            default => 'Quỹ vận hành chung',
        };
    }
}
