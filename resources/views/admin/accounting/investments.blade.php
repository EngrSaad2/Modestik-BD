@extends('layouts.admin')

@section('title', 'Investments')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-coins text-warning me-2"></i>Investment Management</h4>
                <p class="text-muted mb-0 small">Adding owner equity or external investments. (Increases cash balance without counting as sales or profit)</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center gap-3">
                    <i class="fas fa-chart-pie fa-2x"></i>
                    <div>
                        <span class="text-xs fw-bold text-uppercase d-block">Total Investment</span>
                        <h3 class="fw-extrabold mb-0">৳{{ number_format($totalInvested, 2) }}</h3>
                    </div>
                </div>
                <button type="button" class="btn btn-warning text-dark rounded-3 px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addInvestmentModal">
                    <i class="fas fa-plus me-1"></i>Add New Investment
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
                            <th>Investment Source</th>
                            <th>Description</th>
                            <th>Account</th>
                            <th class="text-end pe-4">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($investments as $inv)
                            <tr>
                                <td class="ps-4 font-monospace text-sm">{{ $inv->date->format('d M, Y') }}</td>
                                <td>
                                    <span class="badge bg-warning bg-opacity-10 text-dark fw-bold">
                                        {{ ucfirst(str_replace('_', ' ', $inv->source)) }}
                                    </span>
                                </td>
                                <td>{{ $inv->description ?? '-' }}</td>
                                <td>{{ $inv->account?->name ?? 'Cash' }}</td>
                                <td class="text-end pe-4 fw-extrabold text-success fs-6">৳{{ number_format($inv->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No investment records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($investments->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $investments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Investment -->
<div class="modal fade" id="addInvestmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold"><i class="fas fa-coins text-warning me-2"></i>Record New Investment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.accounting.investments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Investment Source *</label>
                        <select name="source" class="form-select rounded-3" required>
                            <option value="owner_investment">Owner Equity / Capital</option>
                            <option value="new_investment">New Investment</option>
                            <option value="previous_investment">Previous Capital</option>
                            <option value="other_investment">Other Investment</option>
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
                        <input type="text" name="description" class="form-control rounded-3" placeholder="Investment description...">
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
                            <label class="form-label text-sm fw-bold">Deposit Account</label>
                            <select name="account_id" class="form-select rounded-3">
                                <option value="">Default Account</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-dark rounded-3 px-4">Save Investment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
