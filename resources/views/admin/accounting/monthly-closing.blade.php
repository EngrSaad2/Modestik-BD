@extends('layouts.admin')

@section('title', 'Monthly Closing Summary')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-calendar-check text-primary me-2"></i>Monthly Closing Statement</h4>
                <p class="text-muted mb-0 small">End-of-month financial closing report and capital summary</p>
            </div>
            <form action="{{ route('admin.accounting.monthly-closing') }}" method="GET" class="d-flex align-items-center gap-2">
                <div>
                    <label class="form-label text-xs mb-1 fw-bold text-muted">Select Month</label>
                    <input type="month" name="month" class="form-control form-control-sm rounded-3" value="{{ $monthStr }}">
                </div>
                <div class="align-self-end">
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3"><i class="fas fa-calendar me-1"></i>View Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Summary Grid (Key Metrics) -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3">Revenue & Income</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Sales:</span>
                    <strong class="font-monospace text-primary">৳{{ number_format($summary['total_sales'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Money Received:</span>
                    <strong class="font-monospace text-success">৳{{ number_format($summary['money_received'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Customer Due:</span>
                    <strong class="font-monospace text-warning">৳{{ number_format($summary['customer_due'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Investments:</span>
                    <strong class="font-monospace text-info">৳{{ number_format($totalInvestments, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3">Costs & Expenses</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Product Cost (COGS):</span>
                    <strong class="font-monospace text-danger">৳{{ number_format($summary['product_cost'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Other Expenses:</span>
                    <strong class="font-monospace text-danger">৳{{ number_format($summary['other_expenses'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Purchases:</span>
                    <strong class="font-monospace text-secondary">৳{{ number_format($totalPurchases, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Paid Salaries:</span>
                    <strong class="font-monospace text-dark">৳{{ number_format($totalSalaries, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Owner Drawings:</span>
                    <strong class="font-monospace text-danger">৳{{ number_format($totalWithdrawals, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light">
                <h6 class="fw-bold text-uppercase text-dark border-bottom pb-2 mb-3">Monthly Profit & Closing Balance</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Gross Profit:</span>
                    <strong class="font-monospace text-info">৳{{ number_format($summary['gross_profit'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="fw-bold text-success fs-6">Net Profit:</span>
                    <strong class="font-monospace text-success fs-5">৳{{ number_format($summary['net_profit'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Cash in Hand:</span>
                    <strong class="font-monospace text-dark">৳{{ number_format($summary['cash_in_hand'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Stock Value:</span>
                    <strong class="font-monospace text-dark">৳{{ number_format($summary['stock_value'], 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
