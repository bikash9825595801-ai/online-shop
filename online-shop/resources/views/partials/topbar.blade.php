<div class="topbar py-1">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="bi bi-truck me-1"></i> Free delivery on orders over $100</span>
        <div class="d-flex align-items-center gap-3">
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : (auth()->user()->isSeller() ? route('seller.dashboard') : route('customer.dashboard')) }}">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none p-0 text-light" style="font-size:inherit">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                <a href="{{ route('register') }}"><i class="bi bi-person-plus me-1"></i> Register</a>
            @endauth
        </div>
    </div>
</div>
