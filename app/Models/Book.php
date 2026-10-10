<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $title
 * @property string $author
 * @property string|null $publisher
 * @property int|null $publish_year
 * @property string|null $description
 * @property string|null $cover_image
 * @property int $quantity
 * @property int $available_copies
 * @property string|null $isbn
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read string $category_slug
 * @property-read string $local_cover_url
 * @property-read string $cover_url
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BorrowRecord> $borrowRecords
 */
class Book extends Model
{
    use HasFactory;

    // Cho phép thêm dữ liệu vào các cột này
    protected $fillable = [
        'category_id',
        'title',
        'author',
        'publisher',
        'publish_year',
        'description',
        'cover_image',
        'quantity',
        'available_copies',
    ];

    /**
     * Số lượng sách khả dụng trong kho.
     */
    public function getAvailableCopiesAttribute(): int
    {
        return (int) ($this->attributes['available_copies'] ?? $this->attributes['quantity'] ?? 0);
    }

    /**
     * Xác định slug thể loại để tải ảnh bìa cục bộ (Tầng 2)
     */
    public function getCategorySlugAttribute(): string
    {
        $name = mb_strtolower($this->category?->name ?? '', 'UTF-8');
        if (str_contains($name, 'lập trình') || str_contains($name, 'phần mềm') || str_contains($name, 'cntt') || str_contains($name, 'công nghệ thông tin')) return 'cntt';
        if (str_contains($name, 'trí tuệ nhân tạo') || str_contains($name, 'ai') || str_contains($name, 'dữ liệu')) return 'ai-data';
        if (str_contains($name, 'an toàn thông tin') || str_contains($name, 'hacker') || str_contains($name, 'bảo mật')) return 'an-toan-thong-tin';
        if (str_contains($name, 'văn học') || str_contains($name, 'tiểu thuyết') || str_contains($name, 'thơ')) return 'van-hoc';
        if (str_contains($name, 'tâm lý') || str_contains($name, 'bản thân')) return 'tam-ly';
        if (str_contains($name, 'kinh tế') || str_contains($name, 'tài chính') || str_contains($name, 'đầu tư') || str_contains($name, 'marketing')) return 'kinh-te';
        if (str_contains($name, 'vũ trụ') || str_contains($name, 'vật lý') || str_contains($name, 'môi trường') || str_contains($name, 'trái đất') || str_contains($name, 'não bộ')) return 'khoa-hoc';
        if (str_contains($name, 'lịch sử') || str_contains($name, 'quân sự') || str_contains($name, 'chiến lược')) return 'lich-su';
        if (str_contains($name, 'triết học') || str_contains($name, 'nhân sinh')) return 'triet-hoc';
        if (str_contains($name, 'y học') || str_contains($name, 'y tế') || str_contains($name, 'sức khỏe') || str_contains($name, 'dinh dưỡng')) return 'y-hoc';
        if (str_contains($name, 'nghệ thuật') || str_contains($name, 'kiến trúc') || str_contains($name, 'thiết kế') || str_contains($name, 'xây dựng') || str_contains($name, 'ui/ux')) return 'nghe-thuat';
        if (str_contains($name, 'viễn tưởng') || str_contains($name, 'sci-fi') || str_contains($name, 'cyberpunk')) return 'scifi';
        if (str_contains($name, 'truyện tranh') || str_contains($name, 'manga') || str_contains($name, 'light novel')) return 'manga';
        return 'default';
    }

    /**
     * Đường dẫn ảnh bìa cục bộ theo thể loại (Tầng 2)
     */
    public function getLocalCoverUrlAttribute(): string
    {
        return asset('images/covers/' . $this->category_slug . '.svg');
    }

    /**
     * Lấy URL ảnh bìa sách (Tầng 1 nếu có, Tầng 2 cục bộ nếu chưa có ảnh)
     */
    public function getCoverUrlAttribute(): string
    {
        if (!empty($this->cover_image) && !str_contains($this->cover_image, 'placehold.co')) {
            if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
                return $this->cover_image;
            }
            return asset('storage/' . ltrim($this->cover_image, '/'));
        }

        return $this->local_cover_url;
    }

    // Một cuốn sách THUỘC VỀ một thể loại
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Một cuốn sách có thể có nhiều lượt mượn.
     */
    public function borrowRecords(): HasMany
    {
        return $this->hasMany(BorrowRecord::class);
    }

    /**
     * Chi tiết mượn qua bảng borrow_details.
     */
    public function borrowDetails(): HasMany
    {
        return $this->hasMany(BorrowDetail::class);
    }

    /**
     * Các phiếu mượn có chứa cuốn sách này.
     */
    public function borrows()
    {
        return $this->belongsToMany(Borrow::class, 'borrow_details', 'book_id', 'borrow_id')
            ->withPivot(['quantity', 'returned_quantity', 'status'])
            ->withTimestamps();
    }
}