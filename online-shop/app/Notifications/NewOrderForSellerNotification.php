<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\NotificationService;

class NewOrderForSellerNotification
{
    /**
     * Notify the seller that a new order contains their products.
     */
    public static function send(OrderItem $item): void
    {
        NotificationService::send(
            $item->seller_id,
            'order',
            'New order received',
            "You have a new order ({$item->order->order_number}) containing your product \"{$item->product->name}\".",
            route('seller.orders.show', $item->order_id),
        );
    }
}
