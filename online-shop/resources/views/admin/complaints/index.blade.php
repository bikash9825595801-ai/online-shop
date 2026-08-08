@extends('layouts.admin')

@section('title', 'Complaints')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Complaints & Support</h4>
        <span class="text-muted small">{{ $messages->total() }} message(s)</span>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.complaints.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="search" name="search" class="form-control" placeholder="Search by subject or customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach (App\Models\SupportMessage::STATUSES as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-brand w-100" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Subject</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Received</th>
                            <th>Responded</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($messages as $message)
                            <tr>
                                <td class="small fw-semibold" style="min-width:200px">{{ $message->subject }}</td>
                                <td class="small">{{ $message->user->name }}</td>
                                <td>
                                    <span class="badge {{ match($message->status) { 'open' => 'bg-danger', 'in_progress' => 'bg-warning text-dark', 'resolved' => 'bg-success', 'closed' => 'bg-secondary', default => 'bg-secondary' } }}">
                                        {{ ucfirst(str_replace('_', ' ', $message->status)) }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $message->created_at->format('M d, Y') }}</td>
                                <td class="small text-muted">{{ $message->responded_at?->format('M d, Y') ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.complaints.show', $message->id) }}" class="btn btn-sm btn-outline-brand"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No messages found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$messages" />
        </div>
    </div>
@endsection
