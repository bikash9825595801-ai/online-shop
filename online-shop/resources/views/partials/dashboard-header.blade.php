<header class="bg-white border-bottom px-4 py-2 d-flex align-items-center justify-content-between sticky-top" style="z-index:1020">
    <div class="d-flex align-items-center gap-2">
        <h6 class="mb-0 fw-semibold">{{ $dashboardTitle }}</h6>
        <span class="badge bg-light text-dark border d-none d-sm-inline">Hello, {{ auth()->user()->name }}</span>
    </div>

    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm d-none d-md-inline-flex">
            <i class="bi bi-house-door me-1"></i> Store
        </a>

        @if (auth()->user()->isCustomer() || auth()->user()->isSeller())
            <a href="{{ route('messages.index') }}" class="nav-icon d-inline-flex align-items-center" title="Messages">
                <i class="bi bi-chat-dots fs-5"></i>
                @if (isset($messageCount) && $messageCount > 0)
                    <span class="nav-badge">{{ $messageCount }}</span>
                @endif
            </a>
        @endif

        <div class="dropdown">
            <a href="#" class="nav-icon d-inline-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                <i class="bi bi-bell fs-5"></i>
                @if ($unreadCount > 0)
                    <span class="nav-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">
                    <strong class="small">Notifications</strong>
                    <a href="{{ route('notifications.index') }}" class="small text-decoration-none">View all</a>
                </div>
                @forelse ($notifications as $notification)
                    <a href="{{ $notification->link ? url($notification->link) : route('notifications.index') }}"
                       class="notif-item {{ $notification->is_read ? '' : 'unread' }}"
                       onclick="event.preventDefault(); fetch('{{ route('notifications.read', $notification->id) }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>window.location.href=this.href);">
                        <div class="small fw-semibold">{{ $notification->title }}</div>
                        <div class="small text-muted">{{ $notification->message }}</div>
                        <div class="notif-time">{{ $notification->created_at->diffForHumans() }}</div>
                    </a>
                @empty
                    <div class="text-center text-muted py-4 small">No notifications yet</div>
                @endforelse
            </div>
        </div>

        <div class="dropdown">
            <a href="#" class="nav-icon d-inline-flex align-items-center" data-bs-toggle="dropdown" title="Account">
                @if (auth()->user()->profile_image)
                    <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" class="avatar-sm" alt="">
                @else
                    <i class="bi bi-person-circle fs-4"></i>
                @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-muted">{{ auth()->user()->email }}</span></li>
                <li><hr class="dropdown-divider"></li>
                @if (auth()->user()->isSeller())
                    <li><a class="dropdown-item" href="{{ route('seller.profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                @elseif (auth()->user()->isCustomer())
                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                @endif
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
