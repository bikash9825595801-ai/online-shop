@extends('layouts.seller')

@section('title', 'My Profile')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">My Profile</h4>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    @if ($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" class="rounded-circle mb-3" style="height:120px;width:120px;object-fit:cover" alt="">
                    @else
                        <i class="bi bi-person-circle text-muted" style="font-size:7rem"></i>
                    @endif
                    <h5 class="mb-0">{{ $user->name }}</h5>
                    <p class="text-muted small mb-2">{{ $user->email }}</p>
                    <span class="badge bg-soft-brand text-brand">Seller</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Edit Profile</div>
                <div class="card-body">
                    <form action="{{ route('seller.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" rows="2" class="form-control">{{ old('address', $user->address) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile image <small class="text-muted">(optional, max 2MB)</small></label>
                                <input type="file" name="profile_image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <hr>
                        <h6>Change Password</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Current password</label>
                                <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <label class="form-label">New password</label>
                                <input type="password" name="password" class="form-control" autocomplete="new-password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm new password</label>
                                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-brand mt-4"><i class="bi bi-check-lg me-1"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
