@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-decoration-none">Categories</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="section-title mb-0">{{ $category->name }}</h4>
                @if ($category->description)
                    <small class="text-muted">{{ $category->description }}</small>
                @endif
            </div>
            <span class="search-result-count">{{ $products->total() }} product(s)</span>
        </div>

        <div class="row g-3">
            @forelse ($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <x-product-card :product="$product" />
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                        <p class="text-muted mt-3">No products in this category yet.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            <x-pagination :paginator="$products" />
        </div>
    </div>
@endsection
