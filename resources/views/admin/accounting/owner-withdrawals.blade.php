@extends('layouts.admin')

@section('title', 'Owner Withdrawals')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-money-bill-wave text-success me-2"></i>Owner Withdrawals & Drawings</h4>
                <p class="text-muted mb-0 small">Owner cash drawings for personal use. (Reduces cash balance, not counted as business expense)</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center gap-3">
                    <i class="fas fa-hand-holding-usd fa-2x"></i>
                    <div>
                        <span class="text-xs fw-bold text-uppercase d-block">Total Withdrawals</span>
                        <h3 class="fw-extrabold mb-0">৳{{ number_format($totalWithdrawals, 2) }}</h3>
                    </div>
                </div>
                <button type="button" class="btn btn-success rounded-3 px-3 py-2 fw-bold text-white" data-bs-toggle="modal" data-bs-target="#addWithdrawalModal">
                    <i class="fas fa-plus me-1"></i>Record Withdrawal
                </button>
            </div>
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
                            <th>Reason / Purpose</th>
                            <th>Payment Method</th>
                            <th>Account</th>
                            <th class="text-end pe-4">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawals as $w)
                            <tr>
                                <td class="ps-4 font-monospace text-sm">{{ $w->date->format('d M, Y') }}</td>
                                <td><span class="fw-bold text-dark">{{ $w->reason ?? 'Personal Use' }}</span></td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ strtoupper($w->payment_method) }}</span></td>
                                <td>{{ $w->account?->name ?? 'Cash' }}</td>
                                <td class="text-end pe-4 fw-extrabold text-danger fs-6">-৳{{ number_format($w->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No withdrawal records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($withdrawals->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Withdrawal -->
<div class="modal fade" id="addWithdrawalModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold"><i class="fas fa-money-bill-wave text-success me-2"></i>Record Owner Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.accounting.owner-withdrawals.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Withdrawal Amount (৳) *</label>
                            <input type="number" step="0.01" name="amount" class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Date *</label>
                            <input type="date" name="date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Reason / Purpose</label>
                        <input type="text" name="reason" class="form-control rounded-3" placeholder="e.g. Personal expenses...">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select rounded-3">
                                <option value="cash">Cash</option>
                                <option value="bkash">bKash</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Source Account</label>
                            <select name="account_id" class="form-select rounded-3">
                                <option value="">Default Account</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format($acc->current_balance, 0) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Notes</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success text-white rounded-3 px-4">Save Withdrawal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
