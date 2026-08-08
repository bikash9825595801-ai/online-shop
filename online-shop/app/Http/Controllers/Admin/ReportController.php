<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->get('period', 'monthly');

        $from = match ($period) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            default => now()->startOfMonth(),
        };

        $to = now();

        // Sales over time (grouped by day)
        $sales = Order::where('order_status', '!=', Order::ORDER_CANCELLED)
            ->whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $chartDays = $sales->pluck('day')->map(fn ($d) => Carbon::parse($d)->format('M d'));
        $chartRevenue = $sales->pluck('revenue');
        $chartOrders = $sales->pluck('orders');

        // Totals for the period
        $periodRevenue = $sales->sum('revenue');
        $periodOrders = $sales->sum('orders');
        $periodAvgOrder = $periodOrders > 0 ? $periodRevenue / $periodOrders : 0;

        // Product performance
        $topProducts = OrderItem::query()
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->whereBetween('order_items.created_at', [$from, $to])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Seller performance
        $topSellers = OrderItem::query()
            ->join('users', 'users.id', '=', 'order_items.seller_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as orders')
            )
            ->whereBetween('order_items.created_at', [$from, $to])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Category performance
        $categoryPerformance = OrderItem::query()
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('SUM(order_items.subtotal) as revenue'), DB::raw('SUM(order_items.quantity) as total_qty'))
            ->whereBetween('order_items.created_at', [$from, $to])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        return view('admin.reports.index', compact(
            'period', 'sales', 'chartDays', 'chartRevenue', 'chartOrders',
            'periodRevenue', 'periodOrders', 'periodAvgOrder',
            'topProducts', 'topSellers', 'categoryPerformance'
        ));
    }
}
