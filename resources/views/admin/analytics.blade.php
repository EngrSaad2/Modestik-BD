@extends('layouts.admin')

@section('title', 'Analytics & Insights')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Analytics</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Store Analytics</h4>
        <p class="text-muted small mb-0">Product popularity, views, and catalog distribution</p>
    </div>
    <a href="{{ route('admin.reports.sales') }}" class="btn btn-admin-primary">
        <i class="fas fa-file-invoice-dollar me-1"></i> Financial Reports
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Top Products by Views -->
    <div class="col-lg-7">
        <div class="table-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-eye text-primary me-2"></i>Most Viewed Products</h5>
                <span class="badge bg-light text-muted border font-monospace">Top 10</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Views</th>
                                <th>Reviews</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $prod)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($prod->primary_image_url)
                                                <img src="{{ $prod->primary_image_url }}" alt="" style="width:36px; height:36px; object-fit:cover; border-radius:6px;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong class="d-block text-truncate" style="max-width:200px;">{{ $prod->name }}</strong>
                                                <small class="text-muted">{{ $prod->sku ?? 'No SKU' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>৳{{ number_format($prod->price, 2) }}</td>
                                    <td><span class="badge bg-info text-dark font-monospace">{{ number_format($prod->views) }}</span></td>
                                    <td><span class="badge bg-secondary font-monospace">{{ $prod->reviews_count }}</span></td>
                                    <td>
                                        <span class="badge {{ $prod->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($prod->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No product data available yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="col-lg-5">
        <div class="table-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-sitemap text-warning me-2"></i>Catalog by Category</h5>
                <span class="badge bg-light text-muted border font-monospace">Parent Categories</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Products</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryStats as $cat)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($cat->image)
                                                <img src="{{ asset('storage/' . $cat->image) }}" alt="" style="width:28px; height:28px; object-fit:cover; border-radius:50%;">
                                            @endif
                                            <strong>{{ $cat->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary rounded-pill font-monospace">{{ $cat->products_count }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-4">No category data found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
