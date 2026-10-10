<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\Auth\Authenticatable;

abstract class ApiController extends Controller
{
    /**
     * @return array<string, mixed>
     */
    protected function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'brand' => $product->brand,
            'description' => $product->description,
            'price' => (float) $product->price,
            'discount_price' => $product->discount_price !== null ? (float) $product->discount_price : null,
            'selling_price' => $product->sellingPrice(),
            'has_discount' => $product->hasDiscount(),
            'discount_percent' => $product->discountPercent(),
            'stock' => $product->stock,
            'in_stock' => $product->inStock(),
            'image' => product_image_url($product->image),
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'seller_id' => $product->seller_id,
            'average_rating' => $product->averageRating(),
            'reviews_count' => $product->reviewsCount(),
            'created_at' => $product->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function userPayload(Authenticatable $user): array
    {
        return [
            'id' => $user->getAuthIdentifier(),
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'role' => $user->role,
            'profile_image' => $user->profile_image ? product_image_url($user->profile_image) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function paginationPayload(mixed $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
