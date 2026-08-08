@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Order {{ $order->order_number }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </div>
    </div>

    @if ($order->isCancelled())
        <div class="alert alert-danger">
            <i class="bi bi-x-circle me-1"></i> This order was cancelled. {{ $order->cancellation_reason ? 'Reason: ' . $order->cancellation_reason : '' }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Order Items</span>
                    <a href="#" class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Print</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($item->product && $item->product->image)
                                                    <img src="{{ product_image_url($item->product->image) }}" class="avatar-sm" alt="">
                                                @endif
                                                <span class="small">{{ $item->product->name ?? 'Product #' . $item->product_id }}</span>
                                            </div>
                                        </td>
                                        <td class="small text-muted">{{ $item->seller->name ?? '—' }}</td>
                                        <td class="text-center small">{{ $item->quantity }}</td>
                                        <td class="text-center small">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-end small fw-semibold">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-end small fw-semibold">Total</td>
                                    <td class="text-end fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Customer</div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->customer->name ?? $order->customer_name }}</strong></p>
                    @if ($order->customer)
                        <p class="mb-1 small text-muted">{{ $order->customer->email }}</p>
                    @endif
                    <p class="mb-0 small"><strong>Phone:</strong> {{ $order->phone ?? '—' }}</p>
                    <p class="mb-0 small"><strong>Shipping Address:</strong><br>{{ $order->shipping_address }}</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Payment</div>
                <div class="card-body">
                    <p class="mb-1 small"><strong>Status:</strong>
                        <span class="badge {{ match($order->payment_status) { 'paid' => 'bg-success', 'refunded' => 'bg-info text-dark', 'failed' => 'bg-danger', default => 'bg-warning text-dark' } }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                    @if ($order->payment)
                        <p class="mb-1 small"><strong>Method:</strong> {{ ucfirst($order->payment->payment_method) }}</p>
                        <p class="mb-1 small"><strong>Transaction:</strong> {{ $order->payment->transaction_id ?? '—' }}</p>
                        <p class="mb-0 small"><strong>Paid At:</strong> {{ $order->payment->paid_at?->format('M d, Y H:i') ?? '—' }}</p>
                    @else
                        <p class="mb-0 small text-muted">No payment record.</p>
                    @endif

                    @if (!$order->isCancelled())
                        <form action="{{ route('admin.orders.payment', $order->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="input-group input-group-sm">
                                <select name="payment_status" class="form-select">
                                    @foreach (['pending', 'paid', 'refunded', 'failed'] as $payment)
                                        <option value="{{ $payment }}" @selected($order->payment_status === $payment)>{{ ucfirst($payment) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-brand" type="submit">Update</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Update Order Status</div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                        @csrf
                        <div class="input-group input-group-sm mb-2">
                            <select name="order_status" class="form-select">
                                @foreach (App\Models\Order::ORDER_STATUSES as $status)
                                    <option value="{{ $status }}" @selected($order->order_status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-brand" type="submit">Update</button>
                        </div>
                    </form>

                    @if ($order->canCancel())
                        <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Cancel this order? Stock will be restored.')">
                            @csrf
                            <div class="input-group input-group-sm">
                                <input type="text" name="reason" class="form-control" placeholder="Cancellation reason (optional)">
                                <button class="btn btn-outline-danger" type="submit"><i class="bi bi-x-lg me-1"></i> Cancel Order</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
