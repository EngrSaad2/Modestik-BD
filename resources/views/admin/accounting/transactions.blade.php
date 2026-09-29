@extends('layouts.admin')

@section('title', 'Accounting Ledger')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-list-alt text-dark me-2"></i>Central Accounting Ledger</h4>
                <p class="text-muted mb-0 small">Double-entry audit track of every financial transaction in the system. (Transactions are reversed, never deleted)</p>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.accounting.transactions.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm rounded-3" placeholder="Reference / Description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted">Transaction Type</label>
                    <select name="type" class="form-select form-select-sm rounded-3">
                        <option value="">All Transactions</option>
                        <option value="order_payment" {{ request('type') == 'order_payment' ? 'selected' : '' }}>Order Payment</option>
                        <option value="product_purchase" {{ request('type') == 'product_purchase' ? 'selected' : '' }}>Product Purchase</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                        <option value="salary" {{ request('type') == 'salary' ? 'selected' : '' }}>Salary</option>
                        <option value="investment" {{ request('type') == 'investment' ? 'selected' : '' }}>Investment</option>
                        <option value="owner_withdrawal" {{ request('type') == 'owner_withdrawal' ? 'selected' : '' }}>Owner Withdrawal</option>
                        <option value="courier_settlement" {{ request('type') == 'courier_settlement' ? 'selected' : '' }}>Courier Settlement</option>
                        <option value="reversal" {{ request('type') == 'reversal' ? 'selected' : '' }}>Reversal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm rounded-3" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs fw-bold text-muted">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm rounded-3" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-dark rounded-3 w-100"><i class="fas fa-search me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted text-xs text-uppercase">
                            <th class="ps-4">ID #</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th>Account</th>
                            <th class="text-end text-success">Money In (+In)</th>
                            <th class="text-end text-danger">Money Out (-Out)</th>
                            <th class="pe-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr class="{{ $tx->is_reversed ? 'bg-light text-muted' : '' }}">
                                <td class="ps-4 font-monospace text-xs">#{{ $tx->id }}</td>
                                <td class="font-monospace text-xs">{{ $tx->date->format('d M, Y') }}</td>
                                <td>
                                    <span class="badge {{ $tx->is_reversed ? 'bg-secondary' : 'bg-dark' }} bg-opacity-10 text-dark font-monospace text-xs">
                                        {{ ucfirst(str_replace('_', ' ', $tx->transaction_type)) }}
                                    </span>
                                </td>
                                <td><span class="badge bg-light text-dark font-monospace text-xs">{{ $tx->reference_number ?? '-' }}</span></td>
                                <td>
                                    <span class="{{ $tx->is_reversed ? 'text-decoration-line-through' : 'fw-bold text-dark' }}">{{ $tx->description }}</span>
                                    @if($tx->is_reversed)
                                        <br><small class="text-danger fw-bold"><i class="fas fa-undo me-1"></i>Reversed</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-info bg-opacity-10 text-info text-xs">{{ $tx->account?->name ?? 'Default' }}</span></td>
                                <td class="text-end fw-bold text-success">
                                    {{ $tx->money_in > 0 ? '+৳'.number_format($tx->money_in, 2) : '-' }}
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    {{ $tx->money_out > 0 ? '-৳'.number_format($tx->money_out, 2) : '-' }}
                                </td>
                                <td class="pe-4 text-center">
                                    @if(!$tx->is_reversed)
                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#reverseModal-{{ $tx->id }}">
                                            <i class="fas fa-undo me-1"></i>Reverse
                                        </button>

                                        <!-- Reverse Modal -->
                                        <div class="modal fade text-start" id="reverseModal-{{ $tx->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0">
                                                    <div class="modal-header border-0">
                                                        <h5 class="modal-header-title fw-bold">Reverse Transaction (TX #{{ $tx->id }})</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.accounting.transactions.reverse', $tx->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body p-4">
                                                            <p class="text-muted small mb-2">Description: <strong>{{ $tx->description }}</strong></p>
                                                            <div class="alert alert-warning border-0 small mb-3">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>This will not delete the transaction. It creates a counter reversal entry to balance the account.
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label text-sm fw-bold">Reason for Reversal *</label>
                                                                <input type="text" name="reason" class="form-control rounded-3" placeholder="e.g. Wrong entry / Cancelled order..." required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger rounded-3 px-4">Confirm Reversal</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary rounded-pill">Reversed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">No transaction ledger entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
