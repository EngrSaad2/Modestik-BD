@extends('layouts.admin')

@section('title', 'Customer Reports')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Customer Reports</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Customer Analytics</h4>
    <a href="{{ route('admin.reports.export', 'customers') }}" class="btn btn-admin-primary"><i class="fas fa-file-csv me-1"></i>Export Customers List</a>
</div>

<!-- Top Purchasing Customers -->
<div class="table-card">
    <div class="card-header">
        <h5>Top purchasing Customers & Lifetime Value (LTV)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Customer Name</th>
                        <th>Email Address</th>
                        <th>Orders Placed</th>
                        <th>Lifetime Spend (LTV)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCustomers as $index => $customer)
                        <tr>
                            <td><strong>#{{ $index + 1 }}</strong></td>
                            <td><strong>{{ $customer->name }}</strong></td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->total_orders }} orders</td>
                            <td class="fw-bold text-success">৳{{ number_format($customer->total_spent, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No customer transactions logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
