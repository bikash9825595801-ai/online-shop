@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Categories</h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-brand btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Category</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Category</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($category->image)
                                            <img src="{{ product_image_url($category->image) }}" class="avatar-sm" alt="">
                                        @endif
                                        <span class="small fw-semibold">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td class="small text-muted">{{ $category->slug }}</td>
                                <td><span class="badge bg-soft-brand text-brand">{{ $category->products_count }}</span></td>
                                <td>
                                    <span class="badge {{ $category->isActive() ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($category->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.categories.toggle-status', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm {{ $category->isActive() ? 'btn-outline-warning' : 'btn-outline-success' }}" type="submit"
                                                data-confirm="{{ $category->isActive() ? 'Deactivate this category?' : 'Activate this category?' }}">
                                            <i class="bi {{ $category->isActive() ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="Delete this category?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No categories found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
