@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Payments</h4>
    <span class="text-muted">Transaction Logs</span>
</div>

<div class="table-card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <h5 class="mb-0">All Payments</h5>
        <form action="{{ route('admin.payments.index') }}" method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search order/trx..." value="{{ request('search') }}" style="width: 200px;">
            <select name="status" class="form-select form-select-sm" style="width: 120px;">
                <option value="">Status</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
            <select name="method" class="form-select form-select-sm" style="width: 120px;">
                <option value="">Method</option>
                <option value="cod" {{ request('method') === 'cod' ? 'selected' : '' }}>COD</option>
                <option value="bkash" {{ request('method') === 'bkash' ? 'selected' : '' }}>bKash</option>
                <option value="sslcommerz" {{ request('method') === 'sslcommerz' ? 'selected' : '' }}>SSLCommerz</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-secondary">Reset</a>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Transaction ID</th>
                        <th>Payment Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><strong>{{ $payment->order_number }}</strong></td>
                        <td>{{ $payment->name }}</td>
                        <td class="fw-bold">৳{{ number_format($payment->total) }}</td>
                        <td>
                            <span class="badge bg-light text-dark text-uppercase">{{ $payment->payment_method }}</span>
                        </td>
                        <td>
                            @if($payment->bkash_trx_id)
                                <code class="text-dark">{{ $payment->bkash_trx_id }}</code>
                                @if($payment->bkash_number)
                                    <br><small class="text-muted">Num: {{ $payment->bkash_number }}</small>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{!! $payment->payment_status_badge !!}</td>
                        <td>{{ $payment->created_at->format('d M, Y h:i A') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $payment) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No payments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
