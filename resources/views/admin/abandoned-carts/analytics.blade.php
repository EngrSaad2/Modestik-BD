@extends('layouts.admin')

@section('title', 'Abandoned Cart Analytics')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>Abandoned Cart Analytics</h3>
                <p class="text-muted mb-0" style="font-size: 13px;">Product-level drop-off analysis and cart recovery performance.</p>
            </div>
            <a href="{{ route('admin.orders.incomplete') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="fas fa-arrow-left me-1"></i>Back to Incomplete Orders
            </a>
        </div>
    </div>

    <!-- Key Analytics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <span class="text-muted text-xs fw-bold text-uppercase">Total Incomplete Carts</span>
                <h3 class="fw-extrabold text-primary mb-0 mt-2">{{ number_format($metrics['total_incomplete_carts']) }}</h3>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <span class="text-muted text-xs fw-bold text-uppercase">Potential Revenue Lost</span>
                <h3 class="fw-extrabold text-danger mb-0 mt-2">৳{{ number_format($metrics['potential_revenue_lost'], 2) }}</h3>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <span class="text-muted text-xs fw-bold text-uppercase">Recovered Carts</span>
                <h3 class="fw-extrabold text-success mb-0 mt-2">{{ number_format($metrics['recovered_count']) }}</h3>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white">
                <span class="text-white-50 text-xs fw-bold text-uppercase">Recovered Revenue</span>
                <h3 class="fw-extrabold text-white mb-0 mt-2">৳{{ number_format($metrics['recovered_revenue'], 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Product Level Drop-off Analysis Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-0">
            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-boxes me-2 text-warning"></i>Product Level Drop-off Analysis</h5>
            <small class="text-muted">Identify products frequently added to cart but abandoned before checkout completion.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="bg-light text-muted text-xs text-uppercase">
                        <tr>
                            <th class="ps-4">Product Name</th>
                            <th>SKU</th>
                            <th class="text-center">Abandoned Carts</th>
                            <th class="text-center">Abandoned Qty</th>
                            <th class="text-end">Potential Revenue</th>
                            <th class="text-end pe-4">Recovered Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productAnalysis as $prod)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $prod->name }}</td>
                                <td><code>{{ $prod->sku ?? 'N/A' }}</code></td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark rounded-pill px-3">{{ $prod->abandoned_cart_count }} Carts</span>
                                </td>
                                <td class="text-center fw-bold">{{ $prod->abandoned_qty }} Pcs</td>
                                <td class="text-end fw-extrabold text-danger">৳{{ number_format($prod->potential_revenue, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-success">{{ $prod->recovered_qty }} Pcs</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No product drop-off analytics data available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
