@extends('layouts.seller')

@section('title', 'Sales Reports')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Sales Reports</h4>
        <div class="btn-group">
            @foreach (['daily', 'weekly', 'monthly'] as $p)
                <a href="{{ route('seller.reports.index', ['period' => $p]) }}"
                   class="btn btn-sm {{ $period === $p ? 'btn-brand' : 'btn-outline-brand' }}">{{ ucfirst($p) }}</a>
            @endforeach
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Revenue</div>
                    <h5 class="mb-0">${{ number_format($periodRevenue, 2) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Orders</div>
                    <h5 class="mb-0">{{ number_format($periodOrders) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Units Sold</div>
                    <h5 class="mb-0">{{ number_format($periodItems) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Avg. Order Value</div>
                    <h5 class="mb-0">${{ number_format($periodAvgOrder, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Sales Overview</div>
        <div class="card-body">
            <canvas id="salesChart" height="100"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Product Performance</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productWise as $item)
                            <tr>
                                <td class="small">{{ $item->name }}</td>
                                <td class="text-center small">{{ $item->total_qty }}</td>
                                <td class="text-end small">${{ number_format($item->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No sales in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const days = @json($chartDays);
        const revenue = @json($chartRevenue);
        const orders = @json($chartOrders);

        new Chart(document.getElementById('salesChart'), {
            data: {
                labels: days,
                datasets: [
                    {
                        label: 'Revenue ($)',
                        type: 'bar',
                        data: revenue,
                        backgroundColor: 'rgba(13, 110, 253, 0.6)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Orders',
                        type: 'line',
                        data: orders,
                        borderColor: '#20c997',
                        backgroundColor: 'rgba(32, 201, 151, 0.1)',
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, position: 'left' },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
                }
            }
        });
    </script>
@endpush
