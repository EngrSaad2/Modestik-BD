@extends('layouts.admin')

@section('title', 'Customer Due')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-hand-holding-usd text-warning me-2"></i>Customer Receivables & Due</h4>
                <p class="text-muted mb-0 small">List of orders with pending payments and payment collection entry</p>
            </div>
            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center gap-3">
                <i class="fas fa-coins fa-2x"></i>
                <div>
                    <span class="text-xs fw-bold text-uppercase d-block">Total Customer Due</span>
                    <h3 class="fw-extrabold mb-0">৳{{ number_format($totalDue, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted text-xs text-uppercase">
                            <th class="ps-4">Order ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">#{{ $o->order_number }}</td>
                                <td><span class="fw-bold text-dark">{{ $o->name }}</span></td>
                                <td>{{ $o->phone }}</td>
                                <td class="fw-bold">৳{{ number_format($o->total, 2) }}</td>
                                <td class="fw-bold text-success">৳{{ number_format($o->payment_status === 'paid' ? $o->total : 0, 2) }}</td>
                                <td class="fw-extrabold text-danger fs-6">৳{{ number_format($o->payment_status === 'paid' ? 0 : $o->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">
                                        {{ strtoupper($o->payment_status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button type="button" class="btn btn-xs btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#collectModal-{{ $o->id }}">
                                        <i class="fas fa-check-circle me-1"></i>Collect Due
                                    </button>

                                    <!-- Collect Modal -->
                                    <div class="modal fade text-start" id="collectModal-{{ $o->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content rounded-4 border-0">
                                                <div class="modal-header border-0">
                                                    <h5 class="modal-header-title fw-bold">Collect Payment (#{{ $o->order_number }})</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.accounting.orders.collect-due', $o->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body p-4">
                                                        <p class="text-muted small">Customer: <strong>{{ $o->name }}</strong> (Due: <strong>৳{{ number_format($o->total, 2) }}</strong>)</p>
                                                        <div class="mb-3">
                                                            <label class="form-label text-sm fw-bold">Amount Received *</label>
                                                            <input type="number" step="0.01" name="amount" class="form-control rounded-3" value="{{ $o->total }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label text-sm fw-bold">Payment Method</label>
                                                            <select name="payment_method" class="form-select rounded-3">
                                                                <option value="cod">Cash on Delivery</option>
                                                                <option value="bkash">bKash</option>
                                                                <option value="nagad">Nagad</option>
                                                                <option value="bank">Bank</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label text-sm fw-bold">Money Account</label>
                                                            <select name="account_id" class="form-select rounded-3">
                                                                @foreach($accounts as $acc)
                                                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0">
                                                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success rounded-3 px-4">Save Payment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No customer due records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
