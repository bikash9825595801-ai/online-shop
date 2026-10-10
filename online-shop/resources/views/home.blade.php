@extends('layouts.app')

@section('title', 'ShopEase — Online Shopping')

@section('content')
    {{-- ============ HERO ============ --}}
    <section class="hp-hero">
        <div class="container position-relative">
            <div class="row align-items-center py-5">
                <div class="col-lg-6">
                    <span class="hp-hero-badge">
                        <i class="bi bi-fire me-1"></i> Festive Sale — Up to 70% Off
                    </span>
                    <h1 class="hp-hero-title">
                        Everything you love,<br>
                        <span>delivered to your door.</span>
                    </h1>
                    <p class="hp-hero-sub">
                        Shop thousands of products from trusted sellers across Nepal.
                        Cash on delivery, fast shipping, and hassle-free returns.
                    </p>

                    <form class="hp-hero-search" action="{{ route('products.index') }}" method="GET">
                        <i class="bi bi-search"></i>
                        <input type="search" name="q" placeholder="Search for products, brands and categories..." value="{{ request('q') }}">
                        <button type="submit">Search</button>
                    </form>

                    <div class="hp-hero-pills">
                        <span class="hp-pill">Free delivery over Rs. 999</span>
                        <span class="hp-pill">Cash on Delivery</span>
                        <span class="hp-pill">7-Day Returns</span>
                    </div>
                </div>

                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hp-hero-art">
                        <div class="hp-hero-blob"></div>
                        <div class="hp-float-card hp-float-1">
                            <i class="bi bi-truck"></i>
                            <div>
                                <strong>Free Shipping</strong>
                                <small>On orders over Rs. 999</small>
                            </div>
                        </div>
                        <div class="hp-float-card hp-float-2">
                            <i class="bi bi-shield-check"></i>
                            <div>
                                <strong>100% Secure</strong>
                                <small>Safe payments</small>
                            </div>
                        </div>
                        <div class="hp-float-card hp-float-3">
                            <i class="bi bi-star-fill"></i>
                            <div>
                                <strong>4.8 / 5 Rating</strong>
                                <small>12,000+ happy shoppers</small>
                            </div>
                        </div>
                        <div class="hp-hero-icon">
                            <i class="bi bi-bag-heart-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FEATURES STRIP ============ --}}
    <section class="container mt-n5 position-relative" style="z-index:2">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="hp-feature">
                    <div class="hp-feature-icon"><i class="bi bi-truck"></i></div>
                    <div>
                        <strong>Free Delivery</strong>
                        <span>On orders over Rs. 999</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="hp-feature">
                    <div class="hp-feature-icon bg-soft-green text-success"><i class="bi bi-arrow-counterclockwise"></i></div>
                    <div>
                        <strong>Easy Returns</strong>
                        <span>7-day return policy</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="hp-feature">
                    <div class="hp-feature-icon bg-soft-orange text-warning"><i class="bi bi-wallet2"></i></div>
                    <div>
                        <strong>Cash on Delivery</strong>
                        <span>Pay when you receive</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="hp-feature">
                    <div class="hp-feature-icon bg-soft-pink text-danger"><i class="bi bi-headset"></i></div>
                    <div>
                        <strong>24/7 Support</strong>
                        <span>We're always here</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ CATEGORIES ============ --}}
    <section class="container mt-5 pt-3">
        <div class="hp-section-head">
            <div>
                <h2 class="hp-section-title">Shop by Category</h2>
                <p class="hp-section-sub">Browse our most popular departments</p>
            </div>
            <a href="{{ route('categories.index') }}" class="hp-link">View all <i class="bi bi-arrow-right"></i></a>
        </div>

        @php
            $catStyle = [
                'Electronics'        => ['bi-cpu', 'linear-gradient(135deg,#6366f1,#4338ca)'],
                'Fashion'            => ['bi-bag', 'linear-gradient(135deg,#ec4899,#be185d)'],
                'Home & Living'      => ['bi-house-heart', 'linear-gradient(135deg,#14b8a6,#0f766e)'],
                'Beauty & Health'    => ['bi-heart-pulse', 'linear-gradient(135deg,#f472b6,#db2777)'],
                'Sports & Outdoors'  => ['bi-trophy', 'linear-gradient(135deg,#f59e0b,#d97706)'],
                'Toys & Kids'        => ['bi-emoji-smile', 'linear-gradient(135deg,#a78bfa,#7c3aed)'],
                'Books & Stationery' => ['bi-book', 'linear-gradient(135deg,#38bdf8,#0284c7)'],
                'Grocery'            => ['bi-cart3', 'linear-gradient(135deg,#34d399,#059669)'],
            ];
        @endphp

        <div class="row g-3">
            @forelse ($categories as $category)
                @php [$icon, $grad] = $catStyle[$category->name] ?? ['bi-tag', 'linear-gradient(135deg,#6366f1,#4338ca)']; @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="{{ route('categories.show', $category->slug) }}" class="hp-cat-card">
                        <div class="hp-cat-icon" style="background: {{ $grad }}">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <span class="hp-cat-name">{{ $category->name }}</span>
                        <span class="hp-cat-go"><i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            @empty
                <div class="col-12 text-muted">No categories available.</div>
            @endforelse
        </div>
    </section>

    {{-- ============ HOT DEALS ============ --}}
    @if ($discounted->isNotEmpty())
        <section class="container mt-5">
            <div class="hp-deals-banner">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                    <div>
                        <span class="hp-deals-tag"><i class="bi bi-lightning-charge-fill me-1"></i> Limited Time Offers</span>
                        <h2 class="hp-deals-title mb-0 mt-2">Hot Deals of the Week</h2>
                    </div>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="hp-countdown d-flex align-items-center gap-1" data-target="{{ today()->addDay()->startOfDay()->format('Y-m-d H:i:s') }}">
                            <span class="hp-count-label me-2"><i class="bi bi-clock-history me-1"></i>Ends in</span>
                            <div class="hp-count-box"><span data-cd="d">00</span><em>days</em></div>
                            <div class="hp-count-sep">:</div>
                            <div class="hp-count-box"><span data-cd="h">00</span><em>hrs</em></div>
                            <div class="hp-count-sep">:</div>
                            <div class="hp-count-box"><span data-cd="m">00</span><em>min</em></div>
                            <div class="hp-count-sep">:</div>
                            <div class="hp-count-box"><span data-cd="s">00</span><em>sec</em></div>
                        </div>
                        <a href="{{ route('products.index', ['sort' => 'price_asc']) }}" class="btn btn-light fw-semibold px-4">
                            View All Deals <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach ($discounted as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ FEATURED ============ --}}
    <section class="container mt-5">
        <div class="hp-section-head">
            <div>
                <h2 class="hp-section-title">Featured Products</h2>
                <p class="hp-section-sub">Hand-picked favourites from our sellers</p>
            </div>
            <a href="{{ route('products.index') }}" class="hp-link">See all <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="row g-3">
            @forelse ($featured as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <x-product-card :product="$product" />
                </div>
            @empty
                <div class="col-12 text-muted">No products available yet.</div>
            @endforelse
        </div>
    </section>

    {{-- ============ WHY SHOP WITH US ============ --}}
    <section class="container mt-5">
        <div class="hp-why">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <span class="hp-why-eyebrow">WHY SHOPEASE</span>
                    <h2 class="hp-why-title">Shopping that actually feels easy.</h2>
                    <p class="hp-why-sub">
                        We built ShopEase around three promises — honest prices, reliable delivery,
                        and support that shows up when you need it.
                    </p>
                    <div class="row g-3 mt-1">
                        <div class="col-6">
                            <div class="hp-why-stat">
                                <strong>12K+</strong>
                                <span>Happy shoppers</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hp-why-stat">
                                <strong>2,500+</strong>
                                <span>Products listed</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hp-why-stat">
                                <strong>98%</strong>
                                <span>On-time delivery</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hp-why-stat">
                                <strong>4.8/5</strong>
                                <span>Average rating</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="hp-why-card">
                                <i class="bi bi-truck"></i>
                                <h6>Fast & Free Delivery</h6>
                                <p>Free shipping on orders over Rs. 999, dispatched within 24 hours.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="hp-why-card">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                <h6>7-Day Easy Returns</h6>
                                <p>Changed your mind? Return any item within a week, no questions asked.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="hp-why-card">
                                <i class="bi bi-shield-check"></i>
                                <h6>Secure Payments</h6>
                                <p>Your transactions are encrypted and protected end to end.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="hp-why-card">
                                <i class="bi bi-person-check"></i>
                                <h6>Verified Sellers</h6>
                                <p>Every seller is reviewed and approved before listing products.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ LATEST ============ --}}
    <section class="container mt-5">
        <div class="hp-section-head">
            <div>
                <h2 class="hp-section-title">Latest Arrivals</h2>
                <p class="hp-section-sub">Fresh stock, just added</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="hp-link">See all <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="row g-3">
            @forelse ($latest as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <x-product-card :product="$product" />
                </div>
            @empty
                <div class="col-12 text-muted">No products available yet.</div>
            @endforelse
        </div>
    </section>

    {{-- ============ TRUST TICKER ============ --}}
    <section class="container mt-5">
        <div class="hp-ticker">
            <div class="hp-ticker-inner">
                <span class="hp-ticker-item"><i class="bi bi-check-circle-fill"></i> 25,000+ orders delivered across Nepal</span>
                <span class="hp-ticker-item"><i class="bi bi-check-circle-fill"></i> 500+ verified sellers</span>
                <span class="hp-ticker-item"><i class="bi bi-check-circle-fill"></i> Free cash-on-delivery service</span>
                <span class="hp-ticker-item"><i class="bi bi-check-circle-fill"></i> 7-day hassle-free returns</span>
                <span class="hp-ticker-item"><i class="bi bi-check-circle-fill"></i> 24/7 customer support</span>
                <span class="hp-ticker-item"><i class="bi bi-check-circle-fill"></i> 100% secure checkout</span>
            </div>
        </div>
    </section>

    {{-- ============ TESTIMONIALS ============ --}}
    <section class="container mt-5">
        <div class="hp-section-head text-center justify-content-center flex-column">
            <h2 class="hp-section-title">Loved by Shoppers</h2>
            <p class="hp-section-sub">Don't take our word for it — here's what customers say</p>
        </div>
        <div class="row g-3">
            @php
                $testimonials = [
                    ['name' => 'Aarav Sharma', 'loc' => 'Kathmandu', 'text' => 'Ordered a phone on Sunday, it arrived by Tuesday. Genuine product and great packaging. This is my go-to store now.', 'item' => 'Electronics'],
                    ['name' => 'Priya Gurung', 'loc' => 'Pokhara', 'text' => 'The cash-on-delivery option made it so easy to trust. Prices beat the local market and quality is top notch.', 'item' => 'Fashion'],
                    ['name' => 'Suman Thapa', 'loc' => 'Butwal', 'text' => 'Was worried about size fit, but returns were super smooth. Support replied within minutes and processed my refund.', 'item' => 'Home & Living'],
                ];
            @endphp
            @foreach ($testimonials as $t)
                <div class="col-md-4">
                    <div class="hp-testimonial">
                        <div class="hp-test-stars">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="bi bi-star-fill"></i>
                            @endfor
                        </div>
                        <p class="hp-test-text">“{{ $t['text'] }}”</p>
                        <div class="hp-test-user">
                            <div class="hp-test-avatar">{{ substr($t['name'], 0, 1) }}</div>
                            <div>
                                <strong>{{ $t['name'] }}</strong>
                                <span><i class="bi bi-geo-alt"></i> {{ $t['loc'] }} · Bought {{ $t['item'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ NEWSLETTER ============ --}}
    <section class="container mt-5 mb-5">
        <div class="hp-newsletter">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <h3 class="mb-2">Get deals before everyone else.</h3>
                    <p class="mb-0 opacity-75">Subscribe and we'll send you the best offers every week. No spam, ever.</p>
                </div>
                <div class="col-lg-6">
                    <form class="hp-newsletter-form" onsubmit="event.preventDefault(); this.reset(); this.querySelector('button').textContent='Subscribed ✓';">
                        <input type="email" required placeholder="Enter your email address">
                        <button type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

    @push('scripts')
    @if ($discounted->isNotEmpty())
        <script>
            (function(){
                const el = document.querySelector('.hp-countdown');
                if(!el) return;
                const target = new Date(el.dataset.target).getTime();
                const d = el.querySelector('[data-cd="d"]'),
                      h = el.querySelector('[data-cd="h"]'),
                      m = el.querySelector('[data-cd="m"]'),
                      s = el.querySelector('[data-cd="s"]');

                function tick(){
                    const now = Date.now();
                    let diff = target - now;
                    if(diff < 0) diff = 0;
                    const days = Math.floor(diff/86400000);
                    diff %= 86400000;
                    const hrs  = Math.floor(diff/3600000);
                    diff %= 3600000;
                    const mins = Math.floor(diff/60000);
                    diff %= 60000;
                    const secs = Math.floor(diff/1000);
                    d.textContent = String(days).padStart(2,'0');
                    h.textContent = String(hrs).padStart(2,'0');
                    m.textContent = String(mins).padStart(2,'0');
                    s.textContent = String(secs).padStart(2,'0');
                    if(diff === 0) clearInterval(id);
                }
                const id = setInterval(tick,1000);
                tick();
            })();
        </script>
    @endif
    @endpush

    @push('styles')
<style>
    /* ============ HERO ============ */
    .hp-hero {
        background:
            radial-gradient(1000px 420px at 85% -10%, rgba(99,102,241,.55), transparent 60%),
            linear-gradient(125deg, #1e1b4b 0%, #312e81 45%, #4338ca 100%);
        color: #fff;
        border-radius: 0 0 2.5rem 2.5rem;
        overflow: hidden;
        position: relative;
    }

    .hp-hero-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        backdrop-filter: blur(6px);
        border-radius: 50rem;
        padding: .4rem .9rem;
        font-size: .82rem;
        font-weight: 600;
        letter-spacing: .02em;
        margin-bottom: 1.25rem;
    }

    .hp-hero-title {
        font-size: clamp(2.1rem, 4.4vw, 3.4rem);
        font-weight: 800;
        line-height: 1.12;
        letter-spacing: -.03em;
        margin-bottom: 1rem;
    }

    .hp-hero-title span {
        background: linear-gradient(90deg, #a5b4fc, #f0abfc);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    .hp-hero-sub {
        font-size: 1.05rem;
        color: #c7d2fe;
        max-width: 34rem;
        margin-bottom: 1.75rem;
    }

    .hp-hero-search {
        display: flex;
        align-items: center;
        gap: .5rem;
        background: #fff;
        border-radius: 50rem;
        padding: .4rem .4rem .4rem 1.1rem;
        max-width: 34rem;
        box-shadow: 0 18px 40px rgba(15,23,42,.25);
    }

    .hp-hero-search i { color: #9ca3af; font-size: 1.05rem; }

    .hp-hero-search input {
        flex: 1;
        border: 0;
        outline: 0;
        font-size: .98rem;
        padding: .6rem .25rem;
        background: transparent;
        min-width: 0;
    }

    .hp-hero-search button {
        background: var(--brand);
        color: #fff;
        border: 0;
        border-radius: 50rem;
        font-weight: 600;
        padding: .65rem 1.5rem;
        transition: background .15s;
        white-space: nowrap;
    }

    .hp-hero-search button:hover { background: var(--brand-dark); }

    .hp-hero-pills {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-top: 1.25rem;
    }

    .hp-pill {
        font-size: .78rem;
        color: #e0e7ff;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 50rem;
        padding: .3rem .75rem;
    }

    /* Hero art */
    .hp-hero-art {
        position: relative;
        height: 380px;
    }

    .hp-hero-blob {
        position: absolute;
        inset: 8% 4% 8% 12%;
        background: linear-gradient(140deg, rgba(255,255,255,.14), rgba(255,255,255,.04));
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 2rem;
        transform: rotate(-4deg);
        backdrop-filter: blur(4px);
    }

    .hp-hero-icon {
        position: absolute;
        top: 50%; left: 52%;
        transform: translate(-50%, -50%);
        font-size: 8.5rem;
        color: rgba(255,255,255,.9);
        filter: drop-shadow(0 18px 30px rgba(15,23,42,.4));
        animation: hpFloat 5s ease-in-out infinite;
    }

    @keyframes hpFloat {
        0%, 100% { transform: translate(-50%, -50%) translateY(0); }
        50%      { transform: translate(-50%, -50%) translateY(-14px); }
    }

    .hp-float-card {
        position: absolute;
        display: flex;
        align-items: center;
        gap: .65rem;
        background: #fff;
        color: #111827;
        border-radius: .9rem;
        padding: .7rem .95rem;
        box-shadow: 0 16px 34px rgba(15,23,42,.28);
        z-index: 2;
        animation: hpFloat 6s ease-in-out infinite;
    }

    .hp-float-card i {
        width: 38px; height: 38px;
        border-radius: .6rem;
        background: var(--brand-soft);
        color: var(--brand);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }

    .hp-float-card strong { display: block; font-size: .85rem; }
    .hp-float-card small { color: #6b7280; font-size: .72rem; }

    .hp-float-1 { top: 6%;  left: 0;   animation-delay: .4s; }
    .hp-float-2 { top: 34%; right: 0;  animation-delay: 1.2s; }
    .hp-float-3 { bottom: 6%; left: 8%; animation-delay: .8s; }

    /* ============ FEATURES STRIP ============ */
    .hp-feature {
        display: flex;
        align-items: center;
        gap: .85rem;
        background: #fff;
        border: 1px solid #eef0f4;
        border-radius: .9rem;
        padding: 1rem 1.1rem;
        height: 100%;
        box-shadow: 0 8px 22px rgba(15,23,42,.06);
    }

    .hp-feature-icon {
        width: 46px; height: 46px;
        min-width: 46px;
        border-radius: .75rem;
        background: var(--brand-soft);
        color: var(--brand);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
    }

    .bg-soft-green { background: #ecfdf5; }
    .bg-soft-orange { background: #fffbeb; }
    .bg-soft-pink { background: #fff1f2; }

    .hp-feature strong { display: block; font-size: .92rem; color: #111827; }
    .hp-feature span { font-size: .78rem; color: #6b7280; }

    /* ============ SECTIONS ============ */
    .hp-section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }

    .hp-section-title {
        font-size: 1.55rem;
        font-weight: 800;
        letter-spacing: -.02em;
        color: #111827;
        margin-bottom: .15rem;
    }

    .hp-section-sub {
        color: #6b7280;
        font-size: .92rem;
        margin: 0;
    }

    .hp-link {
        color: var(--brand);
        text-decoration: none;
        font-weight: 600;
        font-size: .9rem;
        white-space: nowrap;
    }

    .hp-link:hover { text-decoration: underline; }

    /* ============ CATEGORY CARDS ============ */
    .hp-cat-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: .85rem;
        background: #fff;
        border: 1px solid #eef0f4;
        border-radius: .9rem;
        padding: 1rem 1.1rem;
        text-decoration: none;
        height: 100%;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        overflow: hidden;
    }

    .hp-cat-card:hover {
        transform: translateY(-3px);
        border-color: #c7d2fe;
        box-shadow: 0 12px 26px rgba(79,70,229,.12);
    }

    .hp-cat-icon {
        width: 50px; height: 50px;
        min-width: 50px;
        border-radius: .8rem;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-size: 1.35rem;
        box-shadow: 0 8px 18px rgba(15,23,42,.18);
    }

    .hp-cat-name {
        font-weight: 600;
        font-size: .95rem;
        color: #111827;
    }

    .hp-cat-go {
        margin-left: auto;
        color: #cbd5e1;
        transition: color .15s, transform .15s;
    }

    .hp-cat-card:hover .hp-cat-go {
        color: var(--brand);
        transform: translateX(3px);
    }

    /* ============ HOT DEALS ============ */
    .hp-deals-banner {
        background:
            radial-gradient(700px 260px at 100% 0%, rgba(239,68,68,.10), transparent 65%),
            linear-gradient(180deg, #fff7ed 0%, #fff 70%);
        border: 1px solid #fed7aa;
        border-radius: 1.4rem;
        padding: 2rem 1.5rem;
    }

    .hp-deals-tag {
        display: inline-flex;
        align-items: center;
        background: #ef4444;
        color: #fff;
        font-size: .78rem;
        font-weight: 700;
        border-radius: 50rem;
        padding: .35rem .85rem;
        letter-spacing: .03em;
    }

    .hp-deals-title {
        font-size: 1.7rem;
        font-weight: 800;
        letter-spacing: -.02em;
        color: #7c2d12;
    }

    /* ============ WHY SHOP WITH US ============ */
    .hp-why {
        background: #fff;
        border: 1px solid #eef0f4;
        border-radius: 1.4rem;
        padding: 2.5rem 2rem;
        box-shadow: 0 10px 30px rgba(15,23,42,.05);
    }

    .hp-why-eyebrow {
        display: inline-block;
        color: var(--brand);
        background: var(--brand-soft);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .12em;
        padding: .3rem .7rem;
        border-radius: 50rem;
        margin-bottom: .9rem;
    }

    .hp-why-title {
        font-size: clamp(1.5rem, 2.6vw, 2.1rem);
        font-weight: 800;
        letter-spacing: -.03em;
        color: #111827;
        margin-bottom: .75rem;
    }

    .hp-why-sub { color: #6b7280; }

    .hp-why-stat {
        background: #f8fafc;
        border: 1px solid #eef0f4;
        border-radius: .8rem;
        padding: .85rem 1rem;
    }

    .hp-why-stat strong {
        display: block;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--brand);
    }

    .hp-why-stat span { font-size: .8rem; color: #6b7280; }

    .hp-why-card {
        background: #f8fafc;
        border: 1px solid #eef0f4;
        border-radius: 1rem;
        padding: 1.25rem;
        height: 100%;
        transition: transform .15s, box-shadow .15s, background .15s;
    }

    .hp-why-card:hover {
        background: #fff;
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(15,23,42,.08);
    }

    .hp-why-card i {
        width: 44px; height: 44px;
        border-radius: .7rem;
        background: var(--brand-soft);
        color: var(--brand);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-bottom: .85rem;
    }

    .hp-why-card h6 { font-weight: 700; margin-bottom: .35rem; color: #111827; }
    .hp-why-card p { font-size: .86rem; color: #6b7280; margin: 0; }

    /* ============ NEWSLETTER ============ */
    .hp-newsletter {
        background:
            radial-gradient(600px 200px at 100% 100%, rgba(165,180,252,.35), transparent 60%),
            linear-gradient(120deg, #1e1b4b, #4338ca);
        color: #fff;
        border-radius: 1.4rem;
        padding: 2.5rem 2rem;
    }

    .hp-newsletter h3 { font-weight: 800; letter-spacing: -.02em; }

    .hp-newsletter-form {
        display: flex;
        background: #fff;
        border-radius: 50rem;
        padding: .35rem;
        box-shadow: 0 14px 30px rgba(15,23,42,.3);
    }

    .hp-newsletter-form input {
        flex: 1;
        border: 0;
        outline: 0;
        background: transparent;
        padding: .65rem 1.1rem;
        font-size: .95rem;
        min-width: 0;
        color: #111827;
    }

    .hp-newsletter-form button {
        background: var(--brand);
        color: #fff;
        border: 0;
        border-radius: 50rem;
        font-weight: 600;
        padding: .65rem 1.5rem;
        transition: background .15s;
        white-space: nowrap;
    }

    .hp-newsletter-form button:hover { background: var(--brand-dark); }

    /* ============ TRUST TICKER ============ */
    .hp-ticker {
        background: linear-gradient(120deg, #faf5ff, #eef2ff);
        border: 1px solid #e6e0f8;
        border-radius: 1rem;
        overflow: hidden;
        position: relative;
        padding: .55rem 0;
    }

    .hp-ticker-inner {
        display: flex;
        gap: 2.5rem;
        width: max-content;
        animation: hp-ticker 28s linear infinite;
    }

    .hp-ticker-item {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .88rem;
        font-weight: 600;
        color: #4f46e5;
        white-space: nowrap;
    }

    .hp-ticker-item i { color: #22c55e; font-size: .85rem; }

    @keyframes hp-ticker {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }

    /* ============ COUNTDOWN ============ */
    .hp-countdown { font-size: .8rem; }

    .hp-count-label {
        color: #c7d2fe;
        font-weight: 600;
        font-size: .8rem;
        white-space: nowrap;
    }

    .hp-count-box {
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.22);
        border-radius: .55rem;
        padding: .3rem .5rem;
        text-align: center;
        min-width: 44px;
    }

    .hp-count-box span {
        display: block;
        font-weight: 800;
        font-size: 1.05rem;
        line-height: 1.1;
        color: #fff;
        font-variant-numeric: tabular-nums;
    }

    .hp-count-box em {
        font-style: normal;
        font-size: .62rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #c7d2fe;
        display: block;
        margin-top: 2px;
    }

    .hp-count-sep { color: #c7d2fe; font-weight: 800; }

    /* ============ TESTIMONIALS ============ */
    .hp-testimonial {
        border: 1px solid #eef0f4;
        border-radius: 1.1rem;
        background: #fff;
        padding: 1.5rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: .9rem;
        transition: transform .15s, box-shadow .15s;
    }

    .hp-testimonial:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 34px rgba(15,23,42,.08);
    }

    .hp-test-stars { color: #f59e0b; font-size: .85rem; letter-spacing: 2px; }

    .hp-test-text {
        color: #374151;
        font-size: .95rem;
        line-height: 1.7;
        margin: 0;
        flex: 1;
    }

    .hp-test-user { display: flex; gap: .7rem; align-items: center; }

    .hp-test-avatar {
        width: 42px; height: 42px;
        border-radius: 50rem;
        background: linear-gradient(135deg, #6366f1, #4338ca);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800;
    }

    .hp-test-user div:last-child { line-height: 1.35; }
    .hp-test-user strong { font-size: .9rem; color: #111827; display: block; }
    .hp-test-user span { font-size: .78rem; color: #6b7280; }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 991.98px) {
        .hp-hero { border-radius: 0 0 1.5rem 1.5rem; }
        .hp-why, .hp-newsletter { padding: 1.75rem 1.25rem; }
        .hp-deals-banner { padding: 1.5rem 1rem; }
        .hp-countdown { width: 100%; }
    }
</style>
@endpush
