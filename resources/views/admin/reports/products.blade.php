@extends('layouts.admin')

@section('title', 'Product Performance & Profit Report')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Product Performance & Profit Report</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Product Wise Sales & Profit Reports</h4>
        <p class="text-muted mb-0" style="font-size:13px;">View actual product-wise profits calculated using selling price minus cost.</p>
    </div>
    <a href="{{ route('admin.reports.export', 'products') }}" class="btn btn-admin-primary"><i class="fas fa-file-csv me-1"></i>Export Catalog Report</a>
</div>

<!-- Filters Bar -->
<div class="form-card mb-4 py-3">
    <form action="{{ route('admin.reports.products') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-md-3">
            <label class="form-label mb-1">Time Period</label>
            <select name="filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $filter == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="last_7_days" {{ $filter == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="last_30_days" {{ $filter == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="this_month" {{ $filter == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $filter == 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ $filter == 'this_year' ? 'selected' : '' }}>This Year</option>
                <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        
        @if($filter == 'custom')
            <div class="col-md-3">
                <label class="form-label mb-1">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3 mt-4 pt-1">
                <button type="submit" class="btn btn-sm btn-primary px-3 w-100">Filter Range</button>
            </div>
        @endif
    </form>
</div>

<div class="row g-4 mb-4">
    <!-- Low Stock Alert Box -->
    <div class="col-lg-6">
        <div class="table-card">
            <div class="card-header bg-danger bg-opacity-10">
                <h5 class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Inventory Stock Alerts</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Quantity Left</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $prod)
                                <tr>
                                    <td><strong>{{ $prod->name }}</strong></td>
                                    <td><code>{{ $prod->sku }}</code></td>
                                    <td>
                                        <span class="badge bg-danger">{{ $prod->quantity }} left</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $prod) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus-circle me-1"></i>Restock</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">All active catalog products have healthy stock levels.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Category performance breakdown -->
    <div class="col-lg-6">
        <div class="table-card">
            <div class="card-header">
                <h5>Category Sales & Profit Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Units Sold</th>
                                <th>Total Revenue</th>
                                <th>Actual Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryPerformance as $cat)
                                <tr>
                                    <td><strong>{{ $cat->category_name }}</strong></td>
                                    <td>{{ $cat->total_sold }} units</td>
                                    <td class="fw-bold text-dark">৳{{ number_format($cat->total_revenue, 2) }}</td>
                                    <td class="fw-bold text-success">৳{{ number_format($cat->category_profit, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No category sales calculated for this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Wise Sales & Profit Table -->
<div class="table-card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Product Wise Sales & Actual Profit Performance ({{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }})</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rank</th>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Units Sold</th>
                        <th>Total Revenue</th>
                        <th>Total Cost</th>
                        <th>Actual Profit</th>
                        <th>Margin %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productStats as $index => $item)
                        @php
                            $margin = $item->total_revenue > 0 ? round(($item->actual_profit / $item->total_revenue) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td><strong>#{{ $index + 1 }}</strong></td>
                            <td><strong>{{ $item->name }}</strong></td>
                            <td><code>{{ $item->sku }}</code></td>
                            <td><span class="badge bg-light text-dark">{{ $item->total_sold }} units</span></td>
                            <td class="fw-bold text-dark">৳{{ number_format($item->total_revenue, 2) }}</td>
                            <td class="text-muted">৳{{ number_format($item->total_cost, 2) }}</td>
                            <td class="fw-bold text-success">৳{{ number_format($item->actual_profit, 2) }}</td>
                            <td><span class="badge bg-success bg-opacity-10 text-success">{{ $margin }}%</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No product sales tracked in this time range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
