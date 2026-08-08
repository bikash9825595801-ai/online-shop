<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public const APPROVAL_PENDING = 'pending';
    public const APPROVAL_APPROVED = 'approved';
    public const APPROVAL_REJECTED = 'rejected';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'sku',
        'brand',
        'image',
        'status',
        'approval_status',
    ];

    protected $casts = [
        'price' => 'float',
        'discount_price' => 'float',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Products that are visible on the customer storefront. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED)
            ->where('status', self::STATUS_ACTIVE);
    }

    /** Effective selling price (discount price wins when set and lower). */
    public function sellingPrice(): float
    {
        if ($this->discount_price && $this->discount_price < $this->price) {
            return (float) $this->discount_price;
        }

        return (float) $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->discount_price !== null
            && $this->discount_price > 0
            && $this->discount_price < $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount() || $this->price <= 0) {
            return 0;
        }

        return (int) round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function isLowStock(int $threshold = 10): bool
    {
        return $this->stock <= $threshold;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }

    public function averageRating(): float
    {
        return round((float) $this->reviews()->where('status', Review::STATUS_APPROVED)->avg('rating'), 1);
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->where('status', Review::STATUS_APPROVED)->count();
    }
}
