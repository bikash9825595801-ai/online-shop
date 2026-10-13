@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">My Orders</a></li>
                <li class="breadcrumb-item active">{{ $order->order_number }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <div>
                <h4 class="section-title mb-0">{{ $order->order_number }}</h4>
                <small class="text-muted">Placed {{ $order->created_at->format('M d, Y h:i A') }}</small>
            </div>
            <div>
                <span class="badge {{ order_status_badge($order->order_status) }} fs-6">{{ ucfirst($order->order_status) }}</span>
                <span class="badge {{ payment_status_badge($order->payment_status) }} fs-6">{{ ucfirst($order->payment_status) }}</span>
            </div>
        </div>

        @if ($order->canCancel())
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>
                    <strong><i class="bi bi-exclamation-triangle me-1"></i> Pending order</strong>
                    <div class="small">You can cancel this order while it is still {{ $order->order_status }}.</div>
                </div>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel Order</button>
            </div>
        @endif

        @if ($order->payment && ! $order->payment->isPaid() && in_array($order->payment->payment_method, \App\Models\Payment::ONLINE_METHODS) && ! $order->isCancelled())
            <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <strong><i class="bi bi-credit-card me-1"></i> Payment pending</strong>
                    <div class="small">Complete your {{ $order->payment->methodLabel() }} payment to confirm this order.</div>
                </div>
                <a href="{{ route('payment.initiate', $order->id) }}" class="btn btn-brand btn-sm">
                    <i class="bi bi-lock-fill me-1"></i> Pay Now
                </a>
            </div>
        @elseif ($order->payment && ! $order->payment->isPaid() && $order->payment->payment_method === \App\Models\Payment::METHOD_BANK && ! $order->isCancelled())
            <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <strong><i class="bi bi-bank me-1"></i> Bank transfer</strong>
                    <div class="small">{{ $order->payment->proof_path ? 'Your receipt is awaiting verification.' : 'Upload your bank deposit receipt to confirm this order.' }}</div>
                </div>
                <a href="{{ route('payment.initiate', $order->id) }}" class="btn btn-brand btn-sm">
                    <i class="bi bi-upload me-1"></i> {{ $order->payment->proof_path ? 'View / Re-upload' : 'Upload Receipt' }}
                </a>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header"><i class="bi bi-box-seam me-1"></i> Items</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if ($item->product->image)
                                                        <img src="{{ product_image_url($item->product->image) }}" style="width:44px;height:44px;object-fit:cover;border-radius:.4rem" alt="">
                                                    @endif
                                                    <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none small fw-semibold text-dark">{{ $item->product->name }}</a>
                                                </div>
                                            </td>
                                            <td class="text-center">Rs. {{ number_format($item->price, 2) }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end fw-semibold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-semibold">Total</td>
                                        <td class="text-end fw-bold text-brand">Rs. {{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Review section for delivered orders --}}
                @if ($order->isDelivered() && $reviewable->isNotEmpty())
                    <div class="card">
                        <div class="card-header"><i class="bi bi-star me-1"></i> Review Purchased Products</div>
                        <div class="card-body">
                            @foreach ($reviewable as $item)
                                <div class="border rounded p-3 mb-3">
                                    <strong class="small">{{ $item->product->name }}</strong>
                                    <form action="{{ route('reviews.store') }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                                        <div class="rating-input mb-2">
                                            <input type="hidden" name="rating" value="{{ $item->existing_review?->rating ?? 5 }}">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star-fill star {{ ($item->existing_review?->rating ?? 5) >= $i ? 'selected' : '' }}"></i>
                                            @endfor
                                        </div>

                                        <textarea name="comment" rows="2" class="form-control form-control-sm mb-2"
                                                  placeholder="Share your experience...">{{ $item->existing_review?->comment }}</textarea>

                                        <button type="submit" class="btn btn-brand btn-sm">
                                            {{ $item->existing_review ? 'Update Review' : 'Submit Review' }}
                                        </button>

                                        @if ($item->existing_review)
                                            <button type="submit" class="btn btn-outline-danger btn-sm" form="delete-review-{{ $item->existing_review->id }}">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </button>
                                        @endif
                                    </form>

                                    @if ($item->existing_review)
                                        <form id="delete-review-{{ $item->existing_review->id }}"
                                              action="{{ route('reviews.destroy', $item->existing_review->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                @if ($sellers->isNotEmpty())
                    <div class="card mb-3">
                        <div class="card-header"><i class="bi bi-shop me-1"></i> Sellers</div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach ($sellers as $seller)
                                    <li class="list-group-item d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-soft-brand text-brand d-flex align-items-center justify-content-center fw-bold flex-shrink-0">
                                            {{ strtoupper(substr($seller->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="small fw-semibold text-truncate">{{ $seller->name }}</div>
                                            <small class="text-muted">Verified Seller</small>
                                        </div>
                                        <form action="{{ route('messages.start', $seller->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-brand" title="Message seller">
                                                <i class="bi bi-chat-dots me-1"></i> Message
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="card mb-3">
                    <div class="card-header"><i class="bi bi-truck me-1"></i> Shipping Information</div>
                    <div class="card-body small">
                        <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                        <p class="mb-1"><i class="bi bi-telephone me-1"></i>{{ $order->phone }}</p>
                        @if ($order->customer_email)
                            <p class="mb-1"><i class="bi bi-envelope me-1"></i>{{ $order->customer_email }}</p>
                        @endif
                        <p class="mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $order->shipping_address }}</p>
                        <div class="d-flex flex-wrap gap-1">
                            @if ($order->city)<span class="badge bg-light text-dark border">{{ $order->city }}</span>@endif
                            @if ($order->state)<span class="badge bg-light text-dark border">{{ $order->state }}</span>@endif
                            @if ($order->postal_code)<span class="badge bg-light text-dark border">{{ $order->postal_code }}</span>@endif
                            @if ($order->country)<span class="badge bg-light text-dark border">{{ $order->country }}</span>@endif
                        </div>
                        @if ($order->notes)
                            <hr class="my-2">
                            <p class="mb-0 text-muted"><i class="bi bi-sticky me-1"></i><strong>Notes:</strong> {{ $order->notes }}</p>
                        @endif
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><i class="bi bi-credit-card me-1"></i> Payment</div>
                    <div class="card-body small">
                        <p class="mb-1"><strong>Method:</strong> {{ $order->payment ? $order->payment->methodLabel() : '—' }}</p>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge {{ payment_status_badge($order->payment_status) }}">{{ ucfirst($order->payment_status) }}</span></p>
                        @if ($order->payment?->transaction_id)
                            <p class="mb-1"><strong>Transaction:</strong> {{ $order->payment->transaction_id }}</p>
                        @endif
                        @if ($order->payment?->paid_at)
                            <p class="mb-1"><strong>Paid at:</strong> {{ $order->payment->paid_at->format('M d, Y h:i A') }}</p>
                        @endif
                        @if ($order->payment && ! $order->payment->isPaid() && ! $order->isCancelled())
                            <a href="{{ route('payment.initiate', $order->id) }}" class="btn btn-brand btn-sm w-100 mt-2">
                                <i class="bi bi-lock-fill me-1"></i> Pay Now
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><i class="bi bi-hourglass-split me-1"></i> Tracking</div>
                    <div class="card-body">
                        <ul class="list-unstyled small mb-0">
                            @foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered'] as $step)
                                @php
                                    $done = array_search($step, \App\Models\Order::ORDER_STATUSES) <= array_search($order->order_status, \App\Models\Order::ORDER_STATUSES)
                                        && ! $order->isCancelled();
                                @endphp
                                <li class="mb-1">
                                    @if ($done)
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    @else
                                        <i class="bi bi-circle text-muted me-2"></i>
                                    @endif
                                    <span class="{{ $done ? '' : 'text-muted' }}">{{ ucfirst($step) }}</span>
                                </li>
                            @endforeach
                            @if ($order->isCancelled())
                                <li class="mb-1 text-danger"><i class="bi bi-x-circle-fill me-2"></i>Cancelled</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel modal --}}
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">Are you sure you want to cancel order <strong>{{ $order->order_number }}</strong>? Stock will be restored.</p>
                        <label class="form-label small">Reason (optional)</label>
                        <textarea name="reason" rows="2" class="form-control" placeholder="Why are you cancelling?"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Keep Order</button>
                        <button type="submit" class="btn btn-danger">Yes, Cancel Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
