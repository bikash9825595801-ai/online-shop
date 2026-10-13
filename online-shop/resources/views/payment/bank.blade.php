@extends('layouts.app')

@section('title', 'Bank Transfer Payment')

@section('content')
    @php $bank = config('payment.bank'); @endphp

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">My Orders</a></li>
                        <li class="breadcrumb-item active">Bank Transfer</li>
                    </ol>
                </nav>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-1"><i class="bi bi-bank text-warning me-1"></i> Bank Transfer</h4>
                        <p class="text-muted">Transfer the amount below to our bank account, then upload the deposit receipt.</p>

                        <div class="alert alert-light border d-flex justify-content-between align-items-center">
                            <span>Amount to transfer</span>
                            <span class="fw-bold fs-5 text-brand">Rs. {{ number_format($order->total_amount, 2) }}</span>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle mb-0">
                                <tbody>
                                    <tr><th class="w-50 bg-light">Bank Name</th><td>{{ $bank['bank_name'] }}</td></tr>
                                    <tr><th class="bg-light">Account Name</th><td>{{ $bank['account_name'] }}</td></tr>
                                    <tr><th class="bg-light">Account Number</th><td class="fw-semibold">{{ $bank['account_number'] }}</td></tr>
                                    <tr><th class="bg-light">Branch</th><td>{{ $bank['branch'] }}</td></tr>
                                    <tr><th class="bg-light">Reference (Order No.)</th><td class="fw-semibold text-brand">{{ $order->order_number }}</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle me-1"></i>
                            Please use your order number <strong>{{ $order->order_number }}</strong> as the transfer reference so we can match your payment quickly.
                        </div>

                        @if ($order->payment?->proof_path)
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Receipt already uploaded. It is awaiting verification.
                            </div>
                        @endif

                        <form action="{{ route('payment.bank.proof', $order->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Deposit receipt (image) <span class="text-danger">*</span></label>
                                <input type="file" name="proof" accept="image/*" class="form-control @error('proof') is-invalid @enderror" required>
                                @error('proof')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Transaction / reference code <span class="text-muted">(optional)</span></label>
                                <input type="text" name="reference" value="{{ old('reference', $order->payment?->transaction_id) }}" class="form-control @error('reference') is-invalid @enderror" placeholder="e.g. TXN12345678">
                                @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-brand btn-lg w-100">
                                <i class="bi bi-upload me-1"></i> Submit Payment Proof
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('orders.show', $order->id) }}" class="text-decoration-none small">
                                <i class="bi bi-arrow-left me-1"></i> Back to order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
