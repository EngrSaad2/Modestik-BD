@extends('layouts.admin')

@section('title', 'Accounting Dashboard')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Date Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-calculator me-2 text-danger"></i>Financial Position Summary</h4>
                <p class="text-muted mb-0 small">Accurate overview of income, expenses, receivables/payables, and net profit</p>
            </div>
            <form action="{{ route('admin.accounting.dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                <div>
                    <label class="form-label text-xs mb-1 fw-bold text-muted">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ $startDate }}">
                </div>
                <div>
                    <label class="form-label text-xs mb-1 fw-bold text-muted">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ $endDate }}">
                </div>
                <div class="align-self-end">
                    <button type="submit" class="btn btn-sm btn-danger rounded-3 px-3"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Top Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Sales -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #f4f6ff 100%);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted fw-bold text-uppercase text-xs">Total Sales</span>
                        <h3 class="fw-extrabold text-primary mb-0 mt-1">৳{{ number_format($summary['total_sales'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                    </div>
                </div>
                <div class="mt-auto pt-2 border-top border-light">
                    <small class="text-muted" style="font-size: 11px;"><i class="fas fa-info-circle me-1 text-primary"></i>Total sales revenue from customer orders within selected date range.</small>
                </div>
            </div>
        </div>

        <!-- 2. Money Received -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted fw-bold text-uppercase text-xs">Money Received</span>
                        <h3 class="fw-extrabold text-success mb-0 mt-1">৳{{ number_format($summary['money_received'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                        <i class="fas fa-arrow-down fa-lg"></i>
                    </div>
                </div>
                <div class="mt-auto pt-2 border-top border-light">
                    <small class="text-muted" style="font-size: 11px;"><i class="fas fa-info-circle me-1 text-success"></i>Actual cash received from orders and other collections.</small>
                </div>
            </div>
        </div>

        <!-- 3. Customer Due -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted fw-bold text-uppercase text-xs">Customer Due</span>
                        <h3 class="fw-extrabold text-warning mb-0 mt-1">৳{{ number_format($summary['customer_due'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3">
                        <i class="fas fa-hand-holding-usd fa-lg"></i>
                    </div>
                </div>
                <div class="mt-auto pt-2 border-top border-light">
                    <small class="text-muted" style="font-size: 11px;"><i class="fas fa-info-circle me-1 text-warning"></i>Pending receivables from customers or couriers.</small>
                </div>
            </div>
        </div>

        <!-- 4. Product Cost / COGS -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted fw-bold text-uppercase text-xs">Product Cost (COGS)</span>
                        <h3 class="fw-extrabold text-danger mb-0 mt-1">৳{{ number_format($summary['product_cost'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-3">
                        <i class="fas fa-box-open fa-lg"></i>
                    </div>
                </div>
                <div class="mt-auto pt-2 border-top border-light">
                    <small class="text-muted" style="font-size: 11px;"><i class="fas fa-info-circle me-1 text-danger"></i>Cost price of goods sold.</small>
                </div>
            </div>
        </div>

        <!-- 5. Other Expenses -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted fw-bold text-uppercase text-xs">Other Expenses</span>
                        <h3 class="fw-extrabold text-dark mb-0 mt-1">৳{{ number_format($summary['other_expenses'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-secondary bg-opacity-10 text-dark rounded-3">
                        <i class="fas fa-receipt fa-lg"></i>
                    </div>
                </div>
                <div class="mt-auto pt-2 border-top border-light">
                    <small class="text-muted" style="font-size: 11px;"><i class="fas fa-info-circle me-1"></i>Packaging, shipping, salaries, ads, and operational expenses.</small>
                </div>
            </div>
        </div>

        <!-- 6. Net Profit -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-white-50 fw-bold text-uppercase text-xs">Net Profit</span>
                        <h3 class="fw-extrabold text-white mb-0 mt-1">৳{{ number_format($summary['net_profit'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-white bg-opacity-20 text-white rounded-3">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                </div>
                <div class="mt-auto pt-2 border-top border-white border-opacity-20">
                    <small class="text-white-50" style="font-size: 11px;"><i class="fas fa-check-circle me-1 text-white"></i>Actual profit after deducting COGS and all expenses (Margin: {{ $summary['profit_margin'] }}%).</small>
                </div>
            </div>
        </div>

        <!-- 7. Cash in Hand -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted fw-bold text-uppercase text-xs">Cash in Hand</span>
                        <h3 class="fw-extrabold text-info mb-0 mt-1">৳{{ number_format($summary['cash_in_hand'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-3">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
                <div class="mt-auto pt-2 border-top border-light">
                    <small class="text-muted" style="font-size: 11px;"><i class="fas fa-info-circle me-1 text-info"></i>Total liquid cash across all active bank and digital accounts.</small>
                </div>
            </div>
        </div>

        <!-- 8. Stock Value -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ffffff 0%, #fbfbfe 100%);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted fw-bold text-uppercase text-xs">Stock Value</span>
                        <h3 class="fw-extrabold text-secondary mb-0 mt-1">৳{{ number_format($summary['stock_value'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-dark bg-opacity-10 text-dark rounded-3">
                        <i class="fas fa-cubes fa-lg"></i>
                    </div>
                </div>
                <div class="mt-auto pt-2 border-top border-light">
                    <small class="text-muted" style="font-size: 11px;"><i class="fas fa-info-circle me-1"></i>Valuation of current inventory based on cost prices.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Breakdown Box -->
    <div class="alert alert-info border-0 rounded-4 shadow-sm p-4 mb-4" style="background: #e0f2fe; color: #0369a1;">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-calculator fa-2x"></i>
            <div>
                <h6 class="fw-bold mb-1">Profit Calculation Formula:</h6>
                <div class="d-flex flex-wrap gap-4 mt-2 font-monospace" style="font-size: 14px;">
                    <span><strong>Total Sales:</strong> ৳{{ number_format($summary['total_sales'], 0) }}</span>
                    <span>− <strong>Product Cost:</strong> ৳{{ number_format($summary['product_cost'], 0) }}</span>
                    <span>= <strong>Gross Profit:</strong> ৳{{ number_format($summary['gross_profit'], 0) }}</span>
                    <span>− <strong>Other Expenses:</strong> ৳{{ number_format($summary['other_expenses'], 0) }}</span>
                    <span class="badge bg-success fs-6">= <strong>Net Profit:</strong> ৳{{ number_format($summary['net_profit'], 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Recent Transactions -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-area me-2 text-primary"></i>Daily Sales vs Expenses</h5>
                </div>
                <div class="card-body p-4">
                    <canvas id="accountingChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-history me-2 text-danger"></i>Recent Transactions</h5>
                    <a href="{{ route('admin.accounting.transactions.index') }}" class="btn btn-xs btn-outline-danger rounded-pill px-2">View All</a>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        @forelse($recentTransactions as $tx)
                            <div class="list-group-item px-0 py-2 border-bottom border-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-sm fw-bold text-dark">{{ Str::limit($tx->description, 35) }}</h6>
                                        <small class="text-muted" style="font-size: 11px;">{{ $tx->date->format('d M, Y') }} • {{ ucfirst(str_replace('_', ' ', $tx->transaction_type)) }}</small>
                                    </div>
                                    <div class="text-end">
                                        @if($tx->money_in > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">+৳{{ number_format($tx->money_in, 2) }}</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">-৳{{ number_format($tx->money_out, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted py-4">No transactions found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('accountingChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [
                    {
                        label: 'Sales',
                        data: {!! json_encode($salesData) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Expenses',
                        data: {!! json_encode($expenseData) !!},
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        fill: true,
                        tension: 0.3
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
