@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            {{-- Image --}}
            <div class="col-lg-5">
                @if ($product->image)
                    <img src="{{ product_image_url($product->image) }}" class="product-detail-image" alt="{{ $product->name }}">
                @else
                    <div class="product-detail-image d-flex align-items-center justify-content-center">
                        <i class="bi bi-image text-muted" style="font-size:5rem"></i>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="col-lg-7">
                <small class="text-muted text-uppercase">{{ $product->brand ?? $product->category->name }}</small>
                <h3 class="fw-bold">{{ $product->name }}</h3>

                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $product->averageRating() ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </span>
                    <span class="small text-muted">{{ $product->averageRating() }} / 5 ({{ $product->reviewsCount() }} reviews)</span>
                </div>

                <div class="mb-3">
                    <span class="fs-3 fw-bold text-brand">Rs. {{ number_format($product->sellingPrice(), 2) }}</span>
                    @if ($product->hasDiscount())
                        <span class="old-price fs-5 ms-2">Rs. {{ number_format($product->price, 2) }}</span>
                        <span class="badge bg-danger ms-2">-{{ $product->discountPercent() }}%</span>
                    @endif
                </div>

                <p class="text-muted">{{ $product->description }}</p>

                <ul class="list-unstyled small mb-4">
                    <li class="mb-1">
                        <strong>Availability:</strong>
                        @if ($product->stock <= 0)
                            <span class="badge bg-danger">Out of stock</span>
                        @elseif ($product->stock <= 10)
                            <span class="badge bg-warning text-dark">Only {{ $product->stock }} left in stock</span>
                        @else
                            <span class="badge bg-success">In stock ({{ $product->stock }})</span>
                        @endif
                    </li>
                    <li class="mb-1"><strong>SKU:</strong> {{ $product->sku ?? '—' }}</li>
                    <li class="mb-1">
                        <strong>Seller:</strong>
                        <a href="#" class="text-decoration-none">{{ $product->seller->name }}</a>
                    </li>
                    <li><strong>Category:</strong> {{ $product->category->name }}</li>
                </ul>

                @if ($product->stock > 0)
                    <form action="{{ route('cart.add') }}" method="POST" class="d-flex gap-2 align-items-end flex-wrap">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div>
                            <label class="form-label small">Quantity</label>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                   class="form-control" style="width:100px">
                        </div>
                        <button type="submit" class="btn btn-brand">
                            <i class="bi bi-cart-plus me-1"></i> Add to Cart
                        </button>
                    </form>

                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="redirect_to" value="checkout">
                            <button type="submit" class="btn btn-dark w-100">
                                <i class="bi bi-lightning-charge me-1"></i> Buy Now
                            </button>
                        </form>

                        <form action="{{ route('wishlist.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-heart me-1"></i> Wishlist
                            </button>
                        </form>
                    </div>
                @else
                    <button class="btn btn-secondary" disabled><i class="bi bi-x-circle me-1"></i> Out of Stock</button>
                @endif
            </div>
        </div>

        {{-- Reviews --}}
        <div class="row g-4 mt-3">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-chat-left-text me-1"></i> Customer Reviews</span>
                        <span class="badge bg-soft-brand text-brand">{{ $reviews->total() }}</span>
                    </div>
                    <div class="card-body">
                        @forelse ($reviews as $review)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($review->customer->profile_image)
                                            <img src="{{ asset('storage/' . $review->customer->profile_image) }}" class="avatar-sm" alt="">
                                        @else
                                            <i class="bi bi-person-circle fs-4 text-muted"></i>
                                        @endif
                                        <strong class="small">{{ $review->customer->name }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="stars my-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                                <p class="mb-0 small">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No reviews yet. Be the first to review this product!</p>
                        @endforelse

                        <div class="mt-2">
                            <x-pagination :paginator="$reviews" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related products --}}
            <div class="col-lg-5">
                <div class="card mb-3">
                    <div class="card-header">Related Products</div>
                    <div class="card-body">
                        @forelse ($related as $relatedProduct)
                            <div class="d-flex align-items-center gap-3 border-bottom pb-2 mb-2">
                                @if ($relatedProduct->image)
                                    <img src="{{ product_image_url($relatedProduct->image) }}" style="width:56px;height:56px;object-fit:cover;border-radius:.5rem" alt="">
                                @endif
                                <div class="flex-grow-1">
                                    <a href="{{ route('products.show', $relatedProduct->slug) }}" class="text-decoration-none text-dark small fw-semibold">{{ $relatedProduct->name }}</a>
                                    <div class="small">
                                        <span class="fw-bold text-brand">Rs. {{ number_format($relatedProduct->sellingPrice(), 2) }}</span>
                                        @if ($relatedProduct->hasDiscount())
                                            <span class="old-price">Rs. {{ number_format($relatedProduct->price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0 small">No related products.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
