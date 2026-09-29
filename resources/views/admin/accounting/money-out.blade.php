@extends('layouts.admin')

@section('title', 'Money Out & Expenses')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-arrow-up text-danger me-2"></i>Money Out & Expenses</h4>
                <p class="text-muted mb-0 small">Operational expenses, packaging, salaries, courier charges, and purchase records</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 d-flex align-items-center gap-3">
                    <i class="fas fa-receipt fa-2x"></i>
                    <div>
                        <span class="text-xs fw-bold text-uppercase d-block">Total Expenses</span>
                        <h3 class="fw-extrabold mb-0">৳{{ number_format($totalExpenses, 2) }}</h3>
                    </div>
                </div>
                <button type="button" class="btn btn-danger rounded-3 px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="fas fa-plus me-1"></i>Add New Expense
                </button>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.accounting.money-out') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted">Expense Category</label>
                    <select name="category" class="form-select form-select-sm rounded-3">
                        <option value="">All Categories</option>
                        <option value="product_purchase" {{ request('category') == 'product_purchase' ? 'selected' : '' }}>Product Purchase</option>
                        <option value="packaging" {{ request('category') == 'packaging' ? 'selected' : '' }}>Packaging</option>
                        <option value="delivery_expenses" {{ request('category') == 'delivery_expenses' ? 'selected' : '' }}>Delivery Expenses</option>
                        <option value="courier_charges" {{ request('category') == 'courier_charges' ? 'selected' : '' }}>Courier Charges</option>
                        <option value="employee_salary" {{ request('category') == 'employee_salary' ? 'selected' : '' }}>Employee Salary</option>
                        <option value="owner_salary" {{ request('category') == 'owner_salary' ? 'selected' : '' }}>Owner Salary</option>
                        <option value="marketing" {{ request('category') == 'marketing' ? 'selected' : '' }}>Marketing & Ads</option>
                        <option value="website_expenses" {{ request('category') == 'website_expenses' ? 'selected' : '' }}>Website & Server</option>
                        <option value="rent" {{ request('category') == 'rent' ? 'selected' : '' }}>Rent</option>
                        <option value="electricity" {{ request('category') == 'electricity' ? 'selected' : '' }}>Electricity & Utilities</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other Expenses</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-danger rounded-3 w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted text-xs text-uppercase">
                            <th class="ps-4">Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Payment Method / Account</th>
                            <th>Attachment</th>
                            <th class="text-end pe-4">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td class="ps-4 font-monospace text-sm">{{ $exp->date->format('d M, Y') }}</td>
                                <td>
                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold">
                                        {{ ucfirst(str_replace('_', ' ', $exp->category)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $exp->description ?? '-' }}</span>
                                    @if($exp->notes)<br><small class="text-muted">{{ $exp->notes }}</small>@endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark">
                                        {{ strtoupper($exp->payment_method) }} {{ $exp->account ? '('.$exp->account->name.')' : '' }}
                                    </span>
                                </td>
                                <td>
                                    @if($exp->attachment)
                                        <a href="{{ asset('storage/' . $exp->attachment) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2"><i class="fas fa-paperclip me-1"></i>View</a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 fw-extrabold text-danger fs-6">৳{{ number_format($exp->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No expense entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Expense -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold"><i class="fas fa-plus-circle text-danger me-2"></i>Add New Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.accounting.expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Expense Category *</label>
                        <select name="category" class="form-select rounded-3" required>
                            <option value="product_purchase">Product Purchase</option>
                            <option value="packaging">Packaging</option>
                            <option value="delivery_expenses">Delivery Expenses</option>
                            <option value="courier_charges">Courier Charges</option>
                            <option value="employee_salary">Employee Salary</option>
                            <option value="owner_salary">Owner Salary</option>
                            <option value="marketing">Marketing & Ads</option>
                            <option value="website_expenses">Website & Server</option>
                            <option value="rent">Rent</option>
                            <option value="electricity">Electricity & Utilities</option>
                            <option value="other">Other Expenses</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Amount (৳) *</label>
                            <input type="number" step="0.01" name="amount" class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Date *</label>
                            <input type="date" name="date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Description</label>
                        <input type="text" name="description" class="form-control rounded-3" placeholder="Short description...">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select rounded-3">
                                <option value="cash">Cash</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Money Account</label>
                            <select name="account_id" class="form-select rounded-3">
                                <option value="">Default Account</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format($acc->current_balance, 0) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Receipt/Voucher Attachment</label>
                        <input type="file" name="attachment" class="form-control rounded-3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Additional Notes</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Relevant notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
