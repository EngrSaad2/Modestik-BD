@extends('layouts.admin')

@section('title', 'Inventory Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Inventory Log</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Inventory & Stock Tracking</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.inventory.valuation') }}" class="btn btn-outline-primary">
            <i class="fas fa-chart-line me-2"></i>Inventory Valuation
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#restockModal">
            <i class="fas fa-plus-circle me-2"></i>Restock Product
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters -->
<div class="table-card mb-4 p-3 bg-white">
    <form action="{{ route('admin.inventory.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label" style="font-size:12px;">Filter Product</label>
            <select name="product_id" class="form-select form-select-sm">
                <option value="">All Products</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" style="font-size:12px;">Transaction Type</label>
            <select name="type" class="form-select form-select-sm">
                <option value="">All Types</option>
                <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale (বিক্রি)</option>
                <option value="restock" {{ request('type') === 'restock' ? 'selected' : '' }}>Restock (রিস্টক)</option>
                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment (সমন্বয়)</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search me-1"></i>Filter</button>
        </div>
    </form>
</div>

<!-- Stock History Logs Table -->
<div class="table-card">
    <div class="card-header">
        <h5 class="mb-0">Stock History Log</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Option</th>
                        <th>Type</th>
                        <th>Quantity Change</th>
                        <th>Unit Cost</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                @if($log->product)
                                    <strong>{{ $log->product->name }}</strong>
                                @else
                                    <span class="text-muted">Deleted Product</span>
                                @endif
                            </td>
                            <td>
                                @if($log->variant)
                                    <span class="badge bg-light text-dark">{{ $log->variant->display_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $log->type === 'restock' ? 'bg-success' : ($log->type === 'sale' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>
                            <td>
                                <strong class="{{ $log->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $log->quantity > 0 ? '+' : '' }}{{ $log->quantity }}
                                </strong>
                            </td>
                            <td>{{ $log->cost ? '৳' . number_format($log->cost, 2) : '-' }}</td>
                            <td><small class="text-muted">{{ $log->notes ?? '-' }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No stock logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Restock Modal -->
<div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="restockModalLabel">Restock Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.inventory.restock') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Product</label>
                        <select name="product_id" id="restockProductSelect" class="form-select" required>
                            <option value="" disabled selected>Choose Product...</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" data-has-variants="{{ $p->has_variants ? 'true' : 'false' }}">
                                    {{ $p->name }} (Current stock: {{ $p->quantity }} units)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="variantSelectContainer">
                        <label class="form-label fw-bold">Select Product Option (Variant)</label>
                        <select name="variant_id" id="restockVariantSelect" class="form-select">
                            <option value="">Select Option...</option>
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Restock Quantity</label>
                            <input type="number" name="quantity" class="form-control" required min="1" placeholder="10">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Unit Purchase Cost (BDT)</label>
                            <input type="number" name="cost" step="0.01" class="form-control" required min="0" placeholder="0.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes / Comments</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g., Supplier XYZ restock lot #1"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Restock Now</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Product data for dynamic variants load
    const productsData = @json($products);

    $('#restockProductSelect').change(function() {
        let pId = $(this).val();
        let selectedOption = $(this).find('option:selected');
        let hasVariants = selectedOption.data('has-variants') === true || selectedOption.data('has-variants') === 'true';
        let varContainer = $('#variantSelectContainer');
        let varSelect = $('#restockVariantSelect');

        varSelect.empty().append('<option value="">Select Option...</option>');
        
        if (hasVariants) {
            varContainer.removeClass('d-none');
            varSelect.attr('required', true);
            
            // Find selected product's variants
            let product = productsData.find(p => p.id == pId);
            if (product && product.variants) {
                product.variants.forEach(variant => {
                    varSelect.append(`<option value="${variant.id}">${variant.display_name} (Stock: ${variant.quantity} units)</option>`);
                });
            }
        } else {
            varContainer.addClass('d-none');
            varSelect.removeAttr('required');
        }
    });
</script>
@endsection
