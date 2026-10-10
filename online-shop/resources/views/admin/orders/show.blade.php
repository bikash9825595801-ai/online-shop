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
                                        <td class="text-center small">Rs. {{ number_format($item->price, 2) }}</td>
                                        <td class="text-end small fw-semibold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="4" class="text-end small fw-semibold">Total</td>
                                    <td class="text-end fw-bold">Rs. {{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- ============ FULL CUSTOMER DETAIL ============ --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person-vcard me-1"></i> Customer Details</span>
                    @if ($order->customer)
                        <a href="{{ route('admin.customers.show', $order->customer->id) }}" class="btn btn-sm btn-outline-brand" title="View full profile">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    {{-- Identity --}}
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div class="bg-soft-brand text-brand rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.2rem;font-weight:700">
                            {{ strtoupper(substr($order->customerName(), 0, 1)) }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <strong class="d-block text-truncate">{{ $order->customerName() }}</strong>
                            <small class="text-muted d-block text-truncate">{{ $order->customerEmail() }}</small>
                        </div>
                        @if ($order->customer)
                            <span class="badge {{ $order->customer->isActive() ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($order->customer->status) }}
                            </span>
                        @else
                            <span class="badge bg-secondary">Deleted</span>
                        @endif
                    </div>

                    @if ($order->customer && $order->customer->name !== $order->customer_name)
                        <div class="alert alert-secondary py-2 px-3 small mb-3">
                            <i class="bi bi-person me-1"></i><strong>Account name:</strong> {{ $order->customer->name }}
                        </div>
                    @endif

                    {{-- Contact --}}
                    <h6 class="text-uppercase text-muted fs-6 mb-2" style="letter-spacing:.05em">Contact</h6>
                    <p class="mb-1 small"><i class="bi bi-telephone text-brand me-2"></i>{{ $order->phone ?? '—' }}</p>
                    <p class="mb-1 small"><i class="bi bi-envelope text-brand me-2"></i>{{ $order->customerEmail() }}</p>
                    @if ($order->customer?->phone && $order->customer->phone !== $order->phone)
                        <p class="mb-3 small text-muted"><i class="bi bi-person me-2"></i>Account phone: {{ $order->customer->phone }}</p>
                    @else
                        <div class="mb-3"></div>
                    @endif

                    {{-- Address --}}
                    <h6 class="text-uppercase text-muted fs-6 mb-2" style="letter-spacing:.05em">Shipping Address</h6>
                    <p class="mb-1 small">{{ $order->shipping_address }}</p>
                    <div class="d-flex flex-wrap gap-1 mb-3">
                        @if ($order->city)<span class="badge bg-light text-dark border">{{ $order->city }}</span>@endif
                        @if ($order->state)<span class="badge bg-light text-dark border">{{ $order->state }}</span>@endif
                        @if ($order->postal_code)<span class="badge bg-light text-dark border">{{ $order->postal_code }}</span>@endif
                        @if ($order->country)<span class="badge bg-light text-dark border">{{ $order->country }}</span>@endif
                    </div>

                    @if ($order->customer?->address && $order->customer->address !== $order->shipping_address)
                        <div class="alert alert-secondary py-2 px-3 small mb-3">
                            <strong>Account address:</strong> {{ $order->customer->address }}
                        </div>
                    @endif

                    @if ($order->notes)
                        <h6 class="text-uppercase text-muted fs-6 mb-2" style="letter-spacing:.05em">Customer Notes</h6>
                        <div class="alert alert-warning py-2 px-3 small mb-3">
                            <i class="bi bi-sticky me-1"></i>{{ $order->notes }}
                        </div>
                    @endif

                    {{-- Account stats --}}
                    @if ($customerStats)
                        <h6 class="text-uppercase text-muted fs-6 mb-2" style="letter-spacing:.05em">Account Summary</h6>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <div class="fw-bold text-brand">{{ $customerStats['total_orders'] }}</div>
                                    <small class="text-muted" style="font-size:.7rem">Orders</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <div class="fw-bold text-success">Rs. {{ number_format($customerStats['total_spent'], 0) }}</div>
                                    <small class="text-muted" style="font-size:.7rem">Spent</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 text-center">
                                    <div class="fw-bold text-warning">{{ $customerStats['pending_orders'] }}</div>
                                    <small class="text-muted" style="font-size:.7rem">Pending</small>
                                </div>
                            </div>
                        </div>

                        <p class="mb-1 small text-muted"><i class="bi bi-calendar3 me-1"></i> Member since: {{ $customerStats['joined']->format('M d, Y') }}</p>
                        <p class="mb-0 small text-muted"><i class="bi bi-clock-history me-1"></i> Last order: {{ $customerStats['last_order']?->created_at->format('M d, Y') ?? '—' }}</p>
                    @endif
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
