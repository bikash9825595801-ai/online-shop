@extends('layouts.seller')

@section('title', 'Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Orders</h4>
        <span class="text-muted small">{{ $orders->total() }} order(s)</span>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('seller.orders.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-7">
                    <input type="search" name="search" class="form-control" placeholder="Search by order number..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (App\Models\Order::ORDER_STATUSES as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                        @endforeach
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
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Your Items</th>
                            <th>Your Total</th>
                            <th>Status</th>
                            <th>Placed</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="small fw-semibold">{{ $order->order_number }}</td>
                                <td class="small">{{ $order->customer->name ?? $order->customer_name }}</td>
                                <td class="small">{{ $order->items->sum('quantity') }}</td>
                                <td class="small">Rs. {{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                <td><span class="badge {{ order_status_badge($order->order_status) }}">{{ ucfirst($order->order_status) }}</span></td>
                                <td class="small text-muted">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('seller.orders.show', $order->id) }}" class="btn btn-sm btn-outline-brand"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$orders" />
        </div>
    </div>
@endsection
