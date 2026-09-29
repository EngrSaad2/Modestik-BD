@extends('layouts.admin')

@section('title', 'Financial Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Financial Management</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Financial & Accounting Dashboard</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
        <i class="fas fa-plus me-2"></i>Record Transaction
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Dashboard Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-left: 4px solid var(--admin-primary) !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px;">Total Investment</span>
            <h3 class="fw-bold mb-0">৳{{ number_format($totalInvestment, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-left: 4px solid #ef4444 !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px;">Total Expenses</span>
            <h3 class="fw-bold mb-0">৳{{ number_format($totalExpenses, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-left: 4px solid #10b981 !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px;">Total Sales Revenue</span>
            <h3 class="fw-bold mb-0">৳{{ number_format($totalSales, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-left: 4px solid #3b82f6 !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px;">Inventory Value</span>
            <h3 class="fw-bold mb-0">৳{{ number_format($inventoryValue, 2) }}</h3>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-light-primary" style="background:#fff1f2; border-left: 4px solid #d97d8c !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px; color:#c25f6f !important;">Cash In Hand</span>
            <h3 class="fw-bold mb-0 text-primary">৳{{ number_format($cashInHand, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100" style="background:#f0fdf4; border-left: 4px solid #22c55e !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px; color:#15803d !important;">Net Profit / Loss</span>
            <h3 class="fw-bold mb-0 {{ $netProfitLoss >= 0 ? 'text-success' : 'text-danger' }}">
                ৳{{ number_format($netProfitLoss, 2) }}
            </h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-left: 4px solid #6366f1 !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px;">Working Capital</span>
            <h3 class="fw-bold mb-0">৳{{ number_format($workingCapital, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 h-100 bg-white" style="border-left: 4px solid #8b5cf6 !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px;">Business Worth</span>
            <h3 class="fw-bold mb-0">৳{{ number_format($businessWorth, 2) }}</h3>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Transactions Log</h5>
        <form action="{{ route('admin.financial.index') }}" method="GET" class="d-flex gap-2">
            <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="investment" {{ request('type') === 'investment' ? 'selected' : '' }}>Investment</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                <option value="revenue" {{ request('type') === 'revenue' ? 'selected' : '' }}>Revenue</option>
            </select>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Attachment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>{{ $t->date->format('d M Y') }}</td>
                            <td>
                                <span class="badge {{ $t->type === 'investment' ? 'bg-primary' : ($t->type === 'expense' ? 'bg-danger' : 'bg-success') }}">
                                    {{ ucfirst($t->type) }}
                                </span>
                            </td>
                            <td>{{ ucwords(str_replace('_', ' ', $t->category)) }}</td>
                            <td><strong>৳{{ number_format($t->amount, 2) }}</strong></td>
                            <td><small class="text-muted">{{ $t->notes ?? '-' }}</small></td>
                            <td>
                                @if($t->attachment)
                                    <a href="{{ asset('storage/' . $t->attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-paperclip"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No transactions recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $transactions->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Record Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addTransactionModalLabel">Record Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.financial.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Transaction Type</label>
                        <select name="type" id="txType" class="form-select" required>
                            <option value="" disabled selected>Select Type...</option>
                            <option value="investment">Investment (বিনিয়োগ)</option>
                            <option value="expense">Expense (খরচ)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" id="txCategory" class="form-select" required>
                            <option value="" disabled selected>Select Category...</option>
                            <!-- Options filled dynamically by JS -->
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Amount (BDT)</label>
                            <input type="number" name="amount" step="0.01" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Date</label>
                            <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Describe the transaction..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Attachment (Receipt, invoice etc.)</label>
                        <input type="file" name="attachment" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const categories = {
        investment: [
            { value: 'previous_investment', label: 'Previous Investment' },
            { value: 'new_investment', label: 'New Investment' },
            { value: 'other_investments', label: 'Other Investment' }
        ],
        expense: [
            { value: 'product_purchase_cost', label: 'Product Purchase Cost' },
            { value: 'packaging_cost', label: 'Packaging Cost' },
            { value: 'delivery_cost', label: 'Delivery Cost' },
            { value: 'marketing_expense', label: 'Marketing Expense' },
            { value: 'employee_salary', label: 'Employee Salary' },
            { value: 'owner_salary', label: 'Owner Salary' },
            { value: 'office_expense', label: 'Office Expense' },
            { value: 'miscellaneous_expense', label: 'Miscellaneous Expense' }
        ]
    };

    $('#txType').change(function() {
        let type = $(this).val();
        let catSelect = $('#txCategory');
        catSelect.empty().append('<option value="" disabled selected>Select Category...</option>');
        
        if (categories[type]) {
            categories[type].forEach(cat => {
                catSelect.append(`<option value="${cat.value}">${cat.label}</option>`);
            });
        }
    });
</script>
@endsection
