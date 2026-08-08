@extends('layouts.admin')

@section('title', 'Edit Seller')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Edit Seller</h4>
        <a href="{{ route('admin.sellers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.sellers.update', $seller->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Seller name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $seller->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $seller->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $seller->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $seller->address) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">New password <small class="text-muted">(optional)</small></label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm new password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-brand mt-4"><i class="bi bi-check-lg me-1"></i> Save Changes</button>
            </form>
        </div>
    </div>
@endsection
