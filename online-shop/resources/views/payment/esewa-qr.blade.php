@extends('layouts.app')

@section('title', 'Pay with eSewa QR')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}" class="text-decoration-none">My Orders</a></li>
                        <li class="breadcrumb-item active">Pay with eSewa</li>
                    </ol>
                </nav>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <span class="badge text-bg-success fs-6 mb-2"><i class="bi bi-wallet2 me-1"></i> eSewa Payment</span>
                            <h4 class="mb-0">Scan &amp; Pay with eSewa</h4>
                            <p class="text-muted">Order <strong>{{ $order->order_number }}</strong></p>
                        </div>

                        <div class="alert alert-light border d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Amount to pay</span>
                            <span class="fw-bold fs-5 text-brand">Rs. {{ number_format($order->total_amount, 2) }}</span>
                        </div>

                        <form action="{{ $esewaFormUrl }}" method="POST" target="_blank" class="mt-1">
                            @foreach ($fields as $name => $value)
                                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                            @endforeach
                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="bi bi-wallet2 me-1"></i> Pay with eSewa Wallet (opens eSewa website)
                            </button>
                        </form>

                        <div class="d-flex align-items-center gap-2 my-3">
                            <hr class="flex-grow-1">
                            <span class="text-muted small">or pay by scanning the QR</span>
                            <hr class="flex-grow-1">
                        </div>

                        <div class="text-center">
                            <div class="d-inline-block p-3 border rounded-4 bg-white shadow-sm">
                                <img src="{{ asset('images/esewa-qr.png') }}"
                                     alt="eSewa QR code"
                                     style="width:240px;height:240px;object-fit:contain"
                                     class="img-fluid">
                            </div>
                            <p class="text-muted small mt-3 mb-0">
                                Scan this QR code with your <strong>eSewa app</strong>, enter the exact amount and make the payment.
                            </p>
                        </div>

                        <div class="alert alert-info d-flex align-items-start gap-2 mt-4 mb-0">
                            <i class="bi bi-info-circle flex-shrink-0 mt-1"></i>
                            <div class="small">
                                <strong>How it works:</strong>
                                <ol class="mb-0 ps-3 mt-1">
                                    <li>Open the eSewa app and scan the QR code above.</li>
                                    <li>Enter amount <strong>Rs. {{ number_format($order->total_amount, 2) }}</strong> and complete the payment.</li>
                                    <li>Copy the <strong>transaction code</strong> from your eSewa history and enter it below.</li>
                                    <li>Our team verifies and confirms your order.</li>
                                </ol>
                            </div>
                        </div>

                        @if ($order->payment?->transaction_id)
                            <div class="alert alert-success mt-4 mb-0">
                                <i class="bi bi-check-circle me-1"></i>
                                Transaction code <strong>{{ $order->payment->transaction_id }}</strong> already submitted. It is awaiting verification.
                            </div>
                        @endif

                        <form action="{{ route('payment.esewa.qr', $order->id) }}" method="POST" class="mt-4">
                            @csrf
                            <label class="form-label">eSewa transaction code <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                <input type="text" name="transaction_code" required class="form-control @error('transaction_code') is-invalid @enderror"
                                       placeholder="e.g. 0x1A2B3C..." value="{{ old('transaction_code', $order->payment?->transaction_id) }}">
                                @error('transaction_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100 mt-3"
                                    @if ($order->payment?->transaction_id) disabled @endif>
                                <i class="bi bi-check2-circle me-1"></i>
                                {{ $order->payment?->transaction_id ? 'Waiting for verification' : 'Submit Transaction Code' }}
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