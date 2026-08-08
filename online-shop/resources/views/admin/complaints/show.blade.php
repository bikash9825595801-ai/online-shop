@extends('layouts.admin')

@section('title', $message->subject)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Complaint #{{ $message->id }}</h4>
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Message</div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $message->subject }}</strong></p>
                    <span class="badge {{ match($message->status) { 'open' => 'bg-danger', 'in_progress' => 'bg-warning text-dark', 'resolved' => 'bg-success', 'closed' => 'bg-secondary', default => 'bg-secondary' } }}">
                        {{ ucfirst(str_replace('_', ' ', $message->status)) }}
                    </span>
                    <hr>
                    <p class="small text-muted mb-1">From: <strong>{{ $message->user->name }}</strong> ({{ $message->user->email }})</p>
                    <p class="small text-muted">Received: {{ $message->created_at->format('M d, Y H:i') }}</p>
                    <p class="mb-0">{{ $message->message }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">Respond</div>
                <div class="card-body">
                    @if ($message->admin_response)
                        <div class="alert alert-light border">
                            <div class="small text-muted mb-1">
                                Responded by <strong>{{ $message->responder->name ?? 'Admin' }}</strong> on {{ $message->responded_at?->format('M d, Y H:i') }}
                            </div>
                            <p class="mb-0">{{ $message->admin_response }}</p>
                        </div>
                    @endif

                    <form action="{{ route('admin.complaints.respond', $message->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Response</label>
                            <textarea name="admin_response" rows="4" class="form-control" placeholder="Write your response to the customer..." required>{{ old('admin_response') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach (App\Models\SupportMessage::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($message->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-brand"><i class="bi bi-send me-1"></i> Send Response</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
