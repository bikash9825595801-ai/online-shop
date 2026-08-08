<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:customer');
    }

    public function store(StoreReviewRequest $request): RedirectResponse
    {
        $order = Order::where('id', $request->order_id)
            ->where('customer_id', Auth::id())
            ->with('items')
            ->firstOrFail();

        if (! $order->isDelivered()) {
            return back()->with('error', 'You can only review products from delivered orders.');
        }

        $purchased = $order->items->contains('product_id', $request->product_id);
        abort_unless($purchased, 403, 'You can only review products you have purchased.');

        $product = Product::findOrFail($request->product_id);

        $existing = Review::where('customer_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('order_id', $order->id)
            ->first();

        if ($existing) {
            $existing->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => Review::STATUS_APPROVED,
            ]);

            AuditService::log('Review updated', 'Reviews', "Review updated for product {$product->name}.");

            return back()->with('success', 'Your review has been updated.');
        }

        Review::create([
            'customer_id' => Auth::id(),
            'product_id' => $request->product_id,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => Review::STATUS_APPROVED,
        ]);

        AuditService::log('Review created', 'Reviews', "New review added for product {$product->name}.");

        return back()->with('success', 'Thank you! Your review has been submitted.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        abort_unless($review->customer_id === Auth::id(), 403);

        $review->delete();

        return back()->with('success', 'Your review has been removed.');
    }
}
