<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends ApiController
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'order_id' => ['required', 'exists:orders,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('customer_id', $request->user()->id)
            ->with('items')
            ->firstOrFail();

        if (! $order->isDelivered()) {
            return api_response(false, 'You can only review products from delivered orders.', null, 422);
        }

        $purchased = $order->items->contains('product_id', $data['product_id']);
        if (! $purchased) {
            return api_response(false, 'You can only review products you have purchased.', null, 403);
        }

        $product = Product::findOrFail($data['product_id']);

        $existing = Review::where('customer_id', $request->user()->id)
            ->where('product_id', $data['product_id'])
            ->where('order_id', $order->id)
            ->first();

        if ($existing) {
            $existing->update([
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'status' => Review::STATUS_APPROVED,
            ]);

            AuditService::log('Review updated', 'Reviews', "Review updated for product {$product->name}.");

            return api_response(true, 'Your review has been updated.', ['review' => $this->reviewPayload($existing)]);
        }

        $review = Review::create([
            'customer_id' => $request->user()->id,
            'product_id' => $data['product_id'],
            'order_id' => $order->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'status' => Review::STATUS_APPROVED,
        ]);

        AuditService::log('Review created', 'Reviews', "New review added for product {$product->name}.");

        return api_response(true, 'Thank you! Your review has been submitted.', ['review' => $this->reviewPayload($review)], 201);
    }

    public function destroy(Request $request, Review $review): JsonResponse
    {
        abort_unless($review->customer_id === $request->user()->id, 403, 'This review does not belong to you.');

        $review->delete();

        return api_response(true, 'Your review has been removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function reviewPayload(Review $review): array
    {
        return [
            'id' => $review->id,
            'product_id' => $review->product_id,
            'order_id' => $review->order_id,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'status' => $review->status,
            'created_at' => $review->created_at?->toIso8601String(),
        ];
    }
}
