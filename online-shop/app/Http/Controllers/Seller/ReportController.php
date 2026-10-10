<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->get('period', 'monthly');
        $sellerId = Auth::id();

        $from = match ($period) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            default => now()->startOfMonth(),
        };

        $base = OrderItem::where('seller_id', $sellerId)
            ->where('created_at', '>=', $from)
            ->whereHas('order', fn ($q) => $q->where('order_status', '!=', Order::ORDER_CANCELLED));

        $sales = (clone $base)
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(subtotal) as revenue'),
                DB::raw('COUNT(*) as items'),
                DB::raw('COUNT(DISTINCT order_id) as orders')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $chartDays = $sales->pluck('day')->map(fn ($d) => Carbon::parse($d)->format('M d'));
        $chartRevenue = $sales->pluck('revenue');
        $chartOrders = $sales->pluck('orders');

        $periodRevenue = $sales->sum('revenue');
        $periodOrders = $sales->sum('orders');
        $periodItems = $sales->sum('items');
        $periodAvgOrder = $periodOrders > 0 ? $periodRevenue / $periodOrders : 0;

        $productWise = OrderItem::where('order_items.seller_id', $sellerId)
            ->where('order_items.created_at', '>=', $from)
            ->whereHas('order', fn ($q) => $q->where('order_status', '!=', Order::ORDER_CANCELLED))
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as revenue'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return view('seller.reports.index', compact(
            'period', 'sales', 'chartDays', 'chartRevenue', 'chartOrders',
            'periodRevenue', 'periodOrders', 'periodItems', 'periodAvgOrder', 'productWise'
        ));
    }
}
