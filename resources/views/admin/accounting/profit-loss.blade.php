@extends('layouts.admin')

@section('title', 'Profit & Loss Statement')

@section('content')
<div class="container-fluid px-0">
    <!-- Header & Date Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-balance-scale text-info me-2"></i>Profit & Loss Statement</h4>
                <p class="text-muted mb-0 small">Clear breakdown of total revenues, COGS, operating costs, and net profit</p>
            </div>
            <form action="{{ route('admin.accounting.profit-loss') }}" method="GET" class="d-flex align-items-center gap-2">
                <div>
                    <label class="form-label text-xs mb-1 fw-bold text-muted">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ $startDate }}">
                </div>
                <div>
                    <label class="form-label text-xs mb-1 fw-bold text-muted">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ $endDate }}">
                </div>
                <div class="align-self-end">
                    <button type="submit" class="btn btn-sm btn-info text-white rounded-3 px-3"><i class="fas fa-filter me-1"></i>Refresh Statement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Clean P&L Statement Box -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow rounded-4 p-4" style="background: #ffffff;">
                <div class="text-center mb-4 pb-3 border-bottom">
                    <h3 class="fw-extrabold text-dark mb-1">Profit & Loss Statement</h3>
                    <p class="text-muted mb-0 small">Period: {{ date('d M, Y', strtotime($startDate)) }} to {{ date('d M, Y', strtotime($endDate)) }}</p>
                </div>

                <div class="p-3 mb-3 rounded-3" style="background: #f8fafc;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fs-5 fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>Total Sales Revenue</span>
                        <span class="fs-5 font-monospace fw-extrabold text-primary">৳{{ number_format($summary['total_sales'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-6 text-muted ps-4"><i class="fas fa-minus text-danger me-2"></i>Cost of Goods Sold (COGS)</span>
                        <span class="fs-6 font-monospace fw-bold text-danger">- ৳{{ number_format($summary['product_cost'], 2) }}</span>
                    </div>
                </div>

                <!-- Gross Profit -->
                <div class="d-flex justify-content-between align-items-center p-3 mb-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-20">
                    <span class="fs-5 fw-extrabold text-info"><i class="fas fa-equals me-2"></i>Gross Profit</span>
                    <span class="fs-4 font-monospace fw-extrabold text-info">৳{{ number_format($summary['gross_profit'], 2) }}</span>
                </div>

                <div class="p-3 mb-3 rounded-3" style="background: #fef2f2;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fs-6 fw-bold text-danger"><i class="fas fa-minus-circle me-2"></i>Other Operating Expenses</span>
                        <span class="fs-6 font-monospace fw-bold text-danger">- ৳{{ number_format($summary['other_expenses'], 2) }}</span>
                    </div>
                    <div class="ps-4 mt-2">
                        @foreach($expenseCategories as $ec)
                            <div class="d-flex justify-content-between align-items-center py-1 text-muted text-sm border-bottom border-light">
                                <span>• {{ ucfirst(str_replace('_', ' ', $ec->category)) }}</span>
                                <span class="font-monospace">৳{{ number_format($ec->total, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Net Profit -->
                <div class="d-flex justify-content-between align-items-center p-4 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div>
                        <h4 class="fw-extrabold mb-0"><i class="fas fa-chart-line me-2"></i>Net Profit</h4>
                        <small class="text-white-50">Profit Margin: <strong>{{ $summary['profit_margin'] }}%</strong></small>
                    </div>
                    <h2 class="fw-extrabold font-monospace mb-0">৳{{ number_format($summary['net_profit'], 2) }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
