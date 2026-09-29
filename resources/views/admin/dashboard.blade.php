@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <p class="text-muted small mb-0">Overview of your online store metrics</p>
    </div>
    <span class="text-muted small">{{ now()->format('l, d F Y') }}</span>
</div>

<!-- Stats Cards Grid -->
<div class="dashboard-stats-grid">
    <a href="{{ route('admin.reports.sales') }}" class="dashboard-stat-card">
        <div class="stat-card-header">
            <div class="stat-card-meta">
                <span class="stat-label">Total Sales</span>
                <span class="stat-value">৳{{ number_format($stats['total_sales'], 2) }}</span>
            </div>
            <div class="stat-card-icon stat-icon-yellow">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            @if($stats['sales_change'] > 0)
                <span class="stat-trend-badge up">
                    <i class="fas fa-arrow-up"></i> {{ $stats['sales_change'] }}%
                </span>
            @elseif($stats['sales_change'] < 0)
                <span class="stat-trend-badge down">
                    <i class="fas fa-arrow-down"></i> {{ abs($stats['sales_change']) }}%
                </span>
            @else
                <span class="stat-trend-badge neutral">
                    <i class="fas fa-minus" style="font-size: 9px;"></i> 0.0%
                </span>
            @endif
            <span class="stat-trend-text">vs last 30 days</span>
        </div>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="dashboard-stat-card">
        <div class="stat-card-header">
            <div class="stat-card-meta">
                <span class="stat-label">Total Orders</span>
                <span class="stat-value">{{ number_format($stats['total_orders']) }}</span>
            </div>
            <div class="stat-card-icon stat-icon-green">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            @if($stats['orders_change'] > 0)
                <span class="stat-trend-badge up">
                    <i class="fas fa-arrow-up"></i> {{ $stats['orders_change'] }}%
                </span>
            @elseif($stats['orders_change'] < 0)
                <span class="stat-trend-badge down">
                    <i class="fas fa-arrow-down"></i> {{ abs($stats['orders_change']) }}%
                </span>
            @else
                <span class="stat-trend-badge neutral">
                    <i class="fas fa-minus" style="font-size: 9px;"></i> 0.0%
                </span>
            @endif
            <span class="stat-trend-text">vs last 30 days</span>
        </div>
    </a>

    <a href="{{ route('admin.customers.index') }}" class="dashboard-stat-card">
        <div class="stat-card-header">
            <div class="stat-card-meta">
                <span class="stat-label">Total Customers</span>
                <span class="stat-value">{{ number_format($stats['total_customers']) }}</span>
            </div>
            <div class="stat-card-icon stat-icon-purple">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            @if($stats['customers_change'] > 0)
                <span class="stat-trend-badge up">
                    <i class="fas fa-arrow-up"></i> {{ $stats['customers_change'] }}%
                </span>
            @elseif($stats['customers_change'] < 0)
                <span class="stat-trend-badge down">
                    <i class="fas fa-arrow-down"></i> {{ abs($stats['customers_change']) }}%
                </span>
            @else
                <span class="stat-trend-badge neutral">
                    <i class="fas fa-minus" style="font-size: 9px;"></i> 0.0%
                </span>
            @endif
            <span class="stat-trend-text">vs last 30 days</span>
        </div>
    </a>

    <a href="{{ route('admin.products.index') }}" class="dashboard-stat-card">
        <div class="stat-card-header">
            <div class="stat-card-meta">
                <span class="stat-label">Total Products</span>
                <span class="stat-value">{{ number_format($stats['total_products']) }}</span>
            </div>
            <div class="stat-card-icon stat-icon-orange">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            @if($stats['products_change'] > 0)
                <span class="stat-trend-badge up">
                    <i class="fas fa-arrow-up"></i> {{ $stats['products_change'] }}%
                </span>
            @elseif($stats['products_change'] < 0)
                <span class="stat-trend-badge down">
                    <i class="fas fa-arrow-down"></i> {{ abs($stats['products_change']) }}%
                </span>
            @else
                <span class="stat-trend-badge neutral">
                    <i class="fas fa-minus" style="font-size: 9px;"></i> 0.0%
                </span>
            @endif
            <span class="stat-trend-text">vs last 30 days</span>
        </div>
    </a>

    <a href="{{ route('admin.reports.sales') }}" class="dashboard-stat-card">
        <div class="stat-card-header">
            <div class="stat-card-meta">
                <span class="stat-label">Total Earnings</span>
                <span class="stat-value">৳{{ number_format($stats['total_earnings'], 2) }}</span>
            </div>
            <div class="stat-card-icon stat-icon-blue">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="stat-card-footer">
            @if($stats['earnings_change'] > 0)
                <span class="stat-trend-badge up">
                    <i class="fas fa-arrow-up"></i> {{ $stats['earnings_change'] }}%
                </span>
            @elseif($stats['earnings_change'] < 0)
                <span class="stat-trend-badge down">
                    <i class="fas fa-arrow-down"></i> {{ abs($stats['earnings_change']) }}%
                </span>
            @else
                <span class="stat-trend-badge neutral">
                    <i class="fas fa-minus" style="font-size: 9px;"></i> 0.0%
                </span>
            @endif
            <span class="stat-trend-text">vs last 30 days</span>
        </div>
    </a>
</div>

<!-- Courier Logistics Overview Cards Grid -->
<div class="row g-3 mb-4 mt-2">
    <div class="col-12">
        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-shipping-fast text-primary me-2"></i>SteadFast Courier Logistics Overview</h6>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <span class="text-muted d-block" style="font-size:11px;">Pending Courier</span>
            <strong class="fs-4 text-warning">{{ number_format($courierStats['pending_courier']) }}</strong>
            <small class="text-muted" style="font-size:10px;">Unpushed orders</small>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <span class="text-muted d-block" style="font-size:11px;">Courier Assigned</span>
            <strong class="fs-4 text-info">{{ number_format($courierStats['courier_assigned']) }}</strong>
            <small class="text-muted" style="font-size:10px;">Consignment created</small>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <span class="text-muted d-block" style="font-size:11px;">In Transit</span>
            <strong class="fs-4 text-primary">{{ number_format($courierStats['in_transit']) }}</strong>
            <small class="text-muted" style="font-size:10px;">On delivery route</small>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <span class="text-muted d-block" style="font-size:11px;">Delivered</span>
            <strong class="fs-4 text-success">{{ number_format($courierStats['delivered']) }}</strong>
            <small class="text-muted" style="font-size:10px;">Completed parcels</small>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
            <span class="text-muted d-block" style="font-size:11px;">Total COD Pending</span>
            <strong class="fs-4 text-dark">৳{{ number_format($courierStats['total_cod']) }}</strong>
            <small class="text-muted" style="font-size:10px;">Collectable amount</small>
        </div>
    </div>
</div>

<!-- Charts Section Row -->
<div class="dashboard-charts-row">
    <!-- Chart 1: Sales Overview -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h5>Sales Overview</h5>
            <select>
                <option>Last 30 Days</option>
            </select>
        </div>
        <div class="dashboard-card-body">
            <canvas id="salesOverviewChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Orders Overview -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h5>Orders Overview</h5>
        </div>
        <div class="dashboard-card-body">
            <div class="doughnut-container">
                <div class="doughnut-chart-wrapper">
                    <canvas id="ordersOverviewChart"></canvas>
                </div>
                <ul class="doughnut-legend-list">
                    <li class="doughnut-legend-item">
                        <span class="doughnut-legend-label">
                            <span class="doughnut-legend-bullet bullet-completed"></span>
                            Completed
                        </span>
                        <span class="doughnut-legend-value">
                            {{ $orderBreakdown['completed']['count'] }} ({{ $orderBreakdown['completed']['percentage'] }}%)
                        </span>
                    </li>
                    <li class="doughnut-legend-item">
                        <span class="doughnut-legend-label">
                            <span class="doughnut-legend-bullet bullet-processing"></span>
                            Processing
                        </span>
                        <span class="doughnut-legend-value">
                            {{ $orderBreakdown['processing']['count'] }} ({{ $orderBreakdown['processing']['percentage'] }}%)
                        </span>
                    </li>
                    <li class="doughnut-legend-item">
                        <span class="doughnut-legend-label">
                            <span class="doughnut-legend-bullet bullet-on-hold"></span>
                            On Hold
                        </span>
                        <span class="doughnut-legend-value">
                            {{ $orderBreakdown['on_hold']['count'] }} ({{ $orderBreakdown['on_hold']['percentage'] }}%)
                        </span>
                    </li>
                    <li class="doughnut-legend-item">
                        <span class="doughnut-legend-label">
                            <span class="doughnut-legend-bullet bullet-cancelled"></span>
                            Cancelled
                        </span>
                        <span class="doughnut-legend-value">
                            {{ $orderBreakdown['cancelled']['count'] }} ({{ $orderBreakdown['cancelled']['percentage'] }}%)
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Website Overview -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h5>Website Overview</h5>
        </div>
        <div class="dashboard-card-body">
            <ul class="website-overview-list">
                <!-- Row 1: Total Visitors -->
                <li class="website-overview-item">
                    <div class="website-item-left">
                        <div class="website-item-icon" style="background: rgba(255, 199, 44, 0.1); color: #d97706;">
                            <i class="far fa-user-circle"></i>
                        </div>
                        <div class="website-item-meta">
                            <span class="website-item-label">Total Visitors</span>
                            <span class="website-item-value">{{ $websiteStats['visitors'] }}</span>
                        </div>
                    </div>
                    <span class="website-item-trend {{ $websiteStats['visitors_change'] > 0 ? 'website-trend-up' : ($websiteStats['visitors_change'] < 0 ? 'website-trend-down' : 'website-trend-neutral') }}">
                        <i class="fas {{ $websiteStats['visitors_change'] > 0 ? 'fa-arrow-up' : ($websiteStats['visitors_change'] < 0 ? 'fa-arrow-down' : 'fa-minus') }}" style="{{ $websiteStats['visitors_change'] == 0 ? 'font-size:9px;' : '' }}"></i>
                        {{ abs((float) $websiteStats['visitors_change']) }}%
                    </span>
                </li>

                <!-- Row 2: Page Views -->
                <li class="website-overview-item">
                    <div class="website-item-left">
                        <div class="website-item-icon" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                            <i class="far fa-file-alt"></i>
                        </div>
                        <div class="website-item-meta">
                            <span class="website-item-label">Page Views</span>
                            <span class="website-item-value">{{ $websiteStats['page_views'] }}</span>
                        </div>
                    </div>
                    <span class="website-item-trend {{ $websiteStats['page_views_change'] > 0 ? 'website-trend-up' : ($websiteStats['page_views_change'] < 0 ? 'website-trend-down' : 'website-trend-neutral') }}">
                        <i class="fas {{ $websiteStats['page_views_change'] > 0 ? 'fa-arrow-up' : ($websiteStats['page_views_change'] < 0 ? 'fa-arrow-down' : 'fa-minus') }}" style="{{ $websiteStats['page_views_change'] == 0 ? 'font-size:9px;' : '' }}"></i>
                        {{ abs((float) $websiteStats['page_views_change']) }}%
                    </span>
                </li>

                <!-- Row 3: Bounce Rate -->
                <li class="website-overview-item">
                    <div class="website-item-left">
                        <div class="website-item-icon" style="background: rgba(168, 85, 247, 0.1); color: #9333ea;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="website-item-meta">
                            <span class="website-item-label">Bounce Rate</span>
                            <span class="website-item-value">{{ $websiteStats['bounce_rate'] }}</span>
                        </div>
                    </div>
                    <span class="website-item-trend {{ $websiteStats['bounce_rate_change'] < 0 ? 'website-trend-up' : ($websiteStats['bounce_rate_change'] > 0 ? 'website-trend-down' : 'website-trend-neutral') }}">
                        <i class="fas {{ $websiteStats['bounce_rate_change'] < 0 ? 'fa-arrow-down' : ($websiteStats['bounce_rate_change'] > 0 ? 'fa-arrow-up' : 'fa-minus') }}" style="{{ $websiteStats['bounce_rate_change'] == 0 ? 'font-size:9px;' : '' }}"></i>
                        {{ abs((float) $websiteStats['bounce_rate_change']) }}%
                    </span>
                </li>

                <!-- Row 4: Avg Session -->
                <li class="website-overview-item">
                    <div class="website-item-left">
                        <div class="website-item-icon" style="background: rgba(34, 197, 94, 0.1); color: #16a34a;">
                            <i class="far fa-clock"></i>
                        </div>
                        <div class="website-item-meta">
                            <span class="website-item-label">Average Session</span>
                            <span class="website-item-value">{{ $websiteStats['avg_session'] }}</span>
                        </div>
                    </div>
                    <span class="website-item-trend {{ $websiteStats['avg_session_change'] > 0 ? 'website-trend-up' : ($websiteStats['avg_session_change'] < 0 ? 'website-trend-down' : 'website-trend-neutral') }}">
                        <i class="fas {{ $websiteStats['avg_session_change'] > 0 ? 'fa-arrow-up' : ($websiteStats['avg_session_change'] < 0 ? 'fa-arrow-down' : 'fa-minus') }}" style="{{ $websiteStats['avg_session_change'] == 0 ? 'font-size:9px;' : '' }}"></i>
                        {{ abs((float) $websiteStats['avg_session_change']) }}%
                    </span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Order Sources & Channels Analytics Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-pie me-2 text-primary"></i>Sales & Order Sources Analytics</h5>
        <span class="badge bg-light text-muted border font-monospace">Channel Breakdown</span>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            @foreach($sourceStats as $srcKey => $s)
                <div class="col-md-2 col-6">
                    <div class="p-3 bg-light rounded-4 border text-center h-100">
                        <div class="fs-4 mb-1"><i class="{{ $s['icon'] }}"></i></div>
                        <span class="text-xs fw-bold text-uppercase d-block text-muted">{{ $s['label'] }}</span>
                        <h5 class="fw-extrabold mb-1 text-dark">{{ number_format($s['count']) }} orders</h5>
                        <small class="text-success fw-bold font-monospace">৳{{ number_format($s['revenue'], 0) }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Orders Table Card -->
<div class="table-card">
    <div class="card-header">
        <h5>Recent Orders</h5>
        <a href="{{ route('admin.orders.index') }}" class="btn-view-all">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Channel</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td><strong>#{{ $order->order_number }}</strong></td>
                        <td>{!! $order->source_badge !!}</td>
                        <td>
                            <div class="user-display-cell">
                                <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($order->email ?? ''))) }}?d=mp&s=60" alt="" class="user-display-avatar">
                                <span class="user-display-name">{{ $order->name }}</span>
                            </div>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="fw-bold">৳{{ number_format($order->total, 2) }}</td>
                        <td>
                            @php
                                $statusClass = match($order->status) {
                                    'delivered', 'shipped' => 'completed',
                                    'processing', 'confirmed' => 'processing',
                                    'pending' => 'on-hold',
                                    'cancelled', 'returned' => 'cancelled',
                                    default => 'on-hold'
                                };
                            @endphp
                            <span class="status-pill {{ $statusClass }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>
                            @php
                                $paymentClass = match($order->payment_status) {
                                    'paid' => 'paid',
                                    'pending' => 'processing',
                                    'failed', 'unpaid' => 'cancelled',
                                    default => 'on-hold'
                                };
                            @endphp
                            <span class="status-pill {{ $paymentClass }}">
                                {{ $order->payment_status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-circle-action" title="View Order">
                                <i class="far fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No recent orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Sales Overview Line Chart
    const salesCtx = document.getElementById('salesOverviewChart').getContext('2d');
    const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 300);
    salesGradient.addColorStop(0, 'rgba(255, 199, 44, 0.25)');
    salesGradient.addColorStop(1, 'rgba(255, 199, 44, 0.00)');

    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Sales (৳)',
                data: @json($chartData),
                borderColor: '#FFC72C',
                borderWidth: 3,
                backgroundColor: salesGradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#FFC72C',
                pointBorderWidth: 2.5,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    borderRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Sales: ৳' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 10, weight: 600 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        color: '#94a3b8',
                        font: { family: 'Plus Jakarta Sans', size: 10, weight: 600 },
                        callback: v => '৳' + v.toLocaleString()
                    }
                }
            }
        }
    });

    // Orders Overview Doughnut Chart
    @php
        $totalDoughnutOrders = $orderBreakdown['completed']['count'] + $orderBreakdown['processing']['count'] + $orderBreakdown['on_hold']['count'] + $orderBreakdown['cancelled']['count'];
    @endphp
    const ordersCtx = document.getElementById('ordersOverviewChart').getContext('2d');
    @if($totalDoughnutOrders > 0)
    new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Processing', 'On Hold', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $orderBreakdown['completed']['count'] }},
                    {{ $orderBreakdown['processing']['count'] }},
                    {{ $orderBreakdown['on_hold']['count'] }},
                    {{ $orderBreakdown['cancelled']['count'] }}
                ],
                backgroundColor: ['#22c55e', '#ffc72c', '#2563eb', '#ef4444'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '75%'
        }
    });
    @else
    new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
            labels: ['No Orders Recorded'],
            datasets: [{
                data: [1],
                backgroundColor: ['#e2e8f0'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            cutout: '75%'
        }
    });
    @endif
});
</script>
@endsection
