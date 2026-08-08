@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active">Cart</li>
            </ol>
        </nav>

        <h4 class="section-title mb-4">Shopping Cart</h4>

        @if ($cart->items->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-cart-x fs-1 text-muted"></i>
                    <h5 class="mt-3">Your cart is empty</h5>
                    <p class="text-muted">Browse products and add something you love.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-brand">Start Shopping</a>
                </div>
            </div>
        @else
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-end">Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cart->items as $item)
                                            <tr>
                                                <td style="min-width:220px">
                                                    <div class="d-flex align-items-center gap-3">
                                                        @if ($item->product->image)
                                                            <img src="{{ product_image_url($item->product->image) }}" style="width:56px;height:56px;object-fit:cover;border-radius:.5rem" alt="">
                                                        @endif
                                                        <div>
                                                            <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-dark small fw-semibold">{{ $item->product->name }}</a>
                                                            <div class="text-muted" style="font-size:.75rem">
                                                                @if ($item->product->stock < $item->quantity)
                                                                    <span class="text-danger">Only {{ $item->product->stock }} in stock!</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">${{ number_format($item->product->sellingPrice(), 2) }}</td>
                                                <td class="text-center" style="width:130px">
                                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline-flex align-items-center gap-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-dec" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm text-center" style="width:60px">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-inc" {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>+</button>
                                                    </form>
                                                </td>
                                                <td class="text-end fw-semibold">${{ number_format($item->lineTotal(), 2) }}</td>
                                                <td class="text-end">
                                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove" data-confirm="Remove this item from your cart?">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">Order Summary</div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <strong>${{ number_format($cart->subtotal(), 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Shipping</span>
                                <span class="text-success fw-semibold">Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-semibold">Total</span>
                                <span class="fw-bold fs-5 text-brand">${{ number_format($cart->total(), 2) }}</span>
                            </div>
                            <a href="{{ route('checkout.index') }}" class="btn btn-brand w-100">
                                <i class="bi bi-credit-card me-1"></i> Proceed to Checkout
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
