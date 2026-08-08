@extends('layouts.customer')

@section('title', 'My Support Messages')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">My Messages</h4>
        <a href="{{ route('support.create') }}" class="btn btn-brand btn-sm">New Message</a>
    </div>

    @forelse ($messages as $message)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <strong>{{ $message->subject }}</strong>
                        <small class="text-muted d-block">Sent {{ $message->created_at->format('M d, Y h:i A') }}</small>
                    </div>
                    <span class="badge {{ match($message->status) { 'open' => 'bg-warning text-dark', 'in_progress' => 'bg-primary', 'resolved' => 'bg-success', 'closed' => 'bg-secondary', default => 'bg-secondary' } }}">
                        {{ str_replace('_', ' ', ucfirst($message->status)) }}
                    </span>
                </div>

                <div class="border-start border-3 border-light ps-3 mb-3">
                    <p class="small mb-0">{{ $message->message }}</p>
                </div>

                @if ($message->admin_response)
                    <div class="bg-soft-brand rounded p-3">
                        <small class="fw-semibold text-brand"><i class="bi bi-shield-check me-1"></i>Support Response
                            @if ($message->responded_at)
                                <span class="text-muted">· {{ $message->responded_at->format('M d, Y h:i A') }}</span>
                            @endif
                        </small>
                        <p class="small mb-0 mt-1">{{ $message->admin_response }}</p>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-chat-square-text fs-1 text-muted"></i>
                <h5 class="mt-3">No messages yet</h5>
                <p class="text-muted">Contact support and your messages will appear here.</p>
                <a href="{{ route('support.create') }}" class="btn btn-brand btn-sm">Contact Support</a>
            </div>
        </div>
    @endforelse
@endsection
