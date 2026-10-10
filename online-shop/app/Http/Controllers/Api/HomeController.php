<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class HomeController extends ApiController
{
    public function index(): JsonResponse
    {
        $map = fn (Product $p) => $this->productPayload($p);

        $featured = Product::visible()->with(['category'])->where('stock', '>', 0)->orderByDesc('id')->limit(8)->get()->map($map);
        $latest = Product::visible()->with(['category'])->where('stock', '>', 0)->orderByDesc('created_at')->limit(8)->get()->map($map);
        $discounted = Product::visible()->with(['category'])->where('stock', '>', 0)
            ->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price')
            ->orderByDesc('id')->limit(8)->get()->map($map);

        $categories = Category::where('status', Category::STATUS_ACTIVE)
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'image' => product_image_url($c->image),
            ]);

        return api_response(true, 'Home data fetched.', compact('featured', 'latest', 'discounted', 'categories'));
    }
}
