@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="auth-wrapper">
        <div class="card auth-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-key text-brand" style="font-size:2.2rem"></i>
                    <h4 class="fw-bold mt-2">Reset your password</h4>
                    <p class="text-muted small">Enter your email and we'll send you a reset link</p>
                </div>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2">Send Reset Link</button>
                </form>

                <p class="text-center mt-4 mb-0">
                    <a href="{{ route('login') }}" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Back to login</a>
                </p>
            </div>
        </div>
    </div>
@endsection
