@extends('layouts.app')

@section('title', 'ShopEase — Online Shopping')

@section('content')
    <div class="container mt-4">
        {{-- Hero --}}
        <div class="hero p-4 p-md-5 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="fw-bold mb-2">Shop the best deals<br>right from home.</h1>
                <p class="mb-3 opacity-75">Discover thousands of products from trusted sellers with cash on delivery.</p>
                <form class="d-flex gap-2" action="{{ route('products.index') }}" method="GET" style="max-width:420px">
                    <input type="search" name="q" class="form-control" placeholder="Search anything..." value="{{ request('q') }}">
                    <button class="btn btn-light text-dark fw-semibold px-4" type="submit">Search</button>
                </form>
            </div>
            <div class="d-none d-md-block">
                <i class="bi bi-bag-heart-fill text-white-50" style="font-size:8rem"></i>
            </div>
        </div>

        {{-- Categories --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="section-title mb-0">Shop by Category</h5>
            <a href="{{ route('categories.index') }}" class="text-decoration-none small">View all</a>
        </div>
        <div class="row g-3 mb-4">
            @forelse ($categories as $category)
                <div class="col-6 col-md-3 col-lg-2">
                    <a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none">
                        <div class="card h-100 text-center p-3">
                            <div class="bg-soft-brand rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:56px;height:56px">
                                <i class="bi bi-tag text-brand fs-4"></i>
                            </div>
                            <small class="fw-semibold text-dark">{{ $category->name }}</small>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-muted">No categories available.</div>
            @endforelse
        </div>

        {{-- Featured products --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="section-title mb-0">Featured Products</h5>
            <a href="{{ route('products.index') }}" class="text-decoration-none small">See all</a>
        </div>
        <div class="row g-3 mb-4">
            @forelse ($featured as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <x-product-card :product="$product" />
                </div>
            @empty
                <div class="col-12 text-muted">No products available yet.</div>
            @endforelse
        </div>

        {{-- Discount products --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="section-title mb-0">Hot Deals</h5>
            <a href="{{ route('products.index', ['sort' => 'price_asc']) }}" class="text-decoration-none small">See all</a>
        </div>
        <div class="row g-3 mb-4">
            @forelse ($discounted as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <x-product-card :product="$product" />
                </div>
            @empty
                <div class="col-12 text-muted">No deals available right now.</div>
            @endforelse
        </div>

        {{-- Latest products --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="section-title mb-0">Latest Arrivals</h5>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-decoration-none small">See all</a>
        </div>
        <div class="row g-3">
            @forelse ($latest as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <x-product-card :product="$product" />
                </div>
            @empty
                <div class="col-12 text-muted">No products available yet.</div>
            @endforelse
        </div>
    </div>
@endsection
