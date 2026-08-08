<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Product::visible()->with(['category', 'reviews'])
            ->where('stock', '>', 0)
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $latest = Product::visible()->with(['category', 'reviews'])
            ->where('stock', '>', 0)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $discounted = Product::visible()->with(['category', 'reviews'])
            ->where('stock', '>', 0)
            ->whereNotNull('discount_price')
            ->whereColumn('discount_price', '<', 'price')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $categories = Category::where('status', Category::STATUS_ACTIVE)->orderBy('name')->get();

        return view('home', compact('featured', 'latest', 'discounted', 'categories'));
    }
}
