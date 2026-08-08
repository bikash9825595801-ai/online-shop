<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Notifications\OrderStatusNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:customer')->except('notAllowed');
    }

    public function index(): View
    {
        $orders = Order::where('customer_id', Auth::id())
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_unless($order->customer_id === Auth::id(), 403);

        $order->load(['items.product.category', 'payment']);

        // Products from delivered orders that can be reviewed
        $reviewable = collect();
        if ($order->isDelivered()) {
            $reviewable = $order->items()->with('product')->get()->map(function ($item) {
                $item->existing_review = Review::where('customer_id', Auth::id())
                    ->where('product_id', $item->product_id)
                    ->where('order_id', $item->order_id)
                    ->first();

                return $item;
            });
        }

        return view('orders.show', compact('order', 'reviewable'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->customer_id === Auth::id(), 403);

        $request->validate(['reason' => ['nullable', 'string', 'max:1000']]);

        if (! $order->canCancel()) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        $oldStatus = $order->order_status;
        $order->update([
            'order_status' => Order::ORDER_CANCELLED,
            'cancellation_reason' => $request->reason,
        ]);

        // Restore stock
        foreach ($order->items as $item) {
            $item->product()->increment('stock', $item->quantity);
        }

        OrderStatusNotification::send($order, $oldStatus);
        AuditService::log('Order cancelled', 'Orders', "Order {$order->order_number} cancelled by customer.");

        return back()->with('success', 'Order cancelled and stock has been restored.');
    }
}
