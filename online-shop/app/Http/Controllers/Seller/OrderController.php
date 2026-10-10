<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\Payment;
use App\Notifications\OrderStatusNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::whereHas('items', fn ($q) => $q->where('seller_id', Auth::id()))
            ->with(['customer', 'items' => fn ($q) => $q->where('seller_id', Auth::id())->with('product')]);

        if ($request->filled('status') && in_array($request->status, Order::ORDER_STATUSES)) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%'.trim($request->search).'%');
        }

        $orders = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        return view('seller.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_unless($order->items()->where('seller_id', Auth::id())->exists(), 403);

        $order->load([
            'customer',
            'payment',
            'items' => fn ($q) => $q->where('seller_id', Auth::id())->with('product'),
        ]);

        $conversation = Conversation::where('customer_id', $order->customer_id)
            ->where('seller_id', Auth::id())
            ->first();

        return view('seller.orders.show', compact('order', 'conversation'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->items()->where('seller_id', Auth::id())->exists(), 403);

        $request->validate([
            'order_status' => ['required', 'in:'.implode(',', Order::ORDER_STATUSES)],
        ]);

        if ($order->isCancelled()) {
            return back()->with('error', 'A cancelled order cannot be updated.');
        }

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        $order->update(['order_status' => $newStatus]);

        if ($newStatus === Order::ORDER_DELIVERED && $order->payment_status === Order::PAYMENT_PENDING) {
            $order->update(['payment_status' => Order::PAYMENT_PAID]);
            if ($order->payment) {
                $order->payment()->update([
                    'status' => Payment::STATUS_PAID,
                    'paid_at' => now(),
                    'transaction_id' => $order->payment->transaction_id ?? 'COD-'.strtoupper(substr(md5($order->id.now()), 0, 12)),
                ]);
            }
        }

        OrderStatusNotification::send($order->fresh(), $oldStatus);
        AuditService::log('Order status changed', 'Orders', "Order {$order->order_number} changed from {$oldStatus} to {$newStatus} by seller.");

        return back()->with('success', "Order {$order->order_number} marked as {$newStatus}.");
    }
}
