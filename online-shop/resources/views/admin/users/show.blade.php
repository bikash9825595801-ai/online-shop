@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">User Details</h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    @if ($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover" alt="">
                    @else
                        <i class="bi bi-person-circle text-muted" style="font-size:5rem"></i>
                    @endif
                    <h5 class="fw-bold">{{ $user->name }}</h5>
                    <p class="text-muted mb-1">{{ $user->email }}</p>
                    <p class="text-muted mb-1">{{ $user->phone }}</p>
                    <p class="text-muted small mb-2">{{ $user->address }}</p>
                    <span class="badge {{ $user->isAdmin() ? 'bg-danger' : ($user->isSeller() ? 'bg-primary' : 'bg-success') }}">{{ ucfirst($user->role) }}</span>
                    <span class="badge {{ $user->isActive() ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($user->status) }}</span>
                    <div class="small text-muted mt-2">Joined {{ $user->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">Orders</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($user->orders()->latest()->limit(10)->get() as $order)
                                    <tr>
                                        <td class="small fw-semibold">{{ $order->order_number }}</td>
                                        <td class="small">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="small">${{ number_format($order->total_amount, 2) }}</td>
                                        <td><span class="badge {{ order_status_badge($order->order_status) }}">{{ ucfirst($order->order_status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No orders.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($user->isSeller())
                <div class="card">
                    <div class="card-header">Products</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Approval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($user->products()->latest()->limit(10)->get() as $product)
                                        <tr>
                                            <td class="small">{{ $product->name }}</td>
                                            <td class="small">${{ number_format($product->sellingPrice(), 2) }}</td>
                                            <td class="small">{{ $product->stock }}</td>
                                            <td>
                                                <span class="badge {{ match($product->approval_status) { 'approved' => 'bg-success', 'pending' => 'bg-warning text-dark', 'rejected' => 'bg-danger', default => 'bg-secondary' } }}">
                                                    {{ ucfirst($product->approval_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">No products.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
