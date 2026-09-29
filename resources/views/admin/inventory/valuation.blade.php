@extends('layouts.admin')

@section('title', 'Inventory Valuation')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.inventory.index') }}">Inventory Log</a></li>
    <li class="breadcrumb-item active">Valuation</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Monthly Inventory Valuation Report</h4>
    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-2"></i>Back to Inventory
    </a>
</div>

<!-- Valuation Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-left: 4px solid var(--admin-primary) !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px;">Total Stock Quantity (মোট স্টক পরিমাণ)</span>
            <h2 class="fw-bold mb-0 text-dark">{{ number_format($totalQty) }} Units</h2>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white" style="border-left: 4px solid #10b981 !important;">
            <span class="text-muted d-block mb-1" style="font-size:12px;">Total Inventory Value (মোট ইনভেন্টরি মূল্য)</span>
            <h2 class="fw-bold mb-0 text-success">৳{{ number_format($totalValue, 2) }}</h2>
        </div>
    </div>
</div>

<!-- Unsold Inventory List -->
<div class="table-card">
    <div class="card-header">
        <h5 class="mb-0">Unsold / Active Stock Valuation</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Product / Variant</th>
                        <th>Type</th>
                        <th>Available Stock</th>
                        <th>Unit Cost (BDT)</th>
                        <th>Total Value (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Simple Products -->
                    @foreach($unsoldSimple as $p)
                        <tr>
                            <td>
                                <strong>{{ $p->name }}</strong><br>
                                <small class="text-muted">SKU: {{ $p->sku ?? '-' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark">Simple Product</span></td>
                            <td>{{ $p->quantity }} Units</td>
                            <td>৳{{ number_format($p->cost ?? 0, 2) }}</td>
                            <td><strong>৳{{ number_format(($p->cost ?? 0) * $p->quantity, 2) }}</strong></td>
                        </tr>
                    @endforeach

                    <!-- Variant Products -->
                    @foreach($unsoldVariants as $v)
                        <tr>
                            <td>
                                <strong>{{ $v->product->name ?? 'Unknown Product' }}</strong><br>
                                <small class="badge bg-secondary">{{ $v->display_name }}</small><br>
                                <small class="text-muted">SKU: {{ $v->sku ?? '-' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark">Variant Product</span></td>
                            <td>{{ $v->quantity }} Units</td>
                            <td>৳{{ number_format($v->product->cost ?? 0, 2) }}</td>
                            <td><strong>৳{{ number_format(($v->product->cost ?? 0) * $v->quantity, 2) }}</strong></td>
                        </tr>
                    @endforeach

                    @if($unsoldSimple->isEmpty() && $unsoldVariants->isEmpty())
                        <tr><td colspan="5" class="text-center py-4 text-muted">No stock available currently.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
