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
                        @foreach (['cod', 'credit_card', 'paypal', 'bank_transfer'] as $method)
                            <option value="{{ $method }}" @selected(request('method') == $method)>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="small fw-semibold">{{ $payment->order?->order_number ?? '#' . $payment->order_id }}</td>
                                <td class="small">{{ $payment->order?->customer?->name ?? '—' }}</td>
                                <td class="small">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td class="small text-muted">{{ $payment->transaction_id ?? '—' }}</td>
                                <td class="small">Rs. {{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ match($payment->status) { 'paid' => 'bg-success', 'refunded' => 'bg-info text-dark', 'failed' => 'bg-danger', default => 'bg-warning text-dark' } }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $payment->paid_at?->format('M d, Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No payments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$payments" />
        </div>
    </div>
@endsection
