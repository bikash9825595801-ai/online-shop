@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Site Settings</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $siteName) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Low-Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $lowStockThreshold) }}" min="1" max="1000" required>
                        <small class="text-muted">Products at or below this stock level are flagged as low stock.</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-brand mt-4"><i class="bi bi-check-lg me-1"></i> Save Settings</button>
            </form>
        </div>
    </div>
@endsection
