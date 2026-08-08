<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const ORDER_PENDING = 'pending';
    public const ORDER_CONFIRMED = 'confirmed';
    public const ORDER_PROCESSING = 'processing';
    public const ORDER_SHIPPED = 'shipped';
    public const ORDER_DELIVERED = 'delivered';
    public const ORDER_CANCELLED = 'cancelled';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';
    public const PAYMENT_FAILED = 'failed';

    public const ORDER_STATUSES = [
        self::ORDER_PENDING,
        self::ORDER_CONFIRMED,
        self::ORDER_PROCESSING,
        self::ORDER_SHIPPED,
        self::ORDER_DELIVERED,
        self::ORDER_CANCELLED,
    ];

    protected $fillable = [
        'customer_id',
        'order_number',
        'total_amount',
        'shipping_address',
        'phone',
        'customer_name',
        'payment_status',
        'order_status',
        'cancellation_reason',
    ];

    protected $casts = [
        'total_amount' => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isCancelled(): bool
    {
        return $this->order_status === self::ORDER_CANCELLED;
    }

    public function isDelivered(): bool
    {
        return $this->order_status === self::ORDER_DELIVERED;
    }

    public function canCancel(): bool
    {
        return in_array($this->order_status, [self::ORDER_PENDING, self::ORDER_CONFIRMED], true);
    }
}
