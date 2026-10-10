<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Notifications\NewOrderForSellerNotification;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderStatusNotification;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('customer_id', $request->user()->id)
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return api_response(true, 'Orders fetched.', [
            'orders' => collect($orders->items())->map(fn (Order $o) => $this->orderPayload($o)),
            'pagination' => $this->paginationPayload($orders),
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->customer_id === $request->user()->id, 403, 'This order does not belong to you.');

        $order->load(['items.product.category', 'payment']);

        return api_response(true, 'Order fetched.', [
            'order' => $this->orderPayload($order, true),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod'],
        ]);

        $cart = $request->user()->cart()->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return api_response(false, 'Your cart is empty.', null, 422);
        }

        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock) {
                return api_response(false, "Only {$item->product->stock} units of \"{$item->product->name}\" are available.", null, 422);
            }
            if (! $item->product->isApproved() || ! $item->product->inStock()) {
                return api_response(false, "Product \"{$item->product->name}\" is no longer available.", null, 422);
            }
        }

        try {
            $order = DB::transaction(function () use ($request, $cart) {
                $order = Order::create([
                    'customer_id' => $request->user()->id,
                    'order_number' => 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(8)),
                    'total_amount' => round($cart->subtotal(), 2),
                    'shipping_address' => $request->shipping_address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postal_code' => $request->postal_code,
                    'country' => $request->country ?? 'Nepal',
                    'notes' => $request->notes,
                    'phone' => $request->phone,
                    'customer_name' => $request->name,
                    'customer_email' => $request->email ?? $request->user()->email,
                    'payment_status' => Order::PAYMENT_PENDING,
                    'order_status' => Order::ORDER_PENDING,
                ]);

                foreach ($cart->items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'seller_id' => $item->product->seller_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->sellingPrice(),
                        'subtotal' => round($item->product->sellingPrice() * $item->quantity, 2),
                    ]);

                    // Reduce product stock
                    Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => $request->payment_method, // 'cod'
                    'amount' => $order->total_amount,
                    'status' => Payment::STATUS_PENDING,
                ]);

                // Clear the cart
                $cart->items()->delete();

                return $order;
            });
        } catch (\Throwable $e) {
            report($e);

            return api_response(false, 'Something went wrong while placing your order. Please try again.', null, 500);
        }

        // Notifications (after the transaction succeeds)
        OrderPlacedNotification::send($order);
        foreach ($order->items as $orderItem) {
            NewOrderForSellerNotification::send($orderItem);
        }

        AuditService::log('Order placed', 'Orders', "Order {$order->order_number} placed via API.");

        $order->load(['items.product', 'payment']);

        return api_response(true, "Your order {$order->order_number} has been placed successfully. Pay Rs. ".number_format($order->total_amount, 2).' on delivery.', [
            'order' => $this->orderPayload($order, true),
        ], 201);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->customer_id === $request->user()->id, 403, 'This order does not belong to you.');

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:1000']]);

        if (! $order->canCancel()) {
            return api_response(false, 'This order can no longer be cancelled.', null, 422);
        }

        $oldStatus = $order->order_status;
        $order->update([
            'order_status' => Order::ORDER_CANCELLED,
            'cancellation_reason' => $data['reason'] ?? null,
        ]);

        // Restore stock
        foreach ($order->items as $item) {
            $item->product()->increment('stock', $item->quantity);
        }

        OrderStatusNotification::send($order, $oldStatus);
        AuditService::log('Order cancelled', 'Orders', "Order {$order->order_number} cancelled via API.");

        return api_response(true, 'Order cancelled and stock has been restored.');
    }

    public function reviewable(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->customer_id === $request->user()->id, 403, 'This order does not belong to you.');

        $items = collect();

        if ($order->isDelivered()) {
            $items = $order->items()->with('product')->get()->map(function (OrderItem $item) use ($request) {
                $existing = Review::where('customer_id', $request->user()->id)
                    ->where('product_id', $item->product_id)
                    ->where('order_id', $item->order_id)
                    ->first();

                return [
                    'order_item_id' => $item->id,
                    'product' => $this->productPayload($item->product),
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'existing_review' => $existing ? [
                        'id' => $existing->id,
                        'rating' => $existing->rating,
                        'comment' => $existing->comment,
                    ] : null,
                ];
            });
        }

        return api_response(true, 'Reviewable items fetched.', [
            'items' => $items->values(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function orderPayload(Order $order, bool $withDetails = false): array
    {
        $payload = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'shipping_address' => $order->shipping_address,
            'city' => $order->city,
            'state' => $order->state,
            'postal_code' => $order->postal_code,
            'country' => $order->country,
            'notes' => $order->notes,
            'phone' => $order->phone,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'cancellation_reason' => $order->cancellation_reason,
            'created_at' => $order->created_at?->toIso8601String(),
            'items' => $order->items->map(fn (OrderItem $item) => [
                'id' => $item->id,
                'product' => $this->productPayload($item->product),
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
            ])->values(),
        ];

        if ($withDetails && $order->payment) {
            $payload['payment'] = [
                'method' => $order->payment->payment_method,
                'amount' => $order->payment->amount,
                'status' => $order->payment->status,
                'transaction_id' => $order->payment->transaction_id,
                'paid_at' => $order->payment->paid_at?->toIso8601String(),
            ];
        }

        return $payload;
    }
}
