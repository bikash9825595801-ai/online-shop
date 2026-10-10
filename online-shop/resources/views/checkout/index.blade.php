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
                            <div class="form-check border rounded p-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="bi bi-cash-coin text-success me-1"></i>
                                    <strong>Cash on Delivery</strong>
                                    <small class="text-muted d-block">Pay in cash when your order arrives.</small>
                                </label>
                            </div>
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
