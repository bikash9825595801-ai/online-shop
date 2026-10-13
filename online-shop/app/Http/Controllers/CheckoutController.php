<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Notifications\NewOrderForSellerNotification;
use App\Notifications\OrderPlacedNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:customer')->only('index', 'store');
    }

    public function index(): View|RedirectResponse
    {
        $cart = Auth::user()->cart()->with(['items.product.category'])->first();

        if (! $cart || $cart->items->isEmpty()) {
            return view('checkout.empty');
        }

        // Drop items that are no longer purchasable (unapproved, inactive or
        // out of stock) so the customer is never bounced back by a stale row.
        $unavailable = [];
        foreach ($cart->items as $item) {
            $product = $item->product;

            if (! $product
                || ! $product->isApproved()
                || $product->status !== Product::STATUS_ACTIVE
                || ! $product->inStock()) {
                $unavailable[] = $product?->name ?? 'A product';
                $item->delete();
            }
        }

        if ($unavailable !== []) {
            $cart->load('items.product.category');

            if ($cart->items->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('warning', 'These items are no longer available and were removed from your cart: '.implode(', ', $unavailable).'.');
            }
        }

        return view('checkout.index', compact('cart', 'unavailable'));
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $cart = Auth::user()->cart()->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Verify stock before opening the transaction
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->with('checkout_error', "Only {$item->product->stock} units of \"{$item->product->name}\" are available.");
            }
            if (! $item->product->isApproved() || $item->product->status !== Product::STATUS_ACTIVE || ! $item->product->inStock()) {
                return back()->with('checkout_error', "Product \"{$item->product->name}\" is no longer available.");
            }
        }

        try {
            $order = DB::transaction(function () use ($request, $cart) {
                $total = $cart->subtotal();

                $order = Order::create([
                    'customer_id' => Auth::id(),
                    'order_number' => 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(8)),
                    'total_amount' => round($total, 2),
                    'shipping_address' => $request->shipping_address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'postal_code' => $request->postal_code,
                    'country' => $request->country,
                    'notes' => $request->notes,
                    'phone' => $request->phone,
                    'customer_name' => $request->name,
                    'customer_email' => $request->email,
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

            return back()->with('checkout_error', 'Something went wrong while placing your order. Please try again.');
        }

        // Notifications (after the transaction succeeds)
        OrderPlacedNotification::send($order);
        foreach ($order->items as $orderItem) {
            NewOrderForSellerNotification::send($orderItem);
        }

        // Open a direct conversation with every seller in this order and put the
        // customer's message (order notes) right next to them.
        $sellerIds = $order->items->pluck('seller_id')->unique()->filter();
        foreach ($sellerIds as $sellerId) {
            $conversation = Conversation::firstOrCreate(
                ['customer_id' => Auth::id(), 'seller_id' => $sellerId],
                ['order_id' => $order->id, 'last_message_at' => now()],
            );

            if ($request->filled('notes')) {
                $conversation->messages()->create([
                    'sender_id' => Auth::id(),
                    'message' => 'Order notes for '.$order->order_number.":\n".trim($request->notes),
                ]);
                $conversation->update(['last_message_at' => now()]);
            }
        }

        AuditService::log('Order placed', 'Orders', "Order {$order->order_number} placed for $".number_format($order->total_amount, 2));

        // Online / bank payments continue to the payment gateway; COD is done.
        if (in_array($order->payment->payment_method, [Payment::METHOD_COD], true)) {
            return redirect()->route('orders.show', $order->id)
                ->with('success', "Your order {$order->order_number} has been placed successfully. Pay Rs. ".number_format($order->total_amount, 2).' on delivery.');
        }

        return redirect()->route('payment.initiate', $order->id)
            ->with('success', "Order {$order->order_number} created. Please complete your payment to confirm it.");
    }
}
