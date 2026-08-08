<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCustomers = User::where('role', User::ROLE_CUSTOMER)->count();
        $totalSellers = User::where('role', User::ROLE_SELLER)->count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('order_status', '!=', Order::ORDER_CANCELLED)->sum('total_amount');
        $pendingOrders = Order::where('order_status', Order::ORDER_PENDING)->count();
        $lowStockProducts = Product::where('stock', '<=', config('settings.low_stock_threshold', 10))->count();
        $recentOrders = Order::with('customer')->latest()->limit(5)->get();
        $recentUsers = User::latest()->limit(5)->get();
        $pendingApprovals = Product::where('approval_status', Product::APPROVAL_PENDING)->count();
        $openComplaints = \App\Models\SupportMessage::whereIn('status', ['open', 'in_progress'])->count();

        // Sales for the last 7 days (for chart)
        $salesByDay = Order::where('order_status', '!=', Order::ORDER_CANCELLED)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $chartDays = collect();
        $chartTotals = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $chartDays->push(now()->subDays($i)->format('M d'));
            $chartTotals->push((float) ($salesByDay[$day]->total ?? 0));
        }

        // Top selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalCustomers', 'totalSellers', 'totalProducts', 'totalOrders', 'totalRevenue',
            'pendingOrders', 'lowStockProducts', 'recentOrders', 'recentUsers', 'pendingApprovals',
            'openComplaints', 'chartDays', 'chartTotals', 'topProducts'
        ));
    }
}
