<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowDetail extends Model
{
    use HasFactory;

    protected $table = 'borrow_details';

    protected $fillable = [
        'borrow_id',
        'book_id',
        'quantity',
        'returned_quantity',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'returned_quantity' => 'integer',
    ];

    /**
     * Thuộc về một phiếu mượn.
     */
    public function borrow(): BelongsTo
    {
        return $this->belongsTo(Borrow::class, 'borrow_id');
    }

    /**
     * Thuộc về một cuốn sách.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
