@extends('layouts.seller')

@section('title', 'Seller Dashboard')

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
                        <div class="stat-value">{{ $totalProducts }}</div>
                        <div class="stat-label">Products</div>
                    </div>
                    <i class="bi bi-box-seam stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">${{ number_format($revenue, 2) }}</div>
                        <div class="stat-label">Revenue</div>
                    </div>
                    <i class="bi bi-cash-stack stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-indigo">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $totalSales }}</div>
                        <div class="stat-label">Units Sold</div>
                    </div>
                    <i class="bi bi-cart-check stat-icon"></i>
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
            <div class="stat-card stat-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $approvedProducts }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                    <i class="bi bi-check-circle stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-yellow">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $pendingApprovals }}</div>
                        <div class="stat-label">Pending Approvals</div>
                    </div>
                    <i class="bi bi-hourglass-split stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $rejectedProducts }}</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                    <i class="bi bi-x-circle stat-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-gray">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value">{{ $lowStockProducts }}</div>
                        <div class="stat-label">Low Stock (≤ {{ $threshold }})</div>
                    </div>
                    <i class="bi bi-exclamation-triangle stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Sales — Last 7 Days</div>
                <div class="card-body">
                    <canvas id="salesChart" height="110"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Recent Orders</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td class="small"><a href="{{ route('seller.orders.show', $order->id) }}" class="text-decoration-none">{{ $order->order_number }}</a></td>
                                        <td class="small">${{ number_format($order->total_amount, 2) }}</td>
                                        <td><span class="badge {{ order_status_badge($order->order_status) }}">{{ ucfirst($order->order_status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No orders yet.</td></tr>
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
