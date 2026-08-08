@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">All Users</h4>
        <a href="{{ route('admin.sellers.create') }}" class="btn btn-brand btn-sm"><i class="bi bi-plus-lg me-1"></i> Add Seller</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-5">
                    <input type="search" name="search" class="form-control" placeholder="Search by name, email or phone..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-select">
                        <option value="">All roles</option>
                        @foreach (['admin', 'seller', 'customer'] as $role)
                            <option value="{{ $role }}" @selected(request('role') == $role)>{{ ucfirst($role) }}s</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All status</option>
                        @foreach (['active', 'blocked'] as $status)
                            <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-brand w-100" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($user->profile_image)
                                            <img src="{{ asset('storage/' . $user->profile_image) }}" class="avatar-sm" alt="">
                                        @else
                                            <i class="bi bi-person-circle fs-4 text-muted"></i>
                                        @endif
                                        <div>
                                            <div class="small fw-semibold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $user->phone ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $user->isAdmin() ? 'bg-danger' : ($user->isSeller() ? 'bg-primary' : 'bg-success') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $user->isActive() ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($user->status) }}</span>
                                </td>
                                <td class="small text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-brand"><i class="bi bi-eye"></i></a>
                                    @if (! $user->isAdmin())
                                        <form action="{{ route('admin.users.toggle-block', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm {{ $user->isBlocked() ? 'btn-outline-success' : 'btn-outline-warning' }}" type="submit"
                                                    data-confirm="{{ $user->isBlocked() ? 'Unblock this user?' : 'Block this user?' }}">
                                                <i class="bi {{ $user->isBlocked() ? 'bi-unlock' : 'bi-lock' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit" data-confirm="Delete this user? This cannot be undone.">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$users" />
        </div>
    </div>
@endsection
