<?php

namespace App\Notifications;

use App\Models\Product;
use App\Services\NotificationService;

class ProductApprovalNotification
{
    /**
     * Notify the seller that one of their products was approved or rejected.
     */
    public static function send(Product $product): void
    {
        $approved = $product->isApproved();

        NotificationService::send(
            $product->seller_id,
            'product',
            $approved ? 'Product approved' : 'Product rejected',
            "Your product \"{$product->name}\" has been {$product->approval_status} by the administrator.",
            route('seller.products.edit', $product->id),
        );
    }
}
