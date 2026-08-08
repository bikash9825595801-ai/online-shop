<footer class="bg-dark text-white-50 pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="text-white fw-bold"><i class="bi bi-bag-heart-fill me-1"></i> Shop<span class="text-brand">Ease</span></h5>
                <p class="small mt-2">
                    Your one-stop online shopping management system. Browse products from trusted
                    sellers, track orders and enjoy cash on delivery.
                </p>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-white">Shop</h6>
                <ul class="list-unstyled small">
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('products.index') }}">All Products</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('categories.index') }}">Categories</a></li>
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('products.index', ['sort' => 'price_asc']) }}">Best Deals</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="text-white">Account</h6>
                <ul class="list-unstyled small">
                    @auth
                        <li><a class="text-white-50 text-decoration-none" href="{{ route('orders.index') }}">My Orders</a></li>
                        <li><a class="text-white-50 text-decoration-none" href="{{ route('wishlist.index') }}">Wishlist</a></li>
                        <li><a class="text-white-50 text-decoration-none" href="{{ route('profile.show') }}">Profile</a></li>
                    @else
                        <li><a class="text-white-50 text-decoration-none" href="{{ route('login') }}">Login</a></li>
                        <li><a class="text-white-50 text-decoration-none" href="{{ route('register') }}">Register</a></li>
                    @endauth
                    <li><a class="text-white-50 text-decoration-none" href="{{ route('support.create') }}">Support</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white">Contact</h6>
                <ul class="list-unstyled small">
                    <li><i class="bi bi-geo-alt me-2"></i>123 Market Street, City, Country</li>
                    <li><i class="bi bi-telephone me-2"></i>+1 (555) 123-4567</li>
                    <li><i class="bi bi-envelope me-2"></i>support@shopease.test</li>
                </ul>
            </div>
        </div>
        <hr class="my-4" style="border-color:rgba(255,255,255,.1)">
        <div class="d-flex justify-content-between small">
            <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
            <span>Built with Laravel + Bootstrap</span>
        </div>
    </div>
</footer>
