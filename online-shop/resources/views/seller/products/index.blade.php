@extends('layouts.seller')

@section('title', 'My Products')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">My Products</h4>
        <a href="{{ route('seller.products.create') }}" class="btn btn-brand btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('seller.products.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="search" name="search" class="form-control" placeholder="Search your products..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="approval" class="form-select">
                        <option value="">All approvals</option>
                        @foreach (['pending', 'approved', 'rejected'] as $approval)
                            <option value="{{ $approval }}" @selected(request('approval') == $approval)>{{ ucfirst($approval) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-brand w-100" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Approval</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td style="min-width:220px">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($product->image)
                                            <img src="{{ product_image_url($product->image) }}" class="avatar-sm" alt="">
                                        @endif
                                        <span class="small fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="small text-muted">{{ $product->category->name }}</td>
                                <td class="small">
                                    ${{ number_format($product->sellingPrice(), 2) }}
                                    @if ($product->hasDiscount())
                                        <span class="old-price">${{ number_format($product->price, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $product->stock <= 0 ? 'bg-danger' : ($product->stock <= 10 ? 'bg-warning text-dark' : 'bg-success') }}">{{ $product->stock }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ match($product->approval_status) { 'approved' => 'bg-success', 'pending' => 'bg-warning text-dark', 'rejected' => 'bg-danger', default => 'bg-secondary' } }}">
                                        {{ ucfirst($product->approval_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($product->status) }}</span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('seller.products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="Delete this product?"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">
                                No products found.
                                <a href="{{ route('seller.products.create') }}">Add your first product</a>.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$products" />
        </div>
    </div>
@endsection
