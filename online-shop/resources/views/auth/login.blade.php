@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="auth-wrapper">
        <div class="card auth-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-bag-heart-fill text-brand" style="font-size:2.2rem"></i>
                    <h4 class="fw-bold mt-2">Welcome back</h4>
                    <p class="text-muted small">Sign in to your ShopEase account</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label small" for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2">Sign In</button>
                </form>

                <p class="text-center text-muted small mt-4 mb-0">
                    Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none">Create one</a>
                </p>
                <p class="text-center mt-3 mb-0">
                    <a href="{{ route('home') }}" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Back to store</a>
                </p>
            </div>
        </div>
    </div>
@endsection
