<?php

namespace App\Notifications;

use App\Models\Order;
use App\Services\NotificationService;

class OrderStatusNotification
{
    /**
     * Notify the customer that the status of their order changed.
     */
    public static function send(Order $order, string $oldStatus): void
    {
        NotificationService::send(
            $order->customer_id,
            'order',
            "Order {$order->order_number} is now {$order->order_status}",
            "Your order status changed from {$oldStatus} to {$order->order_status}.",
            route('orders.show', $order->id),
        );
    }
}
