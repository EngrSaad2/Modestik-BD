@extends('layouts.admin')

@section('title', 'Money Accounts')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-wallet text-info me-2"></i>Cash & Money Accounts</h4>
                <p class="text-muted mb-0 small">Separate balances and transaction history for Cash, bKash, Nagad, and Bank Accounts</p>
            </div>
            <button type="button" class="btn btn-info text-white rounded-3 px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                <i class="fas fa-plus me-1"></i>Add New Account
            </button>
        </div>
    </div>

    <!-- Account Cards -->
    <div class="row g-3 mb-4">
        @foreach($accounts as $acc)
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-3 {{ $selectedAccountId == $acc->id ? 'border border-2 border-info' : '' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-muted text-xs fw-bold text-uppercase">{{ $acc->name }}</span>
                            <h3 class="fw-extrabold text-dark mb-0 mt-1">৳{{ number_format($acc->current_balance, 2) }}</h3>
                        </div>
                        <div class="p-3 bg-info bg-opacity-10 text-info rounded-3">
                            <i class="fas {{ $acc->type === 'bkash' || $acc->type === 'nagad' ? 'fa-mobile-alt' : ($acc->type === 'bank' ? 'fa-university' : 'fa-money-bill-wave') }} fa-lg"></i>
                        </div>
                    </div>
                    <div class="mt-auto pt-2 border-top border-light d-flex justify-content-between align-items-center">
                        <small class="text-muted" style="font-size: 11px;">Opening: ৳{{ number_format($acc->opening_balance, 0) }}</small>
                        <a href="{{ route('admin.accounting.accounts.index', ['account_id' => $acc->id]) }}" class="btn btn-xs btn-outline-info rounded-pill px-2">Statement</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Account Statement / Ledger -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-list me-2 text-info"></i>Account Statement</h5>
            @if($selectedAccountId)
                <a href="{{ route('admin.accounting.accounts.index') }}" class="btn btn-xs btn-light rounded-pill px-3">Clear Filter</a>
            @endif
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted text-xs text-uppercase">
                            <th class="ps-4">Date</th>
                            <th>Account</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th class="text-end text-success">Money In (+In)</th>
                            <th class="text-end text-danger pe-4">Money Out (-Out)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="ps-4 font-monospace text-sm">{{ $tx->date->format('d M, Y') }}</td>
                                <td><span class="fw-bold text-dark">{{ $tx->account?->name ?? 'Cash' }}</span></td>
                                <td>{{ $tx->description }}</td>
                                <td><span class="badge bg-light text-dark font-monospace">{{ $tx->reference_number ?? '-' }}</span></td>
                                <td class="text-end fw-bold text-success">
                                    {{ $tx->money_in > 0 ? '+৳'.number_format($tx->money_in, 2) : '-' }}
                                </td>
                                <td class="text-end pe-4 fw-bold text-danger">
                                    {{ $tx->money_out > 0 ? '-৳'.number_format($tx->money_out, 2) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No transaction history found.</td>
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

<!-- Modal: Add Account -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold"><i class="fas fa-wallet text-info me-2"></i>Add New Money Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.accounting.accounts.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Account Name *</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. bKash Merchant 017..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Type *</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="cash">Cash</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="bank">Bank Account</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Account Number (Optional)</label>
                        <input type="text" name="account_number" class="form-control rounded-3" placeholder="017... / Bank A/C No">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Opening Balance (৳)</label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control rounded-3" value="0.00">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white rounded-3 px-4">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
