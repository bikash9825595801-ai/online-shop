@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Customers</h4>
        <span class="text-muted small">{{ $customers->total() }} registered customer(s)</span>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="search" name="search" class="form-control" placeholder="Search customers..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
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
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Orders</th>
                            <th>Total Spent</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($customer->profile_image)
                                            <img src="{{ asset('storage/' . $customer->profile_image) }}" class="avatar-sm" alt="">
                                        @else
                                            <i class="bi bi-person-circle fs-4 text-muted"></i>
                                        @endif
                                        <div>
                                            <div class="small fw-semibold">{{ $customer->name }}</div>
                                            <small class="text-muted">{{ $customer->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">{{ $customer->phone ?? '—' }}</td>
                                <td><span class="badge bg-soft-brand text-brand">{{ $customer->orders_count }}</span></td>
                                <td class="small fw-semibold">${{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge {{ $customer->isActive() ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($customer->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-brand"><i class="bi bi-eye"></i></a>
                                    <form action="{{ route('admin.users.toggle-block', $customer->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm {{ $customer->isBlocked() ? 'btn-outline-success' : 'btn-outline-warning' }}" type="submit"
                                                data-confirm="{{ $customer->isBlocked() ? 'Unblock this customer?' : 'Block this customer?' }}">
                                            <i class="bi {{ $customer->isBlocked() ? 'bi-unlock' : 'bi-lock' }}"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$customers" />
        </div>
    </div>
@endsection
