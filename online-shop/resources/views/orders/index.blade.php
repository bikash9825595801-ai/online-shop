@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">My Orders</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="section-title mb-0">My Orders</h4>
            <a href="{{ route('products.index') }}" class="btn btn-brand btn-sm">Continue Shopping</a>
        </div>

        @forelse ($orders as $order)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none fw-semibold">{{ $order->order_number }}</a>
                            <small class="text-muted d-block">Placed {{ $order->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ order_status_badge($order->order_status) }}">{{ ucfirst($order->order_status) }}</span>
                            <span class="badge {{ payment_status_badge($order->payment_status) }}">{{ ucfirst($order->payment_status) }}</span>
                        </div>
                    </div>

                    <div class="row g-2">
                        @foreach ($order->items as $item)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center gap-2">
                                    @if ($item->product->image)
                                        <img src="{{ product_image_url($item->product->image) }}" style="width:40px;height:40px;object-fit:cover;border-radius:.4rem" alt="">
                                    @endif
                                    <div class="small">
                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                        <span class="text-muted">Qty: {{ $item->quantity }} &times; Rs. {{ number_format($item->price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total: <span class="text-brand">Rs. {{ number_format($order->total_amount, 2) }}</span></strong>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-brand btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-receipt fs-1 text-muted"></i>
                    <h5 class="mt-3">No orders yet</h5>
                    <p class="text-muted">When you place an order, it will appear here.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-brand">Start Shopping</a>
                </div>
            </div>
        @endforelse

        <div class="mt-4">
            <x-pagination :paginator="$orders" />
        </div>
    </div>
@endsection
