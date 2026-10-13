@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Payments</h4>
        <span class="text-muted small">{{ $payments->total() }} payment(s)</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Collected (Paid)</div>
                    <h5 class="mb-0 text-success">Rs. {{ number_format($totalCollected, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Pending</div>
                    <h5 class="mb-0 text-warning">Rs. {{ number_format($totalPending, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-5">
                    <input type="search" name="search" class="form-control" placeholder="Search by order number or transaction ID..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'paid', 'failed', 'refunded'] as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="method" class="form-select">
                        <option value="">All methods</option>
                        @foreach (['cod' => 'Cash on Delivery', 'esewa' => 'eSewa', 'khalti' => 'Khalti', 'card' => 'Card', 'bank' => 'Bank Transfer'] as $key => $label)
                            <option value="{{ $key }}" @selected(request('method') == $key)>{{ $label }}</option>
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
                            <th>Method</th>
                            <th>Transaction</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid At</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="small fw-semibold">{{ $payment->order?->order_number ?? '#' . $payment->order_id }}</td>
                                <td class="small">{{ $payment->order?->customer?->name ?? '—' }}</td>
                                <td class="small">
                                    {{ $payment->methodLabel() }}
                                    @if ($payment->proof_path)
                                        <span class="badge bg-info text-dark ms-1" title="Receipt uploaded">receipt</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $payment->transaction_id ?? '—' }}</td>
                                <td class="small">Rs. {{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ match($payment->status) { 'paid' => 'bg-success', 'refunded' => 'bg-info text-dark', 'failed' => 'bg-danger', default => 'bg-warning text-dark' } }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $payment->paid_at?->format('M d, Y H:i') ?? '—' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-brand" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $payment->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No payments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$payments" />
        </div>
    </div>

    @foreach ($payments as $payment)
        <div class="modal fade" id="paymentModal{{ $payment->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payment — {{ $payment->order?->order_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <table class="table table-sm mb-0">
                                    <tr><th class="text-muted">Customer</th><td>{{ $payment->order?->customer?->name ?? '—' }}</td></tr>
                                    <tr><th class="text-muted">Method</th><td>{{ $payment->methodLabel() }}</td></tr>
                                    <tr><th class="text-muted">Amount</th><td>Rs. {{ number_format($payment->amount, 2) }}</td></tr>
                                    <tr><th class="text-muted">Status</th><td>
                                        <span class="badge {{ match($payment->status) { 'paid' => 'bg-success', 'refunded' => 'bg-info text-dark', 'failed' => 'bg-danger', default => 'bg-warning text-dark' } }}">{{ ucfirst($payment->status) }}</span>
                                    </td></tr>
                                    <tr><th class="text-muted">Transaction</th><td>{{ $payment->transaction_id ?? '—' }}</td></tr>
                                    @if ($payment->meta['bank_reference'] ?? null)
                                        <tr><th class="text-muted">Bank Ref.</th><td>{{ $payment->meta['bank_reference'] }}</td></tr>
                                    @endif
                                    @if ($payment->meta['pidx'] ?? null)
                                        <tr><th class="text-muted">Khalti pidx</th><td class="text-break small">{{ $payment->meta['pidx'] }}</td></tr>
                                    @endif
                                    @if ($payment->paid_at)
                                        <tr><th class="text-muted">Paid at</th><td>{{ $payment->paid_at->format('M d, Y H:i') }}</td></tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if ($payment->proof_path)
                                    <div class="text-muted small mb-1">Uploaded receipt</div>
                                    <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $payment->proof_path) }}" class="img-fluid rounded border" alt="Payment receipt">
                                    </a>
                                @else
                                    <div class="alert alert-light border text-muted small mb-0">No receipt uploaded for this payment.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        @if ($payment->status !== 'paid')
                            <form action="{{ route('admin.orders.payment', $payment->order_id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="payment_status" value="paid">
                                <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i> Mark as Paid</button>
                            </form>
                        @endif
                        @if ($payment->status !== 'failed')
                            <form action="{{ route('admin.orders.payment', $payment->order_id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="payment_status" value="failed">
                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i> Mark as Failed</button>
                            </form>
                        @endif
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
