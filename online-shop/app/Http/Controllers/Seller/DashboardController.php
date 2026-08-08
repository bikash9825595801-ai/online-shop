<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $threshold = (int) config('settings.low_stock_threshold', 10);

        $totalProducts = Product::where('seller_id', $user->id)->count();
        $approvedProducts = Product::where('seller_id', $user->id)->where('approval_status', Product::APPROVAL_APPROVED)->count();
        $pendingApprovals = Product::where('seller_id', $user->id)->where('approval_status', Product::APPROVAL_PENDING)->count();
        $rejectedProducts = Product::where('seller_id', $user->id)->where('approval_status', Product::APPROVAL_REJECTED)->count();
        $lowStockProducts = Product::where('seller_id', $user->id)->where('stock', '<=', $threshold)->count();

        $totalSales = OrderItem::where('seller_id', $user->id)->sum('quantity');
        $revenue = OrderItem::where('seller_id', $user->id)->whereHas('order', fn ($q) => $q->where('order_status', '!=', Order::ORDER_CANCELLED))->sum('subtotal');
        $totalOrders = OrderItem::where('seller_id', $user->id)->distinct('order_id')->count('order_id');

        $pendingOrders = Order::whereHas('items', fn ($q) => $q->where('seller_id', $user->id))
            ->whereIn('order_status', [Order::ORDER_PENDING, Order::ORDER_CONFIRMED])
            ->count();

        $recentOrders = Order::whereHas('items', fn ($q) => $q->where('seller_id', $user->id))
            ->with(['customer', 'items.product'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Sales for the last 7 days
        $salesByDay = OrderItem::where('seller_id', $user->id)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->whereHas('order', fn ($q) => $q->where('order_status', '!=', Order::ORDER_CANCELLED))
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(subtotal) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $chartDays = collect();
        $chartTotals = collect();
        for ($i = 6; $i >= 0; $i--) {
            $chartDays->push(now()->subDays($i)->format('M d'));
            $chartTotals->push((float) ($salesByDay[now()->subDays($i)->toDateString()]->total ?? 0));
        }

        return view('seller.dashboard', compact(
            'totalProducts', 'approvedProducts', 'pendingApprovals', 'rejectedProducts',
            'lowStockProducts', 'totalSales', 'revenue', 'totalOrders', 'pendingOrders',
            'recentOrders', 'chartDays', 'chartTotals', 'threshold'
        ));
    }
}
