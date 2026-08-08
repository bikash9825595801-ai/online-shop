@extends('layouts.seller')

@section('title', 'Edit Product')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Edit Product</h4>
        <a href="{{ route('seller.products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i> Changes require admin re-approval before appearing in the storefront.
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Product name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($product->category_id == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price ($)</label>
                        <input type="number" step="0.01" min="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Discount price ($)</label>
                        <input type="number" step="0.01" min="0" name="discount_price" class="form-control" value="{{ old('discount_price', $product->discount_price) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stock</label>
                        <input type="number" min="0" name="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Image <small class="text-muted">(optional, max 2MB)</small></label>
                        @if ($product->image)
                            <div class="mb-2">
                                <img src="{{ product_image_url($product->image) }}" style="height:80px;border-radius:.5rem" alt="">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @if ($product->image)
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage" value="1">
                                <label class="form-check-label small" for="removeImage">Remove current image</label>
                            </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-brand mt-4"><i class="bi bi-check-lg me-1"></i> Submit for Re-Approval</button>
            </form>
        </div>
    </div>
@endsection
