@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none">Cart</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>

        <h4 class="section-title mb-4">Checkout</h4>

        @if (session('checkout_error'))
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i> {{ session('checkout_error') }}
            </div>
        @endif

        @if (! empty($unavailable))
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>
                These items are no longer available and were removed from your cart:
                <strong>{{ implode(', ', $unavailable) }}</strong>.
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-header"><i class="bi bi-person me-1"></i> Customer Information</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', auth()->user()->phone) }}" placeholder="98XXXXXXXX" required>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header"><i class="bi bi-geo-alt me-1"></i> Shipping Address</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Street address <span class="text-danger">*</span></label>
                                    <textarea name="shipping_address" rows="2" class="form-control @error('shipping_address') is-invalid @enderror" placeholder="Tole, Ward no, Building, street..." required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                                    @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">City / District <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" placeholder="Kathmandu" required>
                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State / Province <span class="text-danger">*</span></label>
                                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}" placeholder="Bagmati" required>
                                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Postal code</label>
                                    <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code') }}" placeholder="44600">
                                    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Country <span class="text-danger">*</span></label>
                                    <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', 'Nepal') }}" required>
                                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Order notes <span class="text-muted">(optional)</span></label>
                                    <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" placeholder="Delivery instructions, landmark, best time to call...">{{ old('notes') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header"><i class="bi bi-credit-card me-1"></i> Payment Method</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input class="btn-check" type="radio" name="payment_method" id="pm-cod" value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                    <label class="pay-option w-100" for="pm-cod">
                                        <span class="pay-option-icon text-success"><i class="bi bi-cash-coin"></i></span>
                                        <span class="pay-option-body">
                                            <strong>Cash on Delivery</strong>
                                            <small>Pay in cash when your order arrives.</small>
                                        </span>
                                        <span class="pay-option-check"><i class="bi bi-check-circle-fill"></i></span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input class="btn-check" type="radio" name="payment_method" id="pm-esewa" value="esewa" {{ old('payment_method') === 'esewa' ? 'checked' : '' }}>
                                    <label class="pay-option w-100" for="pm-esewa">
                                        <span class="pay-option-icon text-success"><i class="bi bi-wallet2"></i></span>
                                        <span class="pay-option-body">
                                            <strong>eSewa</strong>
                                            <small>Pay securely with your eSewa wallet.</small>
                                        </span>
                                        <span class="pay-option-check"><i class="bi bi-check-circle-fill"></i></span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input class="btn-check" type="radio" name="payment_method" id="pm-khalti" value="khalti" {{ old('payment_method') === 'khalti' ? 'checked' : '' }}>
                                    <label class="pay-option w-100" for="pm-khalti">
                                        <span class="pay-option-icon" style="color:#5c2d91"><i class="bi bi-piggy-bank"></i></span>
                                        <span class="pay-option-body">
                                            <strong>Khalti</strong>
                                            <small>Pay with Khalti wallet, e-banking &amp; more.</small>
                                        </span>
                                        <span class="pay-option-check"><i class="bi bi-check-circle-fill"></i></span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input class="btn-check" type="radio" name="payment_method" id="pm-card" value="card" {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                                    <label class="pay-option w-100" for="pm-card">
                                        <span class="pay-option-icon text-primary"><i class="bi bi-credit-card-2-front"></i></span>
                                        <span class="pay-option-body">
                                            <strong>Credit / Debit Card</strong>
                                            <small>Visa &amp; Mastercard via secure gateway.</small>
                                        </span>
                                        <span class="pay-option-check"><i class="bi bi-check-circle-fill"></i></span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input class="btn-check" type="radio" name="payment_method" id="pm-bank" value="bank" {{ old('payment_method') === 'bank' ? 'checked' : '' }}>
                                    <label class="pay-option w-100" for="pm-bank">
                                        <span class="pay-option-icon text-warning"><i class="bi bi-bank"></i></span>
                                        <span class="pay-option-body">
                                            <strong>Bank Transfer</strong>
                                            <small>Transfer &amp; upload your deposit receipt.</small>
                                        </span>
                                        <span class="pay-option-check"><i class="bi bi-check-circle-fill"></i></span>
                                    </label>
                                </div>
                            </div>
                            @error('payment_method')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                            <div id="pm-hint" class="alert alert-info small mb-0 mt-3 d-none"></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand btn-lg w-100">
                        <i class="bi bi-check-circle me-1"></i> Place Order
                    </button>
                </form>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">Order Summary</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    @foreach ($cart->items as $item)
                                        <tr>
                                            <td class="ps-3 py-2">
                                                <span class="small fw-semibold">{{ $item->product->name }}</span>
                                                <span class="text-muted d-block small">Qty: {{ $item->quantity }}</span>
                                            </td>
                                            <td class="text-end pe-3 py-2 small">Rs. {{ number_format($item->lineTotal(), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 border-top">
                            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Subtotal</span><strong>Rs. {{ number_format($cart->subtotal(), 2) }}</strong></div>
                            <div class="d-flex justify-content-between mb-1"><span class="text-muted small">Shipping</span><span class="text-success small">Free</span></div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Total Payable</span>
                                <span class="fw-bold fs-5 text-brand">Rs. {{ number_format($cart->total(), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var radios = document.querySelectorAll('input[name="payment_method"]');
            var hint = document.getElementById('pm-hint');
            var messages = {
                cod: 'You will pay in cash when your order is delivered.',
                esewa: 'After placing the order, pay with your eSewa wallet, or scan the eSewa QR code.',
                khalti: 'You will be redirected to Khalti to complete the payment.',
                card: 'Card payment is processed through the secure Khalti gateway.',
                bank: 'After ordering, transfer the amount and upload your deposit receipt.'
            };
            function update() {
                var checked = document.querySelector('input[name="payment_method"]:checked');
                if (hint && checked && messages[checked.value]) {
                    hint.textContent = messages[checked.value];
                    hint.classList.remove('d-none');
                }
            }
            radios.forEach(function (radio) { radio.addEventListener('change', update); });
            update();
        });
    </script>
@endpush
