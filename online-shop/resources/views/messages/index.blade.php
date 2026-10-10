@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    <div class="container mt-4" style="max-width:860px">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="section-title mb-0"><i class="bi bi-chat-dots me-2 text-brand"></i>Messages</h4>
            <span class="text-muted small">{{ $conversations->count() }} conversation(s)</span>
        </div>

        <div class="card">
            <div class="card-body p-0">
                @forelse ($conversations as $conversation)
                    @php
                        $other = $conversation->otherUser(auth()->user());
                        $last = $conversation->lastMessage->first();
                        $unread = $conversation->unreadCountFor(auth()->id());
                    @endphp
                    <a href="{{ route('messages.show', $conversation->id) }}"
                       class="d-flex align-items-center gap-3 text-decoration-none text-dark border-bottom px-3 py-3 {{ $unread > 0 ? 'bg-soft-brand' : '' }}"
                       style="transition: background .15s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                        <div class="position-relative">
                            @if ($other->profile_image)
                                <img src="{{ asset('storage/' . $other->profile_image) }}" class="avatar" alt="">
                            @else
                                <div class="avatar bg-soft-brand text-brand d-flex align-items-center justify-content-center fw-bold">
                                    {{ strtoupper(substr($other->name, 0, 1)) }}
                                </div>
                            @endif
                            @if ($unread > 0)
                                <span class="nav-badge" style="top:-4px;right:-4px">{{ $unread }}</span>
                            @endif
                        </div>

                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">{{ $other->name }}</span>
                                <small class="text-muted">{{ $last?->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted text-truncate">{{ $last?->message ?? 'No messages yet' }}</span>
                                @if ($conversation->order)
                                    <span class="badge bg-light text-dark border ms-2" style="font-size:.68rem">#{{ $conversation->order->order_number }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-dots d-block mb-2" style="font-size:2.5rem"></i>
                        No conversations yet. Order a product to start chatting with the seller.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection