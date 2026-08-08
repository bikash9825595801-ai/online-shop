@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-cart-x fs-1 text-muted"></i>
                <h5 class="mt-3">Your cart is empty</h5>
                <p class="text-muted">Add some products before checking out.</p>
                <a href="{{ route('products.index') }}" class="btn btn-brand">Browse Products</a>
            </div>
        </div>
    </div>
@endsection
