@extends('layouts.app')

@section('title', 'Messages')

@section('content')
    @php
        $me = auth()->user();
        $other = $conversation->otherUser($me);
    @endphp

    <div class="container mt-4" style="max-width:860px">
        <div class="card chat-card">
            {{-- Header --}}
            <div class="card-header d-flex align-items-center gap-3 chat-header">
                <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
                @if ($other->profile_image)
                    <img src="{{ asset('storage/' . $other->profile_image) }}" class="avatar-sm" alt="">
                @else
                    <div class="avatar-sm bg-soft-brand text-brand d-flex align-items-center justify-content-center fw-bold">
                        {{ strtoupper(substr($other->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold text-truncate">{{ $other->name }}</div>
                    <small class="text-muted">{{ $other->isSeller() ? 'Verified Seller' : 'Customer' }}</small>
                </div>
                @if ($conversation->order)
                    <a href="{{ $me->isSeller() ? route('seller.orders.show', $conversation->order->id) : route('orders.show', $conversation->order->id) }}" class="btn btn-sm btn-outline-brand">
                        <i class="bi bi-receipt me-1"></i> {{ $conversation->order->order_number }}
                    </a>
                @endif
            </div>

            {{-- Thread --}}
            <div class="card-body chat-thread">
                @forelse ($messages as $message)
                    @php $mine = $message->sender_id === $me->id; @endphp
                    <div class="d-flex {{ $mine ? 'justify-content-end' : 'justify-content-start' }} chat-row">
                        <div class="chat-bubble {{ $mine ? 'chat-bubble-mine' : 'chat-bubble-other' }} {{ $message->isRead() ? '' : 'chat-unread' }}">
                            <div class="chat-text">{{ $message->message }}</div>
                            <div class="chat-meta">
                                {{ $message->created_at->format('M d, H:i') }}
                                @if ($mine)
                                    <i class="bi {{ $message->isRead() ? 'bi-check2-all text-info' : 'bi-check2' }}"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-dots d-block mb-2" style="font-size:2rem"></i>
                        Say hello to start the conversation.
                    </div>
                @endforelse
            </div>

            {{-- Composer --}}
            <div class="card-footer chat-composer">
                <form action="{{ route('messages.send', $conversation->id) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="message" class="form-control" placeholder="Type a message..." required autofocus
                           value="{{ old('message') }}">
                    <button type="submit" class="btn btn-brand px-4"><i class="bi bi-send me-1"></i> Send</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .chat-card { border-radius: 1rem; overflow: hidden; }
        .chat-header { background: #fff; }
        .chat-thread {
            height: 460px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            padding: 1.25rem;
        }
        .chat-row { max-width: 78%; }
        .chat-bubble {
            border-radius: .9rem;
            padding: .6rem .9rem;
            max-width: 100%;
            box-shadow: 0 1px 2px rgba(15,23,42,.06);
        }
        .chat-bubble-mine {
            background: var(--brand);
            color: #fff;
            border-bottom-right-radius: .25rem;
        }
        .chat-bubble-other {
            background: #fff;
            color: #111827;
            border: 1px solid #eef0f4;
            border-bottom-left-radius: .25rem;
        }
        .chat-text { white-space: pre-wrap; word-break: break-word; }
        .chat-meta {
            font-size: .68rem;
            margin-top: .3rem;
            opacity: .75;
            text-align: right;
        }
        .chat-unread { outline: 2px solid rgba(79,70,229,.25); }
        .chat-composer { background: #fff; }
    </style>
@endpush

@push('scripts')
    <script>
        const thread = document.querySelector('.chat-thread');
        if (thread) {
            thread.scrollTop = thread.scrollHeight;
        }
    </script>
@endpush