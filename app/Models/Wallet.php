<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'user_id', 'user_id')->latest();
    }

    /**
     * Nạp tiền vào ví
     */
    public function deposit(float $amount, string $description = 'Nạp tiền vào ví', ?string $referenceId = null): Transaction
    {
        $this->balance += $amount;
        $this->save();

        return Transaction::create([
            'user_id' => $this->user_id,
            'type' => 'deposit',
            'amount' => $amount,
            'status' => 'completed',
            'description' => $description,
            'reference_id' => $referenceId,
        ]);
    }

    /**
     * Trừ tiền ví (nếu đủ số dư)
     */
    public function withdraw(float $amount, string $type, string $description, ?string $referenceId = null): ?Transaction
    {
        if ($this->balance < $amount) {
            return null;
        }

        $this->balance -= $amount;
        $this->save();

        return Transaction::create([
            'user_id' => $this->user_id,
            'type' => $type,
            'amount' => $amount,
            'status' => 'completed',
            'description' => $description,
            'reference_id' => $referenceId,
        ]);
    }
}
