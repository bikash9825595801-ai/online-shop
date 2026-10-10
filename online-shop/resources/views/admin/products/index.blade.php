@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Products</h4>
        <span class="text-muted small">{{ $products->total() }} product(s)</span>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" name="search" class="form-control" placeholder="Search by name, brand or SKU..." value="{{ request('search') }}">
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
                    <select name="category" class="form-select">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All status</option>
                        <option value="active" @selected(request('status') == 'active')>Active</option>
                        <option value="inactive" @selected(request('status') == 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-brand w-100" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th>Seller</th>
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
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($product->image)
                                            <img src="{{ product_image_url($product->image) }}" class="avatar-sm" alt="">
                                        @endif
                                        <span class="small fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td class="small">{{ $product->seller->name }}</td>
                                <td class="small">{{ $product->category->name }}</td>
                                <td class="small">
                                    Rs. {{ number_format($product->sellingPrice(), 2) }}
                                    @if ($product->hasDiscount())
                                        <span class="old-price">Rs. {{ number_format($product->price, 2) }}</span>
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
                                    @if ($product->approval_status === 'pending')
                                        <form action="{{ route('admin.products.approve', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success" type="submit"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $product->id }}"><i class="bi bi-x-lg"></i></button>
                                    @endif
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-brand"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="Delete this product?"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$products" />
        </div>
    </div>

    {{-- Reject modals --}}
    @foreach ($products as $product)
        @if ($product->approval_status === 'pending')
            <div class="modal fade" id="rejectModal{{ $product->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.products.reject', $product->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Reject Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="small">Rejecting <strong>{{ $product->name }}</strong>.</p>
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
        @endif
    @endforeach
@endsection
