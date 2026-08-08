<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $threshold = (int) config('settings.low_stock_threshold', 10);

        $query = Product::with(['category', 'seller']);

        $view = $request->get('view', 'all');

        if ($view === 'low') {
            $query->where('stock', '<=', $threshold)->where('stock', '>', 0);
        } elseif ($view === 'out') {
            $query->where('stock', '<=', 0);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . trim($request->search) . '%');
        }

        $products = $query->orderBy('stock')->paginate(15)->withQueryString();

        $totalStockValue = Product::select(DB::raw('SUM(stock * COALESCE(discount_price, price)) as value'))->value('value');
        $totalUnits = Product::sum('stock');
        $lowStockCount = Product::where('stock', '<=', $threshold)->where('stock', '>', 0)->count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();
        $totalProducts = Product::count();

        // High-demand products (by quantity sold)
        $highDemand = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select('products.id', 'products.name', 'products.stock', DB::raw('SUM(order_items.quantity) as sold'))
            ->groupBy('products.id', 'products.name', 'products.stock')
            ->orderByDesc('sold')
            ->limit(10)
            ->get();

        return view('admin.inventory.index', compact(
            'products', 'threshold', 'view', 'totalStockValue', 'totalUnits',
            'lowStockCount', 'outOfStockCount', 'totalProducts', 'highDemand'
        ));
    }

    public function updateStock(Request $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product->update(['stock' => $request->stock]);

        \App\Services\AuditService::log('Stock updated', 'Inventory', "Stock for \"{$product->name}\" set to {$request->stock}.");

        return back()->with('success', 'Stock updated successfully.');
    }
}
