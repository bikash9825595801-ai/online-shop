<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $price = fake()->randomFloat(2, 5, 200);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'seller_id' => User::factory()->seller(),
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => round($price * $quantity, 2),
        ];
    }
}
