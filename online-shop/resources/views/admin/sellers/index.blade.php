@extends('layouts.admin')

@section('title', 'Sellers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Sellers</h4>
        <a href="{{ route('admin.sellers.create') }}" class="btn btn-brand btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Seller</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sellers.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-8">
                    <input type="search" name="search" class="form-control" placeholder="Search sellers..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-brand w-100" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.sellers.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Seller</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sellers as $seller)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-shop fs-4 text-primary"></i>
                                        <div>
                                            <div class="small fw-semibold">{{ $seller->name }}</div>
                                            <small class="text-muted">{{ $seller->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-soft-brand text-brand">{{ $seller->products_count }} approved</span></td>
                                <td>
                                    <span class="badge {{ $seller->isActive() ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($seller->status) }}</span>
                                </td>
                                <td class="small text-muted">{{ $seller->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.sellers.show', $seller->id) }}" class="btn btn-sm btn-outline-brand"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.sellers.edit', $seller->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.sellers.toggle-block', $seller->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm {{ $seller->isBlocked() ? 'btn-outline-success' : 'btn-outline-warning' }}" type="submit"
                                                data-confirm="{{ $seller->isBlocked() ? 'Unblock this seller?' : 'Block this seller?' }}">
                                            <i class="bi {{ $seller->isBlocked() ? 'bi-unlock' : 'bi-lock' }}"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No sellers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$sellers" />
        </div>
    </div>
@endsection
