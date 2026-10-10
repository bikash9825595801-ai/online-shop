@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Inventory</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.inventory.index', ['view' => 'all']) }}" class="btn btn-sm {{ $view === 'all' ? 'btn-brand' : 'btn-outline-brand' }}">All ({{ $totalProducts }})</a>
            <a href="{{ route('admin.inventory.index', ['view' => 'low']) }}" class="btn btn-sm {{ $view === 'low' ? 'btn-brand' : 'btn-outline-warning' }}">Low ({{ $lowStockCount }})</a>
            <a href="{{ route('admin.inventory.index', ['view' => 'out']) }}" class="btn btn-sm {{ $view === 'out' ? 'btn-brand' : 'btn-outline-danger' }}">Out ({{ $outOfStockCount }})</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Total Units</div>
                    <h5 class="mb-0">{{ number_format($totalUnits) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Stock Value</div>
                    <h5 class="mb-0">Rs. {{ number_format($totalStockValue, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Low Stock (≤ {{ $threshold }})</div>
                    <h5 class="mb-0 text-warning">{{ $lowStockCount }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Out of Stock</div>
                    <h5 class="mb-0 text-danger">{{ $outOfStockCount }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.inventory.index') }}" method="GET" class="row g-2 mb-3">
                        <input type="hidden" name="view" value="{{ $view }}">
                        <div class="col">
                            <input type="search" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-brand" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th class="text-end">Update Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr class="{{ $product->stock <= 0 ? 'table-danger' : ($product->stock <= $threshold ? 'table-warning' : '') }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($product->image)
                                                    <img src="{{ product_image_url($product->image) }}" class="avatar-sm" alt="">
                                                @endif
                                                <span class="small fw-semibold">{{ $product->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->stock <= 0 ? 'bg-danger' : ($product->stock <= $threshold ? 'bg-warning text-dark' : 'bg-success') }}">{{ $product->stock }}</span>
                                        </td>
                                        <td class="small">Rs. {{ number_format($product->stock * $product->sellingPrice(), 2) }}</td>
                                        <td class="small text-muted">{{ $product->category->name }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.inventory.stock', $product->id) }}" method="POST" class="d-flex justify-content-end gap-1">
                                                @csrf
                                                <input type="number" name="stock" class="form-control form-control-sm" style="width:90px" min="0" value="{{ $product->stock }}">
                                                <button class="btn btn-sm btn-outline-brand" type="submit"><i class="bi bi-check-lg"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No products found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <x-pagination :paginator="$products" />
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">High-Demand Products</div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @forelse ($highDemand as $item)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="small">{{ $item->name }}</span>
                                <span class="badge bg-soft-brand text-brand">{{ $item->sold }} sold</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No sales yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
