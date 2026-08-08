<?php

namespace App\Notifications;

use App\Models\Product;
use App\Services\NotificationService;

class ProductAwaitingApprovalNotification
{
    /**
     * Notify all admins that a new product awaits approval.
     */
    public static function send(Product $product): void
    {
        NotificationService::sendToAdmins(
            'product',
            'New product awaiting approval',
            "Product \"{$product->name}\" by seller {$product->seller->name} is waiting for approval.",
            route('admin.products.edit', $product->id),
            $product->seller_id,
        );
    }
}
