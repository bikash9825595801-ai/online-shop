@extends('layouts.seller')

@section('title', 'Order ' . $order->order_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Order {{ $order->order_number }}</h4>
        <a href="{{ route('seller.orders.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    @if ($order->isCancelled())
        <div class="alert alert-danger">
            <i class="bi bi-x-circle me-1"></i> This order was cancelled. {{ $order->cancellation_reason ? 'Reason: ' . $order->cancellation_reason : '' }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Your Items in This Order</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
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
                                        <td class="text-center small">{{ $item->quantity }}</td>
                                        <td class="text-center small">Rs. {{ number_format($item->price, 2) }}</td>
                                        <td class="text-end small fw-semibold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end small fw-semibold">Your total</td>
                                    <td class="text-end fw-bold">Rs. {{ number_format($order->items->sum('subtotal'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Customer</span>
                    @if ($order->customer)
                        <form action="{{ route('messages.start', $order->customer->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-brand">
                                <i class="bi bi-chat-dots me-1"></i> Message Customer
                            </button>
                        </form>
                    @endif
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->customer->name ?? $order->customer_name }}</strong></p>
                    @if ($order->customer)
                        <p class="mb-1 small text-muted">{{ $order->customer->email }}</p>
                    @endif
                    <p class="mb-0 small"><strong>Phone:</strong> {{ $order->phone ?? '—' }}</p>
                    <p class="mb-0 small"><strong>Shipping Address:</strong><br>{{ $order->shipping_address }}</p>

                    @if ($conversation && $conversation->messages()->exists())
                        <a href="{{ route('messages.show', $conversation->id) }}" class="btn btn-brand btn-sm w-100 mt-3">
                            <i class="bi bi-chat-dots me-1"></i>
                            {{ $conversation->unreadCountFor(auth()->id()) > 0 ? 'View messages (' . $conversation->unreadCountFor(auth()->id()) . ' unread)' : 'Open conversation' }}
                        </a>
                    @endif
                </div>
            </div>

            @if ($order->notes)
                <div class="card mt-3 border-warning">
                    <div class="card-header bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-sticky me-1"></i> Customer Notes
                    </div>
                    <div class="card-body">
                        <p class="small mb-0" style="white-space:pre-wrap">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">Payment</div>
                <div class="card-body">
                    <span class="badge {{ payment_status_badge($order->payment_status) }}">{{ ucfirst($order->payment_status) }}</span>
                    @if ($order->payment)
                        <p class="small text-muted mt-2 mb-0">Method: {{ ucfirst($order->payment->payment_method) }}</p>
                        <p class="small text-muted mb-0">Transaction: {{ $order->payment->transaction_id ?? '—' }}</p>
                    @endif
                </div>
            </div>

            @if (!$order->isCancelled())
                <div class="card mt-3">
                    <div class="card-header">Update Order Status</div>
                    <div class="card-body">
                        <form action="{{ route('seller.orders.status', $order->id) }}" method="POST">
                            @csrf
                            <div class="input-group input-group-sm">
                                <select name="order_status" class="form-select">
                                    @foreach (App\Models\Order::ORDER_STATUSES as $status)
                                        <option value="{{ $status }}" @selected($order->order_status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-brand" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
