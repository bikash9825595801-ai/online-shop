<aside class="sidebar p-3 d-none d-lg-flex flex-column" style="width:250px;flex-shrink:0">
    <div class="d-flex align-items-center gap-2 px-2 py-2 mb-3">
        <i class="bi bi-bag-fill fs-4 text-white"></i>
        <div>
            <div class="brand">ShopEase</div>
            <small class="text-white-50">Seller Panel</small>
        </div>
    </div>

    <nav class="nav flex-column flex-grow-1">
        <a class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}" href="{{ route('seller.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}" href="{{ route('seller.products.index') }}">
            <i class="bi bi-box-seam"></i> My Products
        </a>
        <a class="nav-link {{ request()->routeIs('seller.products.create') ? 'active' : '' }}" href="{{ route('seller.products.create') }}">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
        <a class="nav-link {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}" href="{{ route('seller.orders.index') }}">
            <i class="bi bi-receipt"></i> Orders
        </a>
        <a class="nav-link {{ request()->routeIs('seller.inventory.*') ? 'active' : '' }}" href="{{ route('seller.inventory.index') }}">
            <i class="bi bi-boxes"></i> Inventory
        </a>
        <a class="nav-link {{ request()->routeIs('seller.reports.*') ? 'active' : '' }}" href="{{ route('seller.reports.index') }}">
            <i class="bi bi-graph-up"></i> Sales Reports
        </a>
        <a class="nav-link {{ request()->routeIs('seller.messages.*') ? 'active' : '' }}" href="{{ route('seller.messages.index') }}">
            <i class="bi bi-envelope"></i> Messages
        </a>
        <a class="nav-link {{ request()->routeIs('seller.profile.*') ? 'active' : '' }}" href="{{ route('seller.profile.edit') }}">
            <i class="bi bi-person-gear"></i> Profile
        </a>
    </nav>

    <div class="sidebar-footer pt-3 mt-3">
        <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-house-door"></i> Visit Store</a>
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
            <i class="bi bi-bag-fill fs-4"></i>
            <span class="fw-bold">ShopEase — Seller</span>
        </div>
        <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSellerNav">
            <i class="bi bi-list"></i> Menu
        </button>
    </div>
    <div class="collapse mt-2" id="mobileSellerNav">
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}" href="{{ route('seller.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="nav-link {{ request()->routeIs('seller.products.*') ? 'active' : '' }}" href="{{ route('seller.products.index') }}"><i class="bi bi-box-seam"></i> My Products</a>
            <a class="nav-link {{ request()->routeIs('seller.products.create') ? 'active' : '' }}" href="{{ route('seller.products.create') }}"><i class="bi bi-plus-circle"></i> Add Product</a>
            <a class="nav-link {{ request()->routeIs('seller.orders.*') ? 'active' : '' }}" href="{{ route('seller.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a>
            <a class="nav-link {{ request()->routeIs('seller.inventory.*') ? 'active' : '' }}" href="{{ route('seller.inventory.index') }}"><i class="bi bi-boxes"></i> Inventory</a>
            <a class="nav-link {{ request()->routeIs('seller.reports.*') ? 'active' : '' }}" href="{{ route('seller.reports.index') }}"><i class="bi bi-graph-up"></i> Sales Reports</a>
            <a class="nav-link {{ request()->routeIs('seller.messages.*') ? 'active' : '' }}" href="{{ route('seller.messages.index') }}"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link {{ request()->routeIs('seller.profile.*') ? 'active' : '' }}" href="{{ route('seller.profile.edit') }}"><i class="bi bi-person-gear"></i> Profile</a>
        </nav>
    </div>
</div>
