<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        return api_response(true, 'Cart fetched.', $this->cartPayload($this->getCart($request)));
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::visible()->findOrFail($data['product_id']);
        $cart = $this->getCart($request);

        $quantity = (int) $data['quantity'];
        $existing = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = ($existing?->quantity ?? 0) + $quantity;

        if ($newQuantity > $product->stock) {
            return api_response(false, "Only {$product->stock} units of \"{$product->name}\" are available in stock.", null, 422);
        }

        if ($existing) {
            $existing->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create(['product_id' => $product->id, 'quantity' => $quantity]);
        }

        return api_response(true, "{$product->name} added to cart.", $this->cartPayload($cart->load('items.product')));
    }

    public function update(Request $request, CartItem $item): JsonResponse
    {
        $this->authorizeCartItem($request, $item);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quantity = (int) $data['quantity'];

        if ($quantity > $item->product->stock) {
            return api_response(false, "Only {$item->product->stock} units of \"{$item->product->name}\" are available.", null, 422);
        }

        $item->update(['quantity' => $quantity]);

        return api_response(true, 'Cart updated.', $this->cartPayload($this->getCart($request)));
    }

    public function destroy(Request $request, CartItem $item): JsonResponse
    {
        $this->authorizeCartItem($request, $item);

        $item->delete();

        return api_response(true, 'Item removed from cart.', $this->cartPayload($this->getCart($request)));
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->getCart($request);
        $cart->items()->delete();

        return api_response(true, 'Cart cleared.', $this->cartPayload($cart));
    }

    private function getCart(Request $request): Cart
    {
        $cart = $request->user()->cart()->with('items.product')->first();

        if (! $cart) {
            $cart = Cart::create(['user_id' => $request->user()->id]);
        }

        return $cart->load('items.product');
    }

    private function authorizeCartItem(Request $request, CartItem $item): void
    {
        if ($item->cart->user_id !== $request->user()->id) {
            abort(403, 'This cart item does not belong to you.');
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function cartPayload(Cart $cart): array
    {
        return [
            'items' => $cart->items->map(fn (CartItem $item) => [
                'id' => $item->id,
                'product' => $this->productPayload($item->product),
                'quantity' => $item->quantity,
                'line_total' => $item->lineTotal(),
            ])->values(),
            'item_count' => $cart->itemCount(),
            'subtotal' => round($cart->subtotal(), 2),
            'total' => round($cart->total(), 2),
        ];
    }
}
