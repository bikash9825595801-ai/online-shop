@extends('layouts.customer')

@section('title', 'My Dashboard')

@section('content')
    <h4 class="mb-4">Welcome back, {{ $user->name }}!</h4>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $totalOrders }}</div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <i class="bi bi-receipt stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-orange">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $pendingOrders }}</div>
                        <div class="stat-label">Active Orders</div>
                    </div>
                    <i class="bi bi-hourglass-split stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">${{ number_format($totalSpent, 2) }}</div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                    <i class="bi bi-cash-stack stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $wishlistCount }}</div>
                        <div class="stat-label">Wishlist Items</div>
                    </div>
                    <i class="bi bi-heart stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Orders</span>
                    <a href="{{ route('orders.index') }}" class="small text-decoration-none">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td class="small fw-semibold">{{ $order->order_number }}</td>
                                        <td class="small">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td class="small">${{ number_format($order->total_amount, 2) }}</td>
                                        <td><span class="badge {{ order_status_badge($order->order_status) }}">{{ ucfirst($order->order_status) }}</span></td>
                                        <td><a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-brand">View</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">Quick Actions</div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-brand btn-sm">
                        <i class="bi bi-cart3 me-1"></i> My Cart ({{ $cartCount }} items)
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="btn btn-outline-brand btn-sm">
                        <i class="bi bi-heart me-1"></i> My Wishlist ({{ $wishlistCount }})
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-brand btn-sm">
                        <i class="bi bi-bag me-1"></i> Continue Shopping
                    </a>
                    <a href="{{ route('support.create') }}" class="btn btn-outline-brand btn-sm">
                        <i class="bi bi-life-preserver me-1"></i> Need Help?
                    </a>
                </div>
            </div>

            @if ($reviewable->isNotEmpty())
                <div class="card">
                    <div class="card-header">Products to Review</div>
                    <div class="card-body">
                        @foreach ($reviewable->take(4) as $product)
                            <div class="d-flex align-items-center gap-2 mb-2">
                                @if ($product->image)
                                    <img src="{{ product_image_url($product->image) }}" style="width:40px;height:40px;object-fit:cover;border-radius:.4rem" alt="">
                                @endif
                                <div class="small flex-grow-1">
                                    <div class="fw-semibold text-truncate">{{ $product->name }}</div>
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none small">Review it</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
