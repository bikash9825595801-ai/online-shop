<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:customer');
    }

    private function getWishlist(): Wishlist
    {
        $wishlist = Auth::user()->wishlist;

        if (! $wishlist) {
            $wishlist = Wishlist::create(['user_id' => Auth::id()]);
        }

        return $wishlist->load('items.product');
    }

    public function index(): View
    {
        $wishlist = $this->getWishlist();

        return view('wishlist.index', compact('wishlist'));
    }

    public function add(Product $product): RedirectResponse
    {
        if (! $product->isApproved()) {
            return back()->with('error', 'This product is not available.');
        }

        $wishlist = $this->getWishlist();

        if (! $wishlist->items()->where('product_id', $product->id)->exists()) {
            $wishlist->items()->create(['product_id' => $product->id]);
            return back()->with('success', "{$product->name} added to your wishlist.");
        }

        return back()->with('warning', 'This product is already in your wishlist.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $wishlist = $this->getWishlist();
        $wishlist->items()->where('product_id', $product->id)->delete();

        return back()->with('success', 'Product removed from wishlist.');
    }

    public function moveToCart(Product $product): RedirectResponse
    {
        $wishlist = $this->getWishlist();

        if (! $wishlist->items()->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'Product not found in your wishlist.');
        }

        if (! $product->inStock()) {
            return back()->with('error', "{$product->name} is out of stock.");
        }

        $cart = Auth::user()->cart()->firstOrCreate(['user_id' => Auth::id()]);

        $existing = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = ($existing?->quantity ?? 0) + 1;

        if ($newQuantity > $product->stock) {
            return back()->with('error', "Only {$product->stock} units of \"{$product->name}\" are available.");
        }

        if ($existing) {
            $existing->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create(['product_id' => $product->id, 'quantity' => 1]);
        }

        $wishlist->items()->where('product_id', $product->id)->delete();

        return redirect()->route('cart.index')->with('success', "{$product->name} moved to your cart.");
    }
}
