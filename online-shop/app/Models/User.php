<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SELLER = 'seller';

    public const ROLE_CUSTOMER = 'customer';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'address',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /** Products this seller owns. */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /** Orders placed by this customer. */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /** Orders containing this seller's products. */
    public function sellerOrders(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'seller_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class);
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    /** Conversations where this user is the customer. */
    public function conversationsAsCustomer(): HasMany
    {
        return $this->hasMany(Conversation::class, 'customer_id');
    }

    /** Conversations where this user is the seller. */
    public function conversationsAsSeller(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    /** Messages this user sent in conversations. */
    public function conversationMessages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class, 'sender_id');
    }

    /** Total unread conversation messages across all conversations for this user. */
    public function unreadMessageCount(): int
    {
        return ConversationMessage::whereNull('read_at')
            ->where('sender_id', '!=', $this->id)
            ->whereHas('conversation', fn ($q) => $q->forUser($this->id))
            ->count();
    }
}
