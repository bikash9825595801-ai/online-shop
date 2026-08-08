@extends('layouts.admin')

@section('title', 'Seller Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Seller Details</h4>
        <div>
            <a href="{{ route('admin.sellers.edit', $seller->id) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i> Edit</a>
            <a href="{{ route('admin.sellers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-blue">
                <div class="stat-value">{{ $seller->products->count() }}</div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-orange">
                <div class="stat-value">{{ $ordersCount }}</div>
                <div class="stat-label">Orders</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-green">
                <div class="stat-value">{{ $totalSales }}</div>
                <div class="stat-label">Units Sold</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-purple">
                <div class="stat-value">${{ number_format($revenue, 2) }}</div>
                <div class="stat-label">Revenue</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Products by {{ $seller->name }}</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Approval</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($seller->products as $product)
                            <tr>
                                <td class="small fw-semibold">{{ $product->name }}</td>
                                <td class="small">{{ $product->category->name }}</td>
                                <td class="small">${{ number_format($product->sellingPrice(), 2) }}</td>
                                <td class="small">{{ $product->stock }}</td>
                                <td>
                                    <span class="badge {{ match($product->approval_status) { 'approved' => 'bg-success', 'pending' => 'bg-warning text-dark', 'rejected' => 'bg-danger', default => 'bg-secondary' } }}">
                                        {{ ucfirst($product->approval_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($product->status) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No products.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
