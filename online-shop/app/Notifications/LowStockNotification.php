<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\User;
use App\Services\NotificationService;

class LowStockNotification
{
    /**
     * Notify the seller (and admins) that a product is low on stock.
     */
    public static function send(Product $product, int $threshold): void
    {
        $message = "Product \"{$product->name}\" is low on stock. Only {$product->stock} units remaining (threshold: {$threshold}).";

        NotificationService::send(
            $product->seller_id,
            'inventory',
            'Low stock warning',
            $message,
            route('seller.inventory.index'),
        );

        NotificationService::sendToAdmins(
            'inventory',
            'Low stock warning',
            $message,
            route('admin.inventory.index'),
        );
    }
}
