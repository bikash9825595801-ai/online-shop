<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'paid_at',
        'meta',
        'proof_path',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public const METHOD_COD = 'cod';

    public const METHOD_ESEWA = 'esewa';

    public const METHOD_KHALTI = 'khalti';

    public const METHOD_CARD = 'card';

    public const METHOD_BANK = 'bank';

    public const METHODS = [
        self::METHOD_COD,
        self::METHOD_ESEWA,
        self::METHOD_KHALTI,
        self::METHOD_CARD,
        self::METHOD_BANK,
    ];

    public const ONLINE_METHODS = [
        self::METHOD_ESEWA,
        self::METHOD_KHALTI,
        self::METHOD_CARD,
    ];

    public function methodLabel(): string
    {
        return match ($this->payment_method) {
            self::METHOD_ESEWA => 'eSewa',
            self::METHOD_KHALTI => 'Khalti',
            self::METHOD_CARD => 'Credit/Debit Card',
            self::METHOD_BANK => 'Bank Transfer',
            default => 'Cash on Delivery',
        };
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
