@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Edit Category</h4>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" @selected($category->status === 'active')>Active</option>
                        <option value="inactive" @selected($category->status === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image <small class="text-muted">(optional, max 2MB)</small></label>
                    @if ($category->image)
                        <div class="mb-2">
                            <img src="{{ product_image_url($category->image) }}" style="height:80px;border-radius:.5rem" alt="">
                        </div>
                    @endif
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-brand"><i class="bi bi-check-lg me-1"></i> Save Changes</button>
            </form>
        </div>
    </div>
@endsection
