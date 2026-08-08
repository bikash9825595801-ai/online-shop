<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::visible()->with(['category', 'seller'])->withCount(['reviews']);

        // Search by name, brand or SKU
        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('discount_price', '>=', $request->min_price)
                    ->orWhere(function (Builder $q2) use ($request) {
                        $q2->whereNull('discount_price')->where('price', '>=', $request->min_price);
                    });
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('discount_price', '<=', $request->max_price)
                    ->orWhere(function (Builder $q2) use ($request) {
                        $q2->whereNull('discount_price')->where('price', '<=', $request->max_price);
                    });
            });
        }

        // Filter by rating
        if ($request->filled('rating') && $request->rating > 0) {
            $query->whereHas('reviews', function (Builder $q) use ($request) {
                $q->where('status', Review::STATUS_APPROVED)
                    ->groupBy('product_id')
                    ->havingRaw('AVG(rating) >= ?', [(float) $request->rating]);
            });
        }

        // Filter by availability
        if ($request->filled('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Sorting
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'rating':
                $query->orderByDesc(Product::selectRaw('AVG(reviews.rating)')->whereColumn('reviews.product_id', 'products.id')->where('reviews.status', Review::STATUS_APPROVED));
                break;
            default:
                $query->orderByDesc('id');
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('status', Category::STATUS_ACTIVE)->orderBy('name')->get();
        $brands = Product::visible()->whereNotNull('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->isApproved() && $product->status === Product::STATUS_ACTIVE, 404);

        $product->load(['category', 'seller', 'reviews.customer']);

        $reviews = $product->reviews()->where('status', Review::STATUS_APPROVED)->latest()->paginate(6);

        $related = Product::visible()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category'])
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'reviews', 'related'));
    }
}
