<aside class="sidebar p-3 d-none d-lg-flex flex-column" style="width:250px;flex-shrink:0">
    <div class="d-flex align-items-center gap-2 px-2 py-2 mb-3">
        <i class="bi bi-shield-lock-fill fs-4 text-white"></i>
        <div>
            <div class="brand">ShopEase</div>
            <small class="text-white-50">Admin Panel</small>
        </div>
    </div>

    <nav class="nav flex-column flex-grow-1">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
            <i class="bi bi-people"></i> Users
        </a>
        <a class="nav-link {{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}" href="{{ route('admin.sellers.index') }}">
            <i class="bi bi-shop"></i> Sellers
        </a>
        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
            <i class="bi bi-person-badge"></i> Customers
        </a>
        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
            <i class="bi bi-tags"></i> Categories
        </a>
        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
            <i class="bi bi-box-seam"></i> Products
        </a>
        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
            <i class="bi bi-receipt"></i> Orders
        </a>
        <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
            <i class="bi bi-credit-card"></i> Payments
        </a>
        <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
            <i class="bi bi-boxes"></i> Inventory
        </a>
        <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
            <i class="bi bi-star"></i> Reviews
        </a>
        <a class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}" href="{{ route('admin.complaints.index') }}">
            <i class="bi bi-chat-square-dots"></i> Complaints
            @if ($openComplaints ?? 0 > 0)
                <span class="badge bg-danger ms-auto">{{ $openComplaints }}</span>
            @endif
        </a>
        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
            <i class="bi bi-graph-up"></i> Reports
        </a>
        <a class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}">
            <i class="bi bi-clock-history"></i> Audit Logs
        </a>
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
            <i class="bi bi-gear"></i> Settings
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

{{-- Mobile sidebar (collapsible) --}}
<div class="d-lg-none p-3" style="background:#1e1b4b">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2 text-white">
            <i class="bi bi-shield-lock-fill fs-4"></i>
            <span class="fw-bold">ShopEase — Admin</span>
        </div>
        <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#mobileAdminNav">
            <i class="bi bi-list"></i> Menu
        </button>
    </div>
    <div class="collapse mt-2" id="mobileAdminNav">
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Users</a>
            <a class="nav-link {{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}" href="{{ route('admin.sellers.index') }}"><i class="bi bi-shop"></i> Sellers</a>
            <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><i class="bi bi-person-badge"></i> Customers</a>
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><i class="bi bi-box-seam"></i> Products</a>
            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a>
            <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}"><i class="bi bi-credit-card"></i> Payments</a>
            <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}"><i class="bi bi-boxes"></i> Inventory</a>
            <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}"><i class="bi bi-star"></i> Reviews</a>
            <a class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}" href="{{ route('admin.complaints.index') }}"><i class="bi bi-chat-square-dots"></i> Complaints</a>
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}"><i class="bi bi-graph-up"></i> Reports</a>
            <a class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}"><i class="bi bi-clock-history"></i> Audit Logs</a>
            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear"></i> Settings</a>
        </nav>
    </div>
</div>
