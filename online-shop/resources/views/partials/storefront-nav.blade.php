<nav class="navbar navbar-expand-lg storefront-nav sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-bag-heart-fill me-1"></i> Shop<span class="text-brand">Ease</span>
        </a>

        <form class="d-flex me-auto ms-lg-4 my-2 my-lg-0 flex-grow-1" style="max-width:420px" action="{{ route('products.index') }}" method="GET">
            <div class="input-group">
                <input type="search" name="q" class="form-control" placeholder="Search products, brands or SKU..."
                       value="{{ request('q') }}">
                <button class="btn btn-brand" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav align-items-lg-center ms-auto gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('categories.index') }}">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-icon d-inline-flex align-items-center mx-2" href="{{ route('products.index') }}" title="Products">
                        <i class="bi bi-box-seam"></i>
                    </a>
                </li>

                @auth
                    @if (auth()->user()->isCustomer())
                        <li class="nav-item">
                            <a class="nav-icon d-inline-flex align-items-center mx-2" href="{{ route('wishlist.index') }}" title="Wishlist">
                                <i class="bi bi-heart"></i>
                                @if ($wishlistCount > 0)
                                    <span class="nav-badge">{{ $wishlistCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->isCustomer() || auth()->user()->isSeller())
                        <li class="nav-item">
                            <a class="nav-icon d-inline-flex align-items-center mx-2" href="{{ route('messages.index') }}" title="Messages">
                                <i class="bi bi-chat-dots"></i>
                                @if (isset($messageCount) && $messageCount > 0)
                                    <span class="nav-badge">{{ $messageCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->isCustomer())
                        <li class="nav-item">
                            <a class="nav-icon d-inline-flex align-items-center mx-2" href="{{ route('cart.index') }}" title="Cart">
                                <i class="bi bi-cart3"></i>
                                @if ($cartCount > 0)
                                    <span class="nav-badge">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            @if (auth()->user()->profile_image)
                                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" class="avatar-sm" alt="">
                            @else
                                <i class="bi bi-person-circle fs-4"></i>
                            @endif
                            <span class="d-none d-lg-inline">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if (auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                            @elseif (auth()->user()->isSeller())
                                <li><a class="dropdown-item" href="{{ route('seller.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Seller Dashboard</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>My Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-receipt me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('messages.index') }}"><i class="bi bi-chat-dots me-2"></i>Messages</a></li>
                                <li><a class="dropdown-item" href="{{ route('wishlist.index') }}"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-brand btn-sm px-3">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-brand btn-sm px-3">Register</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
