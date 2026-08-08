<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'total_amount' => fake()->randomFloat(2, 20, 1000),
            'shipping_address' => fake()->address(),
            'phone' => fake()->numerify('0#########'),
            'customer_name' => fake()->name(),
            'payment_status' => fake()->randomElement([Order::PAYMENT_PENDING, Order::PAYMENT_PAID]),
            'order_status' => fake()->randomElement(Order::ORDER_STATUSES),
        ];
    }
}
