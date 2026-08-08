@extends('layouts.customer')

@section('title', 'Notifications')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Notifications</h4>
        @if ($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-brand btn-sm">
                    <i class="bi bi-check2-all me-1"></i> Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="card-body p-0">
            @forelse ($notifications as $notification)
                <a href="{{ $notification->link ? url($notification->link) : '#' }}"
                   class="notif-item d-flex justify-content-between align-items-center {{ $notification->is_read ? '' : 'unread' }}"
                   onclick="event.preventDefault(); fetch('{{ route('notifications.read', $notification->id) }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>window.location.href=this.href);">
                    <div>
                        <div class="small fw-semibold">
                            @if ($notification->is_read)
                                <i class="bi bi-check-circle text-success me-1"></i>
                            @else
                                <i class="bi bi-envelope-fill text-brand me-1"></i>
                            @endif
                            {{ $notification->title }}
                        </div>
                        <div class="small text-muted">{{ $notification->message }}</div>
                    </div>
                    <small class="notif-time text-nowrap ms-3">{{ $notification->created_at->diffForHumans() }}</small>
                </a>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-bell-slash fs-1"></i>
                    <p class="mt-3 mb-0">No notifications yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        <x-pagination :paginator="$notifications" />
    </div>
@endsection
