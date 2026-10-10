@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Orders</h4>
        <span class="text-muted small">{{ $orders->total() }} order(s)</span>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" name="search" class="form-control" placeholder="Search order #, name, phone, email, city..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All order statuses</option>
                        @foreach (App\Models\Order::ORDER_STATUSES as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="payment" class="form-select">
                        <option value="">All payment statuses</option>
                        @foreach (['pending', 'paid', 'refunded', 'failed'] as $payment)
                            <option value="{{ $payment }}" @selected(request('payment') == $payment)>{{ ucfirst($payment) }}</option>
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
                            <th>Items</th>
                            <th>Total</th>
                            <th>Order Status</th>
                            <th>Payment</th>
                            <th>Placed</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="small fw-semibold">{{ $order->order_number }}</td>
                                <td class="small">
                                    <div class="fw-semibold">{{ $order->customerName() }}</div>
                                    <div class="text-muted" style="font-size:.72rem">{{ $order->customerEmail() }}</div>
                                    <div class="text-muted" style="font-size:.72rem">{{ $order->phone ?? '—' }}</div>
                                </td>
                                <td class="small">{{ $order->items_count ?? $order->items->sum('quantity') }}</td>
                                <td class="small">Rs. {{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ match($order->order_status) { 'delivered' => 'bg-success', 'cancelled' => 'bg-danger', 'pending' => 'bg-warning text-dark', 'shipped' => 'bg-info text-dark', default => 'bg-secondary' } }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ match($order->payment_status) { 'paid' => 'bg-success', 'refunded' => 'bg-info text-dark', 'failed' => 'bg-danger', default => 'bg-warning text-dark' } }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-brand"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$orders" />
        </div>
    </div>
@endsection
