@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active">Wishlist</li>
            </ol>
        </nav>

        <h4 class="section-title mb-4">My Wishlist</h4>

        @if ($wishlist->items->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-heart fs-1 text-muted"></i>
                    <h5 class="mt-3">Your wishlist is empty</h5>
                    <p class="text-muted">Save products you love to find them later.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-brand">Browse Products</a>
                </div>
            </div>
        @else
            <div class="row g-3">
                @foreach ($wishlist->items as $item)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card product-card h-100">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="d-block position-relative">
                                @if ($item->product->hasDiscount())
                                    <span class="discount-badge">-{{ $item->product->discountPercent() }}%</span>
                                @endif
                                @if ($item->product->image)
                                    <img src="{{ product_image_url($item->product->image) }}" class="product-image w-100" alt="">
                                @else
                                    <div class="product-image w-100 d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-image text-muted" style="font-size:3rem"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="card-body d-flex flex-column">
                                <a href="{{ route('products.show', $item->product->slug) }}" class="text-decoration-none text-dark">
                                    <h6 class="fw-semibold mb-1">{{ $item->product->name }}</h6>
                                </a>
                                <div class="mb-2">
                                    <span class="fw-bold text-brand">${{ number_format($item->product->sellingPrice(), 2) }}</span>
                                    @if ($item->product->hasDiscount())
                                        <span class="old-price">${{ number_format($item->product->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="mt-auto d-flex flex-column gap-1">
                                    @if ($item->product->stock > 0)
                                        <form action="{{ route('wishlist.move-to-cart', $item->product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-brand btn-sm w-100">
                                                <i class="bi bi-cart-plus me-1"></i> Move to Cart
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-secondary btn-sm w-100" disabled>Out of Stock</button>
                                    @endif
                                    <form action="{{ route('wishlist.destroy', $item->product->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" data-confirm="Remove this product from your wishlist?">
                                            <i class="bi bi-trash me-1"></i> Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
