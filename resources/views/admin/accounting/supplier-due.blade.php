@extends('layouts.admin')

@section('title', 'Supplier Due')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-file-invoice-dollar text-danger me-2"></i>Supplier Payables & Due</h4>
                <p class="text-muted mb-0 small">Purchases with pending payments to suppliers</p>
            </div>
            <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-4 d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-triangle fa-2x"></i>
                <div>
                    <span class="text-xs fw-bold text-uppercase d-block">Total Supplier Due</span>
                    <h3 class="fw-extrabold mb-0">৳{{ number_format($totalSupplierDue, 2) }}</h3>
                </div>
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
                            <th class="ps-4">Invoice #</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Total Purchase</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $p)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">#{{ $p->invoice_number }}</td>
                                <td>{{ $p->purchase_date->format('d M, Y') }}</td>
                                <td><span class="fw-bold text-dark">{{ $p->supplier?->name ?? 'General Purchase' }}</span></td>
                                <td class="fw-bold">৳{{ number_format($p->total_amount, 2) }}</td>
                                <td class="fw-bold text-success">৳{{ number_format($p->paid_amount, 2) }}</td>
                                <td class="fw-extrabold text-danger fs-6">৳{{ number_format($p->due_amount, 2) }}</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-xs btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#payModal-{{ $p->id }}">
                                        <i class="fas fa-hand-holding-usd me-1"></i>Pay Supplier
                                    </button>

                                    <!-- Pay Modal -->
                                    <div class="modal fade text-start" id="payModal-{{ $p->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-header-title fw-bold">Pay Supplier Due (#{{ $p->invoice_number }})</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.accounting.purchases.pay-due', $p->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body p-4">
                                                        <p class="text-muted small">Supplier: <strong>{{ $p->supplier?->name ?? 'General' }}</strong> (Due: <strong>৳{{ number_format($p->due_amount, 2) }}</strong>)</p>
                                                        <div class="mb-3">
                                                            <label class="form-label text-sm fw-bold">Payment Amount (৳) *</label>
                                                            <input type="number" step="0.01" name="amount" class="form-control rounded-3" value="{{ $p->due_amount }}" max="{{ $p->due_amount }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label text-sm fw-bold">Payment Method</label>
                                                            <select name="payment_method" class="form-select rounded-3">
                                                                <option value="cash">Cash</option>
                                                                <option value="bkash">bKash</option>
                                                                <option value="nagad">Nagad</option>
                                                                <option value="bank">Bank Transfer</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label text-sm fw-bold">Money Account</label>
                                                            <select name="account_id" class="form-select rounded-3">
                                                                @foreach($accounts as $acc)
                                                                    <option value="{{ $acc->id }}">{{ $acc->name }} (৳{{ number_format($acc->current_balance, 0) }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger rounded-3 px-4">Confirm Payment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No supplier due records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($purchases->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
