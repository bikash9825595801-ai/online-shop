<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['customer', 'product']);

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        $reviews = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, Review $review): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $review->update(['status' => $request->status]);

        AuditService::log('Review status changed', 'Reviews', "Review #{$review->id} set to {$request->status}.");

        return back()->with('success', 'Review status updated.');
    }

    public function destroy(Review $review): \Illuminate\Http\RedirectResponse
    {
        $review->delete();

        AuditService::log('Review deleted', 'Reviews', "Review #{$review->id} deleted.");

        return back()->with('success', 'Review deleted.');
    }
}
