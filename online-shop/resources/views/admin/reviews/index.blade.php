@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Product Reviews</h4>
        <span class="text-muted small">{{ $reviews->total() }} review(s)</span>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">All ratings</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-brand w-100" type="submit"><i class="bi bi-search"></i> Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reviews as $review)
                            <tr>
                                <td class="small fw-semibold" style="min-width:180px">{{ $review->product->name }}</td>
                                <td class="small">{{ $review->customer->name }}</td>
                                <td>
                                    <span class="stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                        @endfor
                                    </span>
                                </td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($review->comment, 60) }}</td>
                                <td>
                                    <span class="badge {{ match($review->status) { 'approved' => 'bg-success', 'pending' => 'bg-warning text-dark', 'rejected' => 'bg-danger', default => 'bg-secondary' } }}">
                                        {{ ucfirst($review->status) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $review->created_at->format('M d, Y') }}</td>
                                <td class="text-end text-nowrap">
                                    @if ($review->status !== 'approved')
                                        <form action="{{ route('admin.reviews.status', $review->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button class="btn btn-sm btn-outline-success" type="submit" title="Approve"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    @endif
                                    @if ($review->status !== 'rejected')
                                        <form action="{{ route('admin.reviews.status', $review->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button class="btn btn-sm btn-outline-danger" type="submit" title="Reject"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-secondary" type="submit" data-confirm="Delete this review?"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No reviews found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$reviews" />
        </div>
    </div>
@endsection
