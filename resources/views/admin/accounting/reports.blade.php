@extends('layouts.admin')

@section('title', 'Financial Reports')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-file-excel text-success me-2"></i>Financial Reports Suite</h4>
                <p class="text-muted mb-0 small">Generate, download, and print custom financial reports for all accounting modules</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.accounting.reports.export', ['type' => $reportType, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success rounded-3 px-3">
                    <i class="fas fa-download me-1"></i>Export Excel (CSV)
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary rounded-3 px-3">
                    <i class="fas fa-print me-1"></i>Print
                </button>
            </div>
        </div>
    </div>

    <!-- Report Type Selector & Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.accounting.reports.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-xs fw-bold text-muted">Report Type</label>
                    <select name="type" class="form-select form-select-sm rounded-3">
                        <option value="sales" {{ $reportType === 'sales' ? 'selected' : '' }}>Sales Report</option>
                        <option value="expenses" {{ $reportType === 'expenses' ? 'selected' : '' }}>Expense Report</option>
                        <option value="purchases" {{ $reportType === 'purchases' ? 'selected' : '' }}>Purchase Report</option>
                        <option value="salaries" {{ $reportType === 'salaries' ? 'selected' : '' }}>Salary Report</option>
                        <option value="investments" {{ $reportType === 'investments' ? 'selected' : '' }}>Investment Report</option>
                        <option value="withdrawals" {{ $reportType === 'withdrawals' ? 'selected' : '' }}>Withdrawal Report</option>
                        <option value="transactions" {{ $reportType === 'transactions' ? 'selected' : '' }}>Cash Flow / Transaction Ledger Report</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 w-100"><i class="fas fa-filter me-1"></i>Generate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Display Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted text-xs text-uppercase">
                            <th class="ps-4">ID / Ref #</th>
                            <th>Date</th>
                            <th>Description / Title</th>
                            <th>Payment Method</th>
                            <th class="text-end pe-4">Amount (৳)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">#{{ $row->order_number ?? $row->invoice_number ?? $row->reference_number ?? $row->id }}</td>
                                <td>{{ isset($row->created_at) ? $row->created_at->format('d M, Y') : (isset($row->date) ? $row->date->format('d M, Y') : '-') }}</td>
                                <td>{{ $row->name ?? $row->category ?? $row->person_name ?? $row->description ?? '-' }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ strtoupper($row->payment_method ?? 'Cash') }}</span></td>
                                <td class="text-end pe-4 fw-bold fs-6">৳{{ number_format($row->total ?? $row->total_amount ?? $row->amount ?? $row->money_in ?? $row->money_out ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No report data found for this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
