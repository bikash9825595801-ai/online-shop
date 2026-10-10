<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', User::ROLE_CUSTOMER)
            ->where('status', User::STATUS_ACTIVE)
            ->whereNotNull('address')
            ->get();
        $products = Product::where('approval_status', Product::APPROVAL_APPROVED)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Skipping order seeding: customers or products missing.');

            return;
        }

        $statuses = Order::ORDER_STATUSES;

        foreach ($customers as $customer) {
            // 2-4 orders per customer
            $ordersCount = random_int(2, 4);

            for ($i = 0; $i < $ordersCount; $i++) {
                $status = $statuses[array_rand($statuses)];
                $itemCount = random_int(1, 3);
                $chosenProducts = $products->random(min($itemCount, $products->count()));

                $total = 0;
                $orderItems = [];

                foreach ($chosenProducts as $product) {
                    $quantity = random_int(1, 3);
                    $price = $product->sellingPrice();
                    $subtotal = round($price * $quantity, 2);
                    $total += $subtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'seller_id' => $product->seller_id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];
                }

                $createdAt = Carbon::now()->subDays(random_int(1, 60))->subHours(random_int(0, 23));

                $paymentStatus = $status === Order::ORDER_DELIVERED || $status === Order::ORDER_SHIPPED
                    ? (in_array($status, [Order::ORDER_DELIVERED], true) ? Order::PAYMENT_PAID : Order::PAYMENT_PAID)
                    : (random_int(0, 1) ? Order::PAYMENT_PAID : Order::PAYMENT_PENDING);

                $order = Order::create([
                    'customer_id' => $customer->id,
                    'order_number' => 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(8)),
                    'total_amount' => round($total, 2),
                    'shipping_address' => $customer->address,
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'postal_code' => fake()->numerify('#####'),
                    'country' => 'Nepal',
                    'notes' => random_int(0, 3) === 0 ? fake()->sentence(8) : null,
                    'phone' => $customer->phone,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'payment_status' => $paymentStatus,
                    'order_status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                foreach ($orderItems as $item) {
                    OrderItem::create(array_merge($item, ['order_id' => $order->id, 'created_at' => $createdAt, 'updated_at' => $createdAt]));
                }

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'cod',
                    'transaction_id' => $paymentStatus === Order::PAYMENT_PAID ? 'TXN-'.strtoupper(Str::random(12)) : null,
                    'amount' => $order->total_amount,
                    'status' => $paymentStatus === Order::PAYMENT_PAID ? Payment::STATUS_PAID : Payment::STATUS_PENDING,
                    'paid_at' => $paymentStatus === Order::PAYMENT_PAID ? $createdAt->addDays(3) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Add reviews for delivered orders
                if ($order->isDelivered()) {
                    foreach ($order->items()->with('product')->get() as $orderItem) {
                        if (random_int(0, 2) !== 0) {
                            Review::create([
                                'customer_id' => $customer->id,
                                'product_id' => $orderItem->product_id,
                                'order_id' => $order->id,
                                'rating' => random_int(3, 5),
                                'comment' => fake()->realText(100),
                                'status' => Review::STATUS_APPROVED,
                                'created_at' => $createdAt->addDays(4),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
