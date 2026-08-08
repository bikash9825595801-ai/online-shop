@extends('layouts.app')

@section('title', 'Shop Products')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="section-title mb-0">All Products</h4>
                @if (request('q'))
                    <span class="search-result-count">Search results for "<strong>{{ request('q') }}</strong>"</span>
                @endif
            </div>
            <span class="search-result-count">{{ $products->total() }} product(s)</span>
        </div>

        <div class="row g-4">
            {{-- Filters sidebar --}}
            <div class="col-lg-3">
                <div class="card filter-card">
                    <div class="card-header"><i class="bi bi-sliders me-1"></i> Filters</div>
                    <div class="card-body">
                        <form action="{{ route('products.index') }}" method="GET">
                            @if (request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Category</label>
                                <select name="category" class="form-select form-select-sm">
                                    <option value="">All categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Brand</label>
                                <select name="brand" class="form-select form-select-sm">
                                    <option value="">All brands</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand }}" @selected(request('brand') == $brand)>{{ $brand }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Price range</label>
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}" min="0">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}" min="0">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Minimum rating</label>
                                <select name="rating" class="form-select form-select-sm">
                                    <option value="">Any rating</option>
                                    @for ($i = 4; $i >= 1; $i--)
                                        <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} star{{ $i > 1 ? 's' : '' }} &amp; up</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="in_stock" name="in_stock" value="1" @checked(request('in_stock'))>
                                <label class="form-check-label small" for="in_stock">In stock only</label>
                            </div>

                            <button type="submit" class="btn btn-brand btn-sm w-100">Apply Filters</button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">Reset</a>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Product grid --}}
            <div class="col-lg-9">
                <div class="d-flex justify-content-end mb-3">
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2 align-items-center">
                        @foreach (request()->except(['sort', 'page']) as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <label class="form-label small text-muted mb-0">Sort by</label>
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Latest</option>
                            <option value="price_asc" @selected(request('sort') == 'price_asc')>Price: Low to High</option>
                            <option value="price_desc" @selected(request('sort') == 'price_desc')>Price: High to Low</option>
                            <option value="newest" @selected(request('sort') == 'newest')>Newest</option>
                            <option value="rating" @selected(request('sort') == 'rating')>Top rated</option>
                        </select>
                    </form>
                </div>

                <div class="row g-3">
                    @forelse ($products as $product)
                        <div class="col-6 col-md-4 col-xl-3">
                            <x-product-card :product="$product" />
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                                <p class="text-muted mt-3">No products match your criteria.</p>
                                <a href="{{ route('products.index') }}" class="btn btn-brand btn-sm">Clear filters</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    <x-pagination :paginator="$products" />
                </div>
            </div>
        </div>
    </div>
@endsection
