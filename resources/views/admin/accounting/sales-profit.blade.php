@extends('layouts.admin')

@section('title', 'Sales and Profit')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Range Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-chart-line text-primary me-2"></i>Sales & Profit Analysis</h4>
                <p class="text-muted mb-0 small">Comprehensive breakdown of sales, product costs, operating expenses, and net profit</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.accounting.sales-profit', ['range' => 'daily']) }}" class="btn btn-sm {{ $range === 'daily' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-3">Daily</a>
                <a href="{{ route('admin.accounting.sales-profit', ['range' => 'weekly']) }}" class="btn btn-sm {{ $range === 'weekly' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-3">Weekly</a>
                <a href="{{ route('admin.accounting.sales-profit', ['range' => 'monthly']) }}" class="btn btn-sm {{ $range === 'monthly' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-3">Monthly</a>
                <a href="{{ route('admin.accounting.sales-profit', ['range' => 'yearly']) }}" class="btn btn-sm {{ $range === 'yearly' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-3">Yearly</a>
            </div>
        </div>
    </div>

    <!-- Custom Date Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.accounting.sales-profit') }}" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="range" value="custom">
                <div class="col-md-4">
                    <label class="form-label text-xs fw-bold text-muted">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-xs fw-bold text-muted">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100"><i class="fas fa-filter me-1"></i>Apply Custom Range</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <span class="text-muted text-xs fw-bold text-uppercase">Total Sales</span>
                <h4 class="fw-extrabold text-primary mb-0 mt-2">৳{{ number_format($summary['total_sales'], 0) }}</h4>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <span class="text-muted text-xs fw-bold text-uppercase">Product Cost (COGS)</span>
                <h4 class="fw-extrabold text-danger mb-0 mt-2">৳{{ number_format($summary['product_cost'], 0) }}</h4>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <span class="text-muted text-xs fw-bold text-uppercase">Gross Profit</span>
                <h4 class="fw-extrabold text-info mb-0 mt-2">৳{{ number_format($summary['gross_profit'], 0) }}</h4>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <span class="text-muted text-xs fw-bold text-uppercase">Other Expenses</span>
                <h4 class="fw-extrabold text-secondary mb-0 mt-2">৳{{ number_format($summary['other_expenses'], 0) }}</h4>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-success text-white">
                <span class="text-white-50 text-xs fw-bold text-uppercase">Net Profit</span>
                <h4 class="fw-extrabold text-white mb-0 mt-2">৳{{ number_format($summary['net_profit'], 0) }}</h4>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center bg-dark text-white">
                <span class="text-white-50 text-xs fw-bold text-uppercase">Profit Margin</span>
                <h4 class="fw-extrabold text-warning mb-0 mt-2">{{ $summary['profit_margin'] }}%</h4>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Sales vs Expenses & Profit Comparison</h5>
        </div>
        <div class="card-body p-4">
            <canvas id="salesProfitChart" height="320"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('salesProfitChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [
                    {
                        label: 'Sales',
                        data: {!! json_encode($salesArr) !!},
                        backgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Product Cost (COGS)',
                        data: {!! json_encode($cogsArr) !!},
                        backgroundColor: '#ef4444'
                    },
                    {
                        label: 'Expenses',
                        data: {!! json_encode($expenseArr) !!},
                        backgroundColor: '#94a3b8'
                    },
                    {
                        label: 'Net Profit',
                        data: {!! json_encode($netProfitArr) !!},
                        backgroundColor: '#10b981'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection
