<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount(['products' => function (Builder $query) {
            $query->visible();
        }])
            ->where('status', Category::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Request $request, Category $category): View
    {
        abort_unless($category->isActive(), 404);

        $query = Product::visible()->with(['category', 'reviews'])->where('category_id', $category->id);

        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc' => $query->orderByRaw('COALESCE(discount_price, price) ASC'),
                'price_desc' => $query->orderByRaw('COALESCE(discount_price, price) DESC'),
                'newest' => $query->orderByDesc('created_at'),
                default => $query->orderByDesc('id'),
            };
        } else {
            $query->orderByDesc('id');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('categories.show', compact('category', 'products'));
    }
}
