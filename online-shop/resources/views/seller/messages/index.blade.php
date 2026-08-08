@extends('layouts.seller')

@section('title', 'Messages')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Notifications</h4>
        <span class="text-muted small">{{ $messages->total() }} notification(s)</span>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse ($messages as $message)
                    <a href="{{ $message->link ? url($message->link) : '#' }}"
                       class="list-group-item list-group-item-action {{ $message->is_read ? '' : 'unread' }} d-flex align-items-center gap-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-semibold">{{ $message->title }}</span>
                                <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="small text-muted mb-0">{{ $message->message }}</p>
                        </div>
                        @if (!$message->is_read)
                            <span class="badge bg-soft-brand text-brand">New</span>
                        @endif
                    </a>
                @empty
                    <div class="text-center text-muted py-5">No notifications yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3">
        <x-pagination :paginator="$messages" />
    </div>
@endsection
