@extends('layouts.admin')

@section('title', 'Inventory Value')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-cubes text-secondary me-2"></i>Inventory Valuation (At Cost Price)</h4>
                <p class="text-muted mb-0 small">Total asset valuation of current inventory based on cost prices</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-secondary bg-opacity-10 text-dark rounded-4 d-flex align-items-center gap-3">
                    <i class="fas fa-boxes fa-2x"></i>
                    <div>
                        <span class="text-xs fw-bold text-uppercase d-block">Total Stock Quantity</span>
                        <h3 class="fw-extrabold mb-0">{{ number_format($totalQty) }} pcs</h3>
                    </div>
                </div>
                <div class="p-3 bg-dark bg-opacity-10 text-dark rounded-4 d-flex align-items-center gap-3">
                    <i class="fas fa-tags fa-2x"></i>
                    <div>
                        <span class="text-xs fw-bold text-uppercase d-block">Total Stock Cost Value</span>
                        <h3 class="fw-extrabold mb-0">৳{{ number_format($totalStockCost, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-warning bg-opacity-10 border border-warning border-opacity-20">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Low Stock Alert</h6>
                        <span class="fs-5 fw-extrabold text-warning">{{ $lowStockProducts->count() }} products low in stock</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-danger bg-opacity-10 border border-danger border-opacity-20">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Out of Stock Alert</h6>
                        <span class="fs-5 fw-extrabold text-danger">{{ $outOfStockCount }} products out of stock</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Listing -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted text-xs text-uppercase">
                            <th class="ps-4">Product</th>
                            <th>Category</th>
                            <th>Selling Price</th>
                            <th>Cost Price</th>
                            <th>Stock Quantity</th>
                            <th class="text-end pe-4">Total Stock Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $prod)
                            @php
                                $cogs = $prod->cost ?? 0;
                                $stockVal = $cogs * $prod->quantity;
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $prod->primary_image_url }}" alt="Img" width="40" height="40" class="rounded-3 object-fit-cover">
                                        <div>
                                            <span class="fw-bold text-dark d-block">{{ $prod->name }}</span>
                                            <small class="text-muted font-monospace">{{ $prod->sku }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ $prod->category?->name ?? '-' }}</span></td>
                                <td>৳{{ number_format($prod->current_price, 2) }}</td>
                                <td class="fw-bold text-danger">৳{{ number_format($cogs, 2) }}</td>
                                <td>
                                    <span class="badge {{ $prod->quantity > 5 ? 'bg-success' : ($prod->quantity > 0 ? 'bg-warning' : 'bg-danger') }} rounded-pill px-3">
                                        {{ $prod->quantity }} pcs
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-extrabold text-dark fs-6">৳{{ number_format($stockVal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No inventory records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
