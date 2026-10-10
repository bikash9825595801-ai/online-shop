<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Notifications\OrderStatusNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'items.product']);

        if ($request->filled('status') && in_array($request->status, Order::ORDER_STATUSES)) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment') && in_array($request->payment, ['pending', 'paid', 'refunded', 'failed'])) {
            $query->where('payment_status', $request->payment);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.product.category', 'payment']);

        $customer = $order->customer;

        $customerStats = null;
        if ($customer) {
            $customerStats = [
                'total_orders' => Order::where('customer_id', $customer->id)->count(),
                'total_spent' => (float) Order::where('customer_id', $customer->id)
                    ->where('order_status', '!=', Order::ORDER_CANCELLED)
                    ->sum('total_amount'),
                'pending_orders' => Order::where('customer_id', $customer->id)
                    ->where('order_status', Order::ORDER_PENDING)
                    ->count(),
                'joined' => $customer->created_at,
                'status' => $customer->status,
                'last_order' => Order::where('customer_id', $customer->id)->latest('created_at')->first(),
            ];
        }

        return view('admin.orders.show', compact('order', 'customerStats'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'order_status' => ['required', 'in:'.implode(',', Order::ORDER_STATUSES)],
        ]);

        if ($order->isCancelled()) {
            return back()->with('error', 'A cancelled order cannot be updated.');
        }

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        $order->update(['order_status' => $newStatus]);

        // When a delivered order's payment was COD and unpaid, mark it paid
        if ($newStatus === Order::ORDER_DELIVERED && $order->payment_status === Order::PAYMENT_PENDING) {
            $order->update(['payment_status' => Order::PAYMENT_PAID]);
            $order->payment()?->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
                'transaction_id' => $order->payment?->transaction_id ?? 'COD-'.strtoupper(substr(md5($order->id.now()), 0, 12)),
            ]);
        }

        OrderStatusNotification::send($order->fresh(), $oldStatus);
        AuditService::log('Order status changed', 'Orders', "Order {$order->order_number} changed from {$oldStatus} to {$newStatus}.");

        return back()->with('success', "Order {$order->order_number} status updated to {$newStatus}.");
    }

    public function updatePayment(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => ['required', 'in:pending,paid,refunded,failed'],
        ]);

        $oldPayment = $order->payment_status;
        $order->update(['payment_status' => $request->payment_status]);

        if ($order->payment) {
            $order->payment()->update([
                'status' => match ($request->payment_status) {
                    Order::PAYMENT_PAID => Payment::STATUS_PAID,
                    Order::PAYMENT_REFUNDED => Payment::STATUS_REFUNDED,
                    Order::PAYMENT_FAILED => Payment::STATUS_FAILED,
                    default => Payment::STATUS_PENDING,
                },
                'paid_at' => $request->payment_status === Order::PAYMENT_PAID ? now() : $order->payment->paid_at,
            ]);
        }

        AuditService::log('Payment status changed', 'Payments', "Order {$order->order_number} payment changed from {$oldPayment} to {$request->payment_status}.");

        return back()->with('success', 'Payment status updated.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['reason' => ['nullable', 'string', 'max:1000']]);

        if ($order->isCancelled()) {
            return back()->with('error', 'This order is already cancelled.');
        }

        $oldStatus = $order->order_status;
        $order->update([
            'order_status' => Order::ORDER_CANCELLED,
            'cancellation_reason' => $request->reason ?? 'Cancelled by administrator',
        ]);

        // Restore stock
        foreach ($order->items as $item) {
            $item->product()->increment('stock', $item->quantity);
        }

        OrderStatusNotification::send($order->fresh(), $oldStatus);
        AuditService::log('Order cancelled', 'Orders', "Order {$order->order_number} cancelled by admin.");

        return back()->with('success', 'Order cancelled and stock restored.');
    }
}
