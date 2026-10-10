<?php

namespace App\Notifications;

use App\Models\Order;
use App\Services\NotificationService;

class OrderPlacedNotification
{
    /**
     * Notify the customer that their order has been placed.
     */
    public static function send(Order $order): void
    {
        NotificationService::send(
            $order->customer_id,
            'order',
            "Order {$order->order_number} placed",
            "Your order worth Rs. " . number_format($order->total_amount, 2) . " has been placed successfully. It is now pending confirmation.",
            route('orders.show', $order->id),
        );
    }
}
