@props(['product'])

<div class="card product-card h-100">
    <div class="product-media position-relative">
        <a href="{{ route('products.show', $product->slug) }}" class="position-relative d-block text-decoration-none overflow-hidden">
            @if ($product->hasDiscount())
                <span class="discount-badge">-{{ $product->discountPercent() }}%</span>
            @endif
            @if ($product->image)
                <img src="{{ product_image_url($product->image) }}" class="product-image w-100" alt="{{ $product->name }}" loading="lazy">
            @else
                <div class="product-image w-100 d-flex align-items-center justify-content-center bg-light">
                    <i class="bi bi-image text-muted" style="font-size:3rem"></i>
                </div>
            @endif
        </a>
        @if ($product->stock > 0)
            <form action="{{ route('cart.add') }}" method="POST" class="product-quick-add">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-brand btn-sm" title="Add to cart">
                    <i class="bi bi-cart-plus me-1"></i> Quick Add
                </button>
            </form>
        @endif
    </div>
    <div class="card-body d-flex flex-column">
        <small class="text-muted text-uppercase" style="font-size:.68rem">{{ $product->brand ?? $product->category->name }}</small>
        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
            <h6 class="mb-1 fw-semibold" style="min-height:2.4rem">{{ $product->name }}</h6>
        </a>
        <div class="mb-2">
            <span class="stars">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $product->averageRating() ? 'bi-star-fill' : 'bi-star' }}"></i>
                @endfor
            </span>
            <small class="text-muted">({{ $product->reviewsCount() }})</small>
        </div>
        <div class="mt-auto d-flex align-items-center justify-content-between">
            <div>
                <span class="fw-bold text-brand fs-5">Rs. {{ number_format($product->sellingPrice(), 2) }}</span>
                @if ($product->hasDiscount())
                    <span class="old-price ms-1">Rs. {{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            @if ($product->stock <= 0)
                <span class="badge bg-danger">Out of stock</span>
            @elseif ($product->stock <= 5)
                <span class="badge bg-warning text-dark">Only {{ $product->stock }} left</span>
            @endif
        </div>
    </div>
    <div class="card-footer bg-white border-top-0">
        @if ($product->stock > 0)
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-outline-brand w-100 btn-sm">
                    <i class="bi bi-cart-plus me-1"></i> Add to Cart
                </button>
            </form>
        @else
            <button class="btn btn-light w-100 btn-sm" disabled>Out of Stock</button>
        @endif
    </div>
</div>
