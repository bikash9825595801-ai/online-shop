<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount(['products' => function (Builder $query) {
            $query->visible();
        }])
            ->where('status', Category::STATUS_ACTIVE)
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'image' => product_image_url($c->image),
                'products_count' => $c->products_count,
            ]);

        return api_response(true, 'Categories fetched.', ['categories' => $categories]);
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        abort_unless($category->isActive(), 404);

        $query = Product::visible()->with(['category'])->where('category_id', $category->id);

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

        $perPage = min((int) $request->get('per_page', 12), 50);
        $products = $query->paginate($perPage);

        return api_response(true, 'Category fetched.', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image' => product_image_url($category->image),
            ],
            'products' => collect($products->items())->map(fn (Product $p) => $this->productPayload($p)),
            'pagination' => $this->paginationPayload($products),
        ]);
    }
}
