@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Product Details</h4>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i> Edit</a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            @if ($product->image)
                <img src="{{ product_image_url($product->image) }}" class="product-detail-image" alt="">
            @else
                <div class="product-detail-image d-flex align-items-center justify-content-center"><i class="bi bi-image text-muted" style="font-size:4rem"></i></div>
            @endif
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold">{{ $product->name }}</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge {{ match($product->approval_status) { 'approved' => 'bg-success', 'pending' => 'bg-warning text-dark', 'rejected' => 'bg-danger', default => 'bg-secondary' } }}">{{ ucfirst($product->approval_status) }}</span>
                        <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($product->status) }}</span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Price:</strong> ${{ number_format($product->price, 2) }}</div>
                        <div class="col-md-6"><strong>Discount Price:</strong> {{ $product->discount_price ? '$' . number_format($product->discount_price, 2) : '—' }}</div>
                        <div class="col-md-6 mt-2"><strong>Stock:</strong> {{ $product->stock }}</div>
                        <div class="col-md-6 mt-2"><strong>SKU:</strong> {{ $product->sku ?? '—' }}</div>
                        <div class="col-md-6 mt-2"><strong>Brand:</strong> {{ $product->brand ?? '—' }}</div>
                        <div class="col-md-6 mt-2"><strong>Seller:</strong> {{ $product->seller->name }}</div>
                        <div class="col-md-6 mt-2"><strong>Category:</strong> {{ $product->category->name }}</div>
                        <div class="col-md-6 mt-2"><strong>Average Rating:</strong> {{ $product->averageRating() }} ({{ $product->reviewsCount() }} reviews)</div>
                    </div>

                    <strong>Description</strong>
                    <p class="text-muted">{{ $product->description }}</p>

                    @if ($product->approval_status === 'pending')
                        <div class="d-flex gap-2">
                            <form action="{{ route('admin.products.approve', $product->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Approve</button>
                            </form>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bi bi-x-lg me-1"></i> Reject</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Customer Reviews</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($product->reviews as $review)
                            <tr>
                                <td class="small fw-semibold">{{ $review->customer->name }}</td>
                                <td>
                                    <span class="stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </span>
                                </td>
                                <td class="small">{{ $review->comment }}</td>
                                <td class="small text-muted">{{ $review->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No reviews yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.products.reject', $product->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label small">Reason (optional)</label>
                        <textarea name="reason" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
