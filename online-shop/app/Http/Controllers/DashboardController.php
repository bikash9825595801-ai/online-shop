<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:customer');
    }

    public function index(): View
    {
        $user = Auth::user();

        $orders = Order::where('customer_id', $user->id)->orderByDesc('created_at')->limit(5)->get();
        $totalOrders = Order::where('customer_id', $user->id)->count();
        $pendingOrders = Order::where('customer_id', $user->id)->whereIn('order_status', ['pending', 'confirmed', 'processing', 'shipped'])->count();
        $deliveredOrders = Order::where('customer_id', $user->id)->where('order_status', Order::ORDER_DELIVERED)->count();
        $totalSpent = Order::where('customer_id', $user->id)->where('order_status', '!=', Order::ORDER_CANCELLED)->sum('total_amount');
        $wishlistCount = $user->wishlist?->items()->count() ?? 0;
        $cartCount = $user->cart?->itemCount() ?? 0;

        $reviewable = collect();
        $delivered = Order::where('customer_id', $user->id)->where('order_status', Order::ORDER_DELIVERED)->with('items.product')->get();
        foreach ($delivered as $order) {
            foreach ($order->items as $item) {
                $hasReview = $item->product->reviews()
                    ->where('customer_id', $user->id)
                    ->where('order_id', $order->id)
                    ->exists();
                if (! $hasReview) {
                    $reviewable->push($item->product);
                }
            }
        }

        return view('customer.dashboard', compact(
            'user', 'orders', 'totalOrders', 'pendingOrders', 'deliveredOrders',
            'totalSpent', 'wishlistCount', 'cartCount', 'reviewable'
        ));
    }
}
