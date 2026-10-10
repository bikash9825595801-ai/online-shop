@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Customer Details</h4>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="stat-card stat-blue">
                <div class="stat-value">{{ $customer->orders_count ?? $customer->orders->count() }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card stat-green">
                <div class="stat-value">Rs. {{ number_format($totalSpent, 2) }}</div>
                <div class="stat-label">Total Spent</div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-card stat-orange">
                <div class="stat-value">{{ $customer->reviews->count() }}</div>
                <div class="stat-label">Reviews Written</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Customer Information</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Name:</strong> {{ $customer->name }}</div>
                <div class="col-md-4"><strong>Email:</strong> {{ $customer->email }}</div>
                <div class="col-md-4"><strong>Phone:</strong> {{ $customer->phone ?? '—' }}</div>
                <div class="col-md-12 mt-2"><strong>Address:</strong> {{ $customer->address ?? '—' }}</div>
                <div class="col-md-4 mt-2">
                    <strong>Status:</strong>
                    <span class="badge {{ $customer->isActive() ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($customer->status) }}</span>
                </div>
                <div class="col-md-4 mt-2"><strong>Joined:</strong> {{ $customer->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Order History</div>
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
                        @forelse ($customer->orders as $order)
                            <tr>
                                <td class="small fw-semibold"><a href="{{ route('admin.orders.show', $order->id) }}" class="text-decoration-none">{{ $order->order_number }}</a></td>
                                <td class="small">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="small">Rs. {{ number_format($order->total_amount, 2) }}</td>
                                <td><span class="badge {{ order_status_badge($order->order_status) }}">{{ ucfirst($order->order_status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
