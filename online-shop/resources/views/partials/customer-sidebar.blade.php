<aside class="sidebar p-3 d-none d-lg-flex flex-column" style="width:250px;flex-shrink:0">
    <div class="d-flex align-items-center gap-2 px-2 py-2 mb-3">
        <i class="bi bi-person-circle fs-4 text-white"></i>
        <div>
            <div class="brand">My Account</div>
            <small class="text-white-50">{{ auth()->user()->name }}</small>
        </div>
    </div>

    <nav class="nav flex-column flex-grow-1">
        <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
            <i class="bi bi-receipt"></i> My Orders
        </a>
        <a class="nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}" href="{{ route('wishlist.index') }}">
            <i class="bi bi-heart"></i> Wishlist
        </a>
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.show') }}">
            <i class="bi bi-person"></i> Profile
        </a>
        <a class="nav-link {{ request()->routeIs('support.create') || request()->routeIs('support.mine') ? 'active' : '' }}" href="{{ route('support.create') }}">
            <i class="bi bi-life-preserver"></i> Support
        </a>
    </nav>

    <div class="sidebar-footer pt-3 mt-3">
        <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-house-door"></i> Back to Store</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</aside>

<div class="d-lg-none p-3" style="background:#1e1b4b">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 text-white">
            <i class="bi bi-person-circle fs-4"></i>
            <span class="fw-bold">My Account</span>
        </div>
        <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#mobileCustomerNav">
            <i class="bi bi-list"></i> Menu
        </button>
    </div>
    <div class="collapse mt-2" id="mobileCustomerNav">
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}"><i class="bi bi-receipt"></i> My Orders</a>
            <a class="nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}" href="{{ route('wishlist.index') }}"><i class="bi bi-heart"></i> Wishlist</a>
            <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.show') }}"><i class="bi bi-person"></i> Profile</a>
            <a class="nav-link {{ request()->routeIs('support.*') ? 'active' : '' }}" href="{{ route('support.create') }}"><i class="bi bi-life-preserver"></i> Support</a>
        </nav>
    </div>
</div>
