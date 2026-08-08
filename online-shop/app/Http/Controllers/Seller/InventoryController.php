<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $threshold = (int) config('settings.low_stock_threshold', 10);

        $query = Product::where('seller_id', Auth::id())->with('category');

        $view = $request->get('view', 'all');

        if ($view === 'low') {
            $query->where('stock', '<=', $threshold)->where('stock', '>', 0);
        } elseif ($view === 'out') {
            $query->where('stock', '<=', 0);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . trim($request->search) . '%');
        }

        $products = $query->orderBy('stock')->paginate(12)->withQueryString();

        $totalUnits = Product::where('seller_id', Auth::id())->sum('stock');
        $totalValue = Product::where('seller_id', Auth::id())
            ->selectRaw('SUM(stock * COALESCE(discount_price, price)) as value')
            ->value('value');
        $lowStockCount = Product::where('seller_id', Auth::id())->where('stock', '<=', $threshold)->where('stock', '>', 0)->count();
        $outOfStockCount = Product::where('seller_id', Auth::id())->where('stock', '<=', 0)->count();

        return view('seller.inventory.index', compact(
            'products', 'threshold', 'view', 'totalUnits', 'totalValue', 'lowStockCount', 'outOfStockCount'
        ));
    }

    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->seller_id === Auth::id(), 403);

        $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $threshold = (int) config('settings.low_stock_threshold', 10);

        $product->update(['stock' => $request->stock]);

        if ($product->stock <= $threshold) {
            LowStockNotification::send($product, $threshold);
        }

        AuditService::log('Stock updated', 'Inventory', "Stock for \"{$product->name}\" set to {$request->stock} by seller.");

        return back()->with('success', 'Stock updated successfully.');
    }
}
