@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Dashboard Overview</h4>
        <span class="text-muted small">{{ now()->format('l, F d Y') }}</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $totalCustomers }}</div>
                        <div class="stat-label">Customers</div>
                    </div>
                    <i class="bi bi-people stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $totalSellers }}</div>
                        <div class="stat-label">Sellers</div>
                    </div>
                    <i class="bi bi-shop stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-indigo">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $totalProducts }}</div>
                        <div class="stat-label">Products</div>
                    </div>
                    <i class="bi bi-box-seam stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-orange">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $totalOrders }}</div>
                        <div class="stat-label">Orders</div>
                    </div>
                    <i class="bi bi-receipt stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <i class="bi bi-cash-stack stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $pendingOrders }}</div>
                        <div class="stat-label">Pending Orders</div>
                    </div>
                    <i class="bi bi-hourglass-split stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-teal">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $pendingApprovals }}</div>
                        <div class="stat-label">Pending Approvals</div>
                    </div>
                    <i class="bi bi-patch-question stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-gray">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $lowStockProducts }}</div>
                        <div class="stat-label">Low Stock Items</div>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Sales — Last 7 Days</div>
                <div class="card-body">
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Top Selling Products</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topProducts as $product)
                                <tr>
                                    <td class="small">{{ $product->name }}</td>
                                    <td class="text-center small">{{ $product->total_qty }}</td>
                                    <td class="text-end small">${{ number_format($product->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No sales data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Orders</span>
                    <a href="{{ route('admin.orders.index') }}" class="small text-decoration-none">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td class="small fw-semibold">{{ $order->order_number }}</td>
                                        <td class="small">{{ $order->customer->name }}</td>
                                        <td class="small">${{ number_format($order->total_amount, 2) }}</td>
                                        <td><span class="badge {{ order_status_badge($order->order_status) }}">{{ ucfirst($order->order_status) }}</span></td>
                                        <td><a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-brand">View</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">No orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Recent Users</span>
                    <a href="{{ route('admin.users.index') }}" class="small text-decoration-none">View all</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <tbody>
                                @forelse ($recentUsers as $user)
                                    <tr>
                                        <td class="ps-3 py-2">
                                            @if ($user->profile_image)
                                                <img src="{{ asset('storage/' . $user->profile_image) }}" class="avatar-sm" alt="">
                                            @else
                                                <i class="bi bi-person-circle fs-4 text-muted"></i>
                                            @endif
                                            <span class="small fw-semibold ms-2">{{ $user->name }}</span>
                                        </td>
                                        <td class="text-end py-2">
                                            <span class="badge bg-soft-brand text-brand">{{ ucfirst($user->role) }}</span>
                                            @if ($user->isBlocked())
                                                <span class="badge bg-danger">Blocked</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="text-center text-muted py-4">No users yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartDays),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: @json($chartTotals),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79,70,229,.12)',
                        fill: true,
                        tension: .4,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    });
</script>
@endpush
