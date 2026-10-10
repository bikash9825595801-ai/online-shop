<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $wishlist = $this->getWishlist($request);

        return api_response(true, 'Wishlist fetched.', [
            'items' => $wishlist->items->map(fn (WishlistItem $item) => $this->productPayload($item->product))->values(),
        ]);
    }

    public function add(Request $request, Product $product): JsonResponse
    {
        if (! $product->isApproved()) {
            return api_response(false, 'This product is not available.', null, 404);
        }

        $wishlist = $this->getWishlist($request);

        if ($wishlist->items()->where('product_id', $product->id)->exists()) {
            return api_response(false, 'This product is already in your wishlist.', null, 422);
        }

        $wishlist->items()->create(['product_id' => $product->id]);

        return api_response(true, "{$product->name} added to your wishlist.");
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $wishlist = $this->getWishlist($request);
        $wishlist->items()->where('product_id', $product->id)->delete();

        return api_response(true, 'Product removed from wishlist.');
    }

    public function moveToCart(Request $request, Product $product): JsonResponse
    {
        $wishlist = $this->getWishlist($request);

        if (! $wishlist->items()->where('product_id', $product->id)->exists()) {
            return api_response(false, 'Product not found in your wishlist.', null, 404);
        }

        if (! $product->inStock()) {
            return api_response(false, "{$product->name} is out of stock.", null, 422);
        }

        $cart = $request->user()->cart()->firstOrCreate(['user_id' => $request->user()->id]);

        $existing = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = ($existing?->quantity ?? 0) + 1;

        if ($newQuantity > $product->stock) {
            return api_response(false, "Only {$product->stock} units of \"{$product->name}\" are available.", null, 422);
        }

        if ($existing) {
            $existing->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);
        }

        $wishlist->items()->where('product_id', $product->id)->delete();

        return api_response(true, "{$product->name} moved to your cart.");
    }

    private function getWishlist(Request $request): Wishlist
    {
        $wishlist = $request->user()->wishlist;

        if (! $wishlist) {
            $wishlist = Wishlist::create(['user_id' => $request->user()->id]);
        }

        return $wishlist->load('items.product');
    }
}
