@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Audit Logs</h4>
        <span class="text-muted small">{{ $logs->total() }} log(s)</span>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" name="search" class="form-control" placeholder="Search description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="module" class="form-select">
                        <option value="">All modules</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module }}" @selected(request('module') == $module)>{{ $module }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="action" class="form-control" placeholder="Action (e.g. created)" value="{{ request('action') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-brand w-100" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="small text-muted text-nowrap">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                <td class="small">{{ $log->user->name ?? 'System' }}</td>
                                <td><span class="badge bg-soft-brand text-brand">{{ $log->module }}</span></td>
                                <td class="small">{{ $log->action }}</td>
                                <td class="small text-muted">{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No audit logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$logs" />
        </div>
    </div>
@endsection
