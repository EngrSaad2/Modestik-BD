@extends('layouts.admin')

@section('title', 'Sales & Financial ERP Reporting Hub')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Sales & Analytics Hub</li>
@endsection

@section('styles')
<style>
    .erp-kpi-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .erp-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .erp-kpi-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .sticky-filter-bar {
        position: sticky;
        top: 70px;
        z-index: 1020;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .nav-tabs-erp .nav-link {
        color: #64748b;
        font-weight: 600;
        font-size: 13px;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        margin-right: 4px;
        white-space: nowrap;
    }
    .nav-tabs-erp .nav-link.active {
        color: #ffffff;
        background: #d97d8c;
    }
    @media print {
        .sticky-filter-bar, .nav-tabs-erp, .btn-print-hide, .admin-sidebar, header {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-chart-line text-primary me-2"></i>Sales & Analytics ERP Hub</h4>
        <p class="text-muted mb-0" style="font-size: 13px;">Centralized ecommerce intelligence for sales, profit, inventory, customers, and logistics.</p>
    </div>
    <div class="d-flex gap-2 btn-print-hide flex-wrap">
        <a href="{{ route('admin.reports.export', array_merge(['type' => $activeTab == 'overview' ? 'sales' : $activeTab], request()->query())) }}" class="btn btn-sm btn-success fw-bold px-3">
            <i class="fas fa-file-excel me-1"></i>Export {{ ucfirst($activeTab == 'overview' ? 'Sales' : $activeTab) }} Excel
        </a>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-success fw-bold px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-download me-1"></i>Export Reports...
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 13px;">
                <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge(['type' => 'sales'], request()->query())) }}"><i class="fas fa-file-excel text-success me-2"></i>Sales & Orders Report</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge(['type' => 'products'], request()->query())) }}"><i class="fas fa-boxes text-info me-2"></i>Product Sales Report</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge(['type' => 'categories'], request()->query())) }}"><i class="fas fa-folder text-warning me-2"></i>Category Sales Report</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge(['type' => 'customers'], request()->query())) }}"><i class="fas fa-users text-primary me-2"></i>Customer Intelligence Report</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge(['type' => 'inventory'], request()->query())) }}"><i class="fas fa-warehouse text-danger me-2"></i>Inventory Stock Report</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge(['type' => 'financial'], request()->query())) }}"><i class="fas fa-file-invoice-dollar text-success me-2"></i>Financial P&L Report</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('admin.reports.export', array_merge(['type' => 'courier'], request()->query())) }}"><i class="fas fa-truck-loading text-dark me-2"></i>Courier Logistics Report</a></li>
            </ul>
        </div>
        <button onclick="window.print()" class="btn btn-sm btn-outline-dark fw-bold px-3">
            <i class="fas fa-print me-1"></i>Print Report
        </button>
    </div>
</div>

<!-- Sticky Filters Bar -->
<div class="sticky-filter-bar p-3 mb-4 border">
    <form action="{{ route('admin.reports.sales') }}" method="GET" id="erpFilterForm">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light fw-bold"><i class="fas fa-calendar-alt me-1 text-muted"></i>Period</span>
                    <select name="filter" class="form-select form-select-sm" onchange="toggleCustomDates(this.value)">
                        <option value="today" {{ $filter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ $filter == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="last_7_days" {{ $filter == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="last_30_days" {{ $filter == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="this_month" {{ $filter == 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_month" {{ $filter == 'last_month' ? 'selected' : '' }}>Last Month</option>
                        <option value="this_year" {{ $filter == 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $filter == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4 d-flex gap-2" id="customDateContainer" style="{{ $filter == 'custom' ? '' : 'display: none !important;' }}">
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>

            <div class="col-md-4 ms-auto text-end d-flex gap-2 justify-content-end align-items-center">
                <button type="button" class="btn btn-sm btn-outline-secondary px-3 flex-shrink-0" data-bs-toggle="collapse" data-bs-target="#advancedFiltersCollapse" style="white-space: nowrap;">
                    <i class="fas fa-sliders-h me-1"></i>More Filters
                </button>
                <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold flex-shrink-0" style="white-space: nowrap;">
                    <i class="fas fa-search me-1"></i>Apply
                </button>
            </div>
        </div>

        <!-- Collapsible Advanced Filters -->
        <div class="collapse {{ request()->anyFilled(['category_id', 'product_id', 'brand_id', 'district', 'division', 'payment_method', 'payment_status', 'status', 'courier_name']) ? 'show' : '' }} mt-3 pt-3 border-top" id="advancedFiltersCollapse">
            <div class="row g-2">
                <div class="col-md-2">
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categoriesList as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">All Products</option>
                        @foreach($productsList as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_method" class="form-select form-select-sm">
                        <option value="">All Payment Methods</option>
                        <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
                        <option value="bkash" {{ request('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">Payment Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Order Status</option>
                        @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="district" class="form-control form-control-sm" placeholder="District (e.g. Dhaka)" value="{{ request('district') }}">
                </div>
            </div>
            <div class="d-flex justify-content-end mt-2">
                <a href="{{ route('admin.reports.sales') }}" class="btn btn-sm btn-link text-decoration-none text-muted">Reset All Filters</a>
            </div>
        </div>
    </form>
</div>

<!-- 14 KPI Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">TOTAL SALES</span>
                <div class="erp-kpi-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-coins"></i></div>
            </div>
            <h5 class="fw-bold text-dark mb-0">৳{{ number_format($kpis['total_sales']) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">TOTAL ORDERS</span>
                <div class="erp-kpi-icon bg-info bg-opacity-10 text-info"><i class="fas fa-shopping-cart"></i></div>
            </div>
            <h5 class="fw-bold text-dark mb-0">{{ number_format($kpis['total_orders']) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">NET SALES</span>
                <div class="erp-kpi-icon bg-success bg-opacity-10 text-success"><i class="fas fa-chart-line"></i></div>
            </div>
            <h5 class="fw-bold text-dark mb-0">৳{{ number_format($kpis['net_sales']) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">GROSS PROFIT</span>
                <div class="erp-kpi-icon bg-success bg-opacity-10 text-success"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
            <h5 class="fw-bold text-success mb-0">৳{{ number_format($kpis['gross_profit']) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">PRODUCT COST</span>
                <div class="erp-kpi-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-boxes"></i></div>
            </div>
            <h5 class="fw-bold text-dark mb-0">৳{{ number_format($kpis['total_cost']) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">NET PROFIT</span>
                <div class="erp-kpi-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-wallet"></i></div>
            </div>
            <h5 class="fw-bold text-warning mb-0">৳{{ number_format($kpis['net_profit']) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">AVG ORDER VALUE</span>
                <div class="erp-kpi-icon bg-secondary bg-opacity-10 text-secondary"><i class="fas fa-calculator"></i></div>
            </div>
            <h5 class="fw-bold text-dark mb-0">৳{{ number_format($kpis['aov'], 2) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">DELIVERY CHARGES</span>
                <div class="erp-kpi-icon bg-info bg-opacity-10 text-info"><i class="fas fa-truck"></i></div>
            </div>
            <h5 class="fw-bold text-dark mb-0">৳{{ number_format($kpis['delivery_charges']) }}</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">RETURNED ORDERS</span>
                <div class="erp-kpi-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-undo"></i></div>
            </div>
            <h5 class="fw-bold text-danger mb-0">{{ $kpis['returned_orders'] }} (৳{{ number_format($kpis['refund_amount']) }})</h5>
        </div>
    </div>
    <div class="col-6 col-md-3 col-lg-2.4">
        <div class="erp-kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted fw-semibold" style="font-size: 11px;">PENDING PAYMENTS</span>
                <div class="erp-kpi-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-clock"></i></div>
            </div>
            <h5 class="fw-bold text-dark mb-0">৳{{ number_format($kpis['pending_payments']) }}</h5>
        </div>
    </div>
</div>

<!-- ERP Sub-Report Tabs Navigation -->
<ul class="nav nav-tabs nav-tabs-erp mb-4 border-0">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'overview' ? 'active' : '' }}" href="{{ route('admin.reports.sales', array_merge(request()->query(), ['tab' => 'overview'])) }}">
            <i class="fas fa-chart-pie me-1"></i>Analytics & Charts
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'products' ? 'active' : '' }}" href="{{ route('admin.reports.sales', array_merge(request()->query(), ['tab' => 'products'])) }}">
            <i class="fas fa-boxes me-1"></i>Product Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'categories' ? 'active' : '' }}" href="{{ route('admin.reports.sales', array_merge(request()->query(), ['tab' => 'categories'])) }}">
            <i class="fas fa-folder me-1"></i>Category Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'customers' ? 'active' : '' }}" href="{{ route('admin.reports.sales', array_merge(request()->query(), ['tab' => 'customers'])) }}">
            <i class="fas fa-users me-1"></i>Customer Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'inventory' ? 'active' : '' }}" href="{{ route('admin.reports.sales', array_merge(request()->query(), ['tab' => 'inventory'])) }}">
            <i class="fas fa-warehouse me-1"></i>Inventory Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'financial' ? 'active' : '' }}" href="{{ route('admin.reports.sales', array_merge(request()->query(), ['tab' => 'financial'])) }}">
            <i class="fas fa-file-invoice-dollar me-1"></i>Financial P&L
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab == 'courier' ? 'active' : '' }}" href="{{ route('admin.reports.sales', array_merge(request()->query(), ['tab' => 'courier'])) }}">
            <i class="fas fa-truck-loading me-1"></i>Courier Reports
        </a>
    </li>
</ul>

<!-- TAB CONTENT: Overview & Interactive Charts -->
@if($activeTab == 'overview')
<div class="row g-4 mb-4">
    <!-- Daily Sales Trend -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Daily Sales Trend</h6>
                <span class="badge bg-light text-dark" style="font-size:11px;">{{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }}</span>
            </div>
            <div class="card-body p-3" style="height: 320px; position: relative;">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Payment Method Distribution -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-credit-card text-success me-2"></i>Payment Method Distribution</h6>
            </div>
            <div class="card-body p-3" style="height: 320px; position: relative;">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Top Selling Products -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-trophy text-warning me-2"></i>Top Selling Products by Revenue</h6>
            </div>
            <div class="card-body p-3" style="height: 300px; position: relative;">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Categories -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-layer-group text-info me-2"></i>Top Categories Performance</h6>
            </div>
            <div class="card-body p-3" style="height: 300px; position: relative;">
                <canvas id="topCategoriesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Sales by District -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>Geographic Sales by District</h6>
            </div>
            <div class="card-body p-3" style="height: 300px; position: relative;">
                <canvas id="districtSalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Trend & Customer Type -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-user-check text-purple me-2"></i>New vs Returning Customers</h6>
            </div>
            <div class="card-body p-3" style="height: 300px; position: relative;">
                <canvas id="customerTypeChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

<!-- TAB CONTENT: Product Reports -->
@if($activeTab == 'products')
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fas fa-box text-primary me-2"></i>Product Performance & Profitability Breakdown</h6>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark">{{ $productReports->total() }} Products Recorded</span>
            <a href="{{ route('admin.reports.export', array_merge(['type' => 'products'], request()->query())) }}" class="btn btn-sm btn-outline-success fw-bold px-3">
                <i class="fas fa-file-excel me-1"></i>Export Products Excel
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                        <th>Product Cost</th>
                        <th>Gross Profit</th>
                        <th>Profit Margin %</th>
                        <th>Current Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productReports as $p)
                        @php
                            $profit = max(0, $p->revenue - $p->product_cost);
                            $margin = $p->revenue > 0 ? round(($profit / $p->revenue) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td><strong class="text-dark">{{ $p->name }}</strong></td>
                            <td><code>{{ $p->sku ?? 'N/A' }}</code></td>
                            <td><span class="badge bg-light text-dark">{{ $p->category_name ?? 'General' }}</span></td>
                            <td class="fw-bold">{{ number_format($p->units_sold) }}</td>
                            <td class="fw-bold text-dark">৳{{ number_format($p->revenue) }}</td>
                            <td class="text-muted">৳{{ number_format($p->product_cost) }}</td>
                            <td class="fw-bold text-success">৳{{ number_format($profit) }}</td>
                            <td><span class="badge bg-success bg-opacity-10 text-success">{{ $margin }}%</span></td>
                            <td>
                                <span class="badge {{ $p->current_stock > 5 ? 'bg-success' : ($p->current_stock > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $p->current_stock ?? 0 }} units
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No product sales logged in selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center p-3">
            {{ $productReports->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endif

<!-- TAB CONTENT: Category Reports -->
@if($activeTab == 'categories')
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fas fa-folder text-primary me-2"></i>Category Performance & Stock Value</h6>
        <a href="{{ route('admin.reports.export', array_merge(['type' => 'categories'], request()->query())) }}" class="btn btn-sm btn-outline-success fw-bold px-3">
            <i class="fas fa-file-excel me-1"></i>Export Categories Excel
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>Category Name</th>
                        <th>Total Products</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                        <th>Gross Profit</th>
                        <th>Inventory Stock Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryReports as $cat)
                        <tr>
                            <td><strong class="text-dark">{{ $cat->name }}</strong></td>
                            <td><span class="badge bg-light text-dark">{{ $cat->products_count }} items</span></td>
                            <td class="fw-bold">{{ number_format($cat->units_sold) }}</td>
                            <td class="fw-bold text-dark">৳{{ number_format($cat->revenue) }}</td>
                            <td class="fw-bold text-success">৳{{ number_format($cat->profit) }}</td>
                            <td class="fw-bold text-info">৳{{ number_format($cat->stock_value) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- TAB CONTENT: Customer Reports -->
@if($activeTab == 'customers')
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fas fa-users text-primary me-2"></i>Customer Lifetime Value & Analytics</h6>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark">{{ $customerReports->total() }} Customers</span>
            <a href="{{ route('admin.reports.export', array_merge(['type' => 'customers'], request()->query())) }}" class="btn btn-sm btn-outline-success fw-bold px-3">
                <i class="fas fa-file-excel me-1"></i>Export Customers Excel
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone / Email</th>
                        <th>Total Orders</th>
                        <th>Lifetime Spend</th>
                        <th>Average Order Value</th>
                        <th>VIP Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customerReports as $c)
                        <tr>
                            <td><strong class="text-dark">{{ $c->name }}</strong></td>
                            <td>
                                <div><code>{{ $c->phone }}</code></div>
                                <small class="text-muted">{{ $c->email }}</small>
                            </td>
                            <td class="fw-bold">{{ $c->total_orders }} orders</td>
                            <td class="fw-bold text-success">৳{{ number_format($c->lifetime_spend) }}</td>
                            <td class="fw-bold text-dark">৳{{ number_format($c->avg_order_value, 2) }}</td>
                            <td>
                                @if($c->lifetime_spend >= 5000)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-crown me-1"></i>VIP Tier 1</span>
                                @elseif($c->lifetime_spend >= 2000)
                                    <span class="badge bg-info text-white"><i class="fas fa-star me-1"></i>Regular VIP</span>
                                @else
                                    <span class="badge bg-light text-muted">Standard</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No customer analytics logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center p-3">
            {{ $customerReports->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endif

<!-- TAB CONTENT: Inventory Reports -->
@if($activeTab == 'inventory')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold mb-0"><i class="fas fa-warehouse text-primary me-2"></i>Inventory Stock Analysis</h6>
    <a href="{{ route('admin.reports.export', array_merge(['type' => 'inventory'], request()->query())) }}" class="btn btn-sm btn-outline-success fw-bold px-3">
        <i class="fas fa-file-excel me-1"></i>Export Inventory Excel
    </a>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="erp-kpi-card text-center">
            <span class="text-muted fw-semibold" style="font-size:11px;">TOTAL INVENTORY UNITS</span>
            <h4 class="fw-bold text-primary mb-0 mt-2">{{ number_format($inventoryStats['total_stock']) }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="erp-kpi-card text-center">
            <span class="text-muted fw-semibold" style="font-size:11px;">TOTAL INVENTORY VALUE</span>
            <h4 class="fw-bold text-success mb-0 mt-2">৳{{ number_format($inventoryStats['total_inventory_value']) }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="erp-kpi-card text-center">
            <span class="text-muted fw-semibold" style="font-size:11px;">LOW STOCK ITEMS (<=5)</span>
            <h4 class="fw-bold text-warning mb-0 mt-2">{{ $inventoryStats['low_stock_count'] }}</h4>
        </div>
    </div>
    <div class="col-md-3">
        <div class="erp-kpi-card text-center">
            <span class="text-muted fw-semibold" style="font-size:11px;">OUT OF STOCK ITEMS</span>
            <h4 class="fw-bold text-danger mb-0 mt-2">{{ $inventoryStats['out_of_stock_count'] }}</h4>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-success"><i class="fas fa-fire me-2"></i>Fast Moving Products</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($fastMovingProducts as $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <div class="fw-bold text-dark">{{ $p->name }}</div>
                                <small class="text-muted">SKU: {{ $p->sku }}</small>
                            </div>
                            <span class="badge bg-success">{{ $p->quantity }} units left</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-warning"><i class="fas fa-snowflake me-2"></i>Slow Moving / Low View Products</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($slowMovingProducts as $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <div class="fw-bold text-dark">{{ $p->name }}</div>
                                <small class="text-muted">SKU: {{ $p->sku }}</small>
                            </div>
                            <span class="badge bg-warning text-dark">{{ $p->quantity }} units left</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endif

<!-- TAB CONTENT: Financial P&L -->
@if($activeTab == 'financial')
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fas fa-calculator text-primary me-2"></i>Profit & Loss (P&L) Financial Statement</h6>
        <a href="{{ route('admin.reports.export', array_merge(['type' => 'financial'], request()->query())) }}" class="btn btn-sm btn-outline-success fw-bold px-3">
            <i class="fas fa-file-excel me-1"></i>Export P&L Excel
        </a>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <table class="table table-bordered align-middle" style="font-size: 14px;">
                    <tbody>
                        <tr>
                            <td class="fw-semibold">Gross Revenue (Total Sales)</td>
                            <td class="text-end fw-bold text-dark">৳{{ number_format($kpis['total_sales'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">(-) Total Product Cost</td>
                            <td class="text-end text-danger">-৳{{ number_format($kpis['total_cost'], 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="fw-bold">= Gross Profit</td>
                            <td class="text-end fw-bold text-success">৳{{ number_format($kpis['gross_profit'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">(+) Shipping & Delivery Charges Collected</td>
                            <td class="text-end text-info">+৳{{ number_format($kpis['delivery_charges'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">(-) Total Discounts Allowed</td>
                            <td class="text-end text-danger">-৳{{ number_format($kpis['total_discounts'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">(-) Returned Order Refunds</td>
                            <td class="text-end text-danger">-৳{{ number_format($kpis['refund_amount'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">(-) Operating Expenses & Courier Handling</td>
                            <td class="text-end text-danger">-৳{{ number_format($kpis['total_expenses'], 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <td class="fw-bold fs-5 text-dark">= NET OPERATING PROFIT</td>
                            <td class="text-end fw-bold fs-5 text-success">৳{{ number_format($kpis['net_profit'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-lg-4">
                <div class="card border bg-light p-3 rounded-3">
                    <h6 class="fw-bold mb-3"><i class="fas fa-wallet text-warning me-2"></i>Cashflow & Balance Overview</h6>
                    <div class="mb-3">
                        <small class="text-muted d-block">Pending Collections (Unpaid Orders)</small>
                        <h5 class="fw-bold text-dark mb-0">৳{{ number_format($kpis['pending_payments']) }}</h5>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Total Inventory Asset Value</small>
                        <h5 class="fw-bold text-info mb-0">৳{{ number_format($inventoryStats['total_inventory_value']) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- TAB CONTENT: Courier Reports -->
@if($activeTab == 'courier')
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fas fa-truck text-primary me-2"></i>Courier Wise Dispatch & Settlement Report</h6>
        <a href="{{ route('admin.reports.export', array_merge(['type' => 'courier'], request()->query())) }}" class="btn btn-sm btn-outline-success fw-bold px-3">
            <i class="fas fa-file-excel me-1"></i>Export Courier Excel
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>Courier Partner</th>
                        <th>Total Orders Pushed</th>
                        <th>Delivered Orders</th>
                        <th>Returned Orders</th>
                        <th>Delivery Charges Collected</th>
                        <th>Pending COD Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courierReports as $courier)
                        <tr>
                            <td><strong class="text-dark">{{ strtoupper($courier->courier_name ?? 'Unassigned') }}</strong></td>
                            <td class="fw-bold">{{ $courier->total_orders }} orders</td>
                            <td><span class="badge bg-success">{{ $courier->delivered_orders }} delivered</span></td>
                            <td><span class="badge bg-danger">{{ $courier->returned_orders }} returned</span></td>
                            <td class="fw-bold text-dark">৳{{ number_format($courier->total_charges) }}</td>
                            <td class="fw-bold text-warning">৳{{ number_format($courier->cod_pending) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No courier dispatches recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    function toggleCustomDates(val) {
        const container = document.getElementById('customDateContainer');
        if (val === 'custom') {
            container.style.setProperty('display', 'flex', 'important');
        } else {
            container.style.setProperty('display', 'none', 'important');
            document.getElementById('erpFilterForm').submit();
        }
    }

    @if($activeTab == 'overview')
    // 1. Daily Sales Chart
    new Chart(document.getElementById('dailySalesChart'), {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Revenue (BDT)',
                data: @json($dailyValues),
                borderColor: '#d97d8c',
                borderWidth: 3,
                backgroundColor: 'rgba(217, 125, 140, 0.15)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#d97d8c',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { callback: v => '৳' + v.toLocaleString() } }
            }
        }
    });

    // 2. Payment Method Distribution Chart
    new Chart(document.getElementById('paymentMethodChart'), {
        type: 'doughnut',
        data: {
            labels: [@foreach($paymentMethodsChart as $pm) '{{ strtoupper($pm->payment_method) }}', @endforeach],
            datasets: [{
                data: [@foreach($paymentMethodsChart as $pm) {{ $pm->total_orders }}, @endforeach],
                backgroundColor: ['#d97d8c', '#10b981', '#f59e0b', '#3b82f6'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            cutout: '70%'
        }
    });

    // 3. Top Products Chart
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: [@foreach($topSellingProductsChart as $tp) '{{ Str::limit($tp->name, 18) }}', @endforeach],
            datasets: [{
                label: 'Revenue (BDT)',
                data: [@foreach($topSellingProductsChart as $tp) {{ $tp->total_rev }}, @endforeach],
                backgroundColor: '#3b82f6',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 4. Top Categories Chart
    new Chart(document.getElementById('topCategoriesChart'), {
        type: 'bar',
        data: {
            labels: [@foreach($topCategoriesChart as $tc) '{{ $tc->category_name }}', @endforeach],
            datasets: [{
                label: 'Sales (BDT)',
                data: [@foreach($topCategoriesChart as $tc) {{ $tc->total_rev }}, @endforeach],
                backgroundColor: '#10b981',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 5. District Sales Chart
    new Chart(document.getElementById('districtSalesChart'), {
        type: 'bar',
        data: {
            labels: [@foreach($districtSalesChart as $ds) '{{ $ds->district }}', @endforeach],
            datasets: [{
                label: 'Revenue (BDT)',
                data: [@foreach($districtSalesChart as $ds) {{ $ds->total_rev }}, @endforeach],
                backgroundColor: '#f59e0b',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 6. Customer Type Chart
    new Chart(document.getElementById('customerTypeChart'), {
        type: 'pie',
        data: {
            labels: [@foreach($customerTypeChart as $ct) '{{ $ct->customer_type }}', @endforeach],
            datasets: [{
                data: [@foreach($customerTypeChart as $ct) {{ $ct->total_count }}, @endforeach],
                backgroundColor: ['#8b5cf6', '#ec4899'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    @endif
</script>
@endsection
