@extends('layouts.admin')

@section('title', 'Money In')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-arrow-down text-success me-2"></i>Money In & Revenue</h4>
                <p class="text-muted mb-0 small">Income from order payments, bKash, COD, and other sources. (Cancelled orders excluded)</p>
            </div>
            <div class="p-3 bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center gap-3">
                <i class="fas fa-wallet fa-2x"></i>
                <div>
                    <span class="text-xs fw-bold text-uppercase d-block">Total Received</span>
                    <h3 class="fw-extrabold mb-0">৳{{ number_format($totalIncome, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.accounting.money-in') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-xs fw-bold text-muted">Payment Method</label>
                    <select name="payment_method" class="form-select form-select-sm rounded-3">
                        <option value="">All Methods</option>
                        <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                        <option value="bkash" {{ request('payment_method') == 'bkash' ? 'selected' : '' }}>bKash</option>
                        <option value="nagad" {{ request('payment_method') == 'nagad' ? 'selected' : '' }}>Nagad</option>
                        <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Bank Account</option>
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
                    <button type="submit" class="btn btn-sm btn-success rounded-3 w-100"><i class="fas fa-search me-1"></i>Search</button>
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
                            <th>Source</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Payment Method</th>
                            <th class="text-end">Amount</th>
                            <th class="pe-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="ps-4 font-monospace text-sm">{{ $tx->date->format('d M, Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-dark fw-bold">
                                        {{ ucfirst(str_replace('_', ' ', $tx->transaction_type)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($tx->order)
                                        <a href="{{ route('admin.orders.show', $tx->order->id) }}" class="fw-bold text-decoration-none">#{{ $tx->order->order_number }}</a>
                                    @else
                                        <span class="text-muted">{{ $tx->reference_number ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>{{ $tx->order?->name ?? 'Business Entity' }}</td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info fw-bold">
                                        {{ strtoupper($tx->order?->payment_method ?? $tx->account?->type ?? 'Cash') }}
                                    </span>
                                </td>
                                <td class="text-end fw-extrabold text-success fs-6">৳{{ number_format($tx->money_in, 2) }}</td>
                                <td class="pe-4 text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Paid</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No money received records found.</td>
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
