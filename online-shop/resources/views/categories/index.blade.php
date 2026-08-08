@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active">Categories</li>
            </ol>
        </nav>

        <h4 class="section-title mb-4">Browse Categories</h4>

        <div class="row g-3">
            @forelse ($categories as $category)
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('categories.show', $category->slug) }}" class="text-decoration-none">
                        <div class="card h-100 text-center p-4">
                            <div class="bg-soft-brand rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px;height:64px">
                                <i class="bi bi-tag text-brand fs-3"></i>
                            </div>
                            <h6 class="fw-semibold text-dark mb-1">{{ $category->name }}</h6>
                            <small class="text-muted">{{ $category->products_count }} product(s)</small>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-muted">No categories available.</div>
            @endforelse
        </div>
    </div>
@endsection
