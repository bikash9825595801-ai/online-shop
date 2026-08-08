<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    private function getCart(): Cart
    {
        $cart = Auth::user()->cart()->with(['items.product.category'])->first();

        if (! $cart) {
            $cart = Cart::create(['user_id' => Auth::id()]);
        }

        return $cart->load('items.product');
    }

    public function index(): View|RedirectResponse
    {
        if (Auth::guest()) {
            return redirect()->route('login')->with('warning', 'Please login to view your cart.');
        }

        $cart = $this->getCart();

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::visible()->findOrFail($request->product_id);
        $cart = $this->getCart();

        $quantity = (int) $request->quantity;

        $existing = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = ($existing?->quantity ?? 0) + $quantity;

        if ($newQuantity > $product->stock) {
            return back()->with('error', "Only {$product->stock} units of \"{$product->name}\" are available in stock.");
        }

        if ($existing) {
            $existing->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        if ($request->input('redirect_to') === 'checkout') {
            return redirect()->route('checkout.index');
        }

        return redirect()->route('cart.index')->with('success', "{$product->name} added to cart.");
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $this->authorizeCartItem($item);

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quantity = (int) $request->quantity;

        if ($quantity > $item->product->stock) {
            return back()->with('error', "Only {$item->product->stock} units of \"{$item->product->name}\" are available.");
        }

        $item->update(['quantity' => $quantity]);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $item): RedirectResponse
    {
        $this->authorizeCartItem($item);

        $item->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    private function authorizeCartItem(CartItem $item): void
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
