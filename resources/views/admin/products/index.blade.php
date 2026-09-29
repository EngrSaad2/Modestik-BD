@extends('layouts.admin')

@section('title', 'Products')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Products Management</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Manage inventory, profit margins, bulk actions, and search products.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Product</a>
</div>

<!-- Stats Row -->
@if(isset($stats))
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block" style="font-size:12px; font-weight:600;">TOTAL PRODUCTS</span>
                    <h4 class="fw-bold mb-0" style="color: #000000 !important;">{{ number_format($stats['total_products']) }}</h4>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                    <i class="fas fa-box fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block" style="font-size:12px; font-weight:600;">TOTAL STOCK UNITS</span>
                    <h4 class="fw-bold mb-0" style="color: #000000 !important;">{{ number_format($stats['total_stock']) }}</h4>
                </div>
                <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info">
                    <i class="fas fa-cubes fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block" style="font-size:12px; font-weight:600;">TOTAL STOCK VALUE</span>
                    <h4 class="fw-bold mb-0" style="color: #000000 !important;">৳{{ number_format($stats['total_value']) }}</h4>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success">
                    <i class="fas fa-coins fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted d-block" style="font-size:12px; font-weight:600;">POTENTIAL PROFIT</span>
                    <h4 class="fw-bold mb-0" style="color: #000000 !important;">৳{{ number_format($stats['potential_profit']) }}</h4>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning">
                    <i class="fas fa-chart-line fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="table-card">
    <div class="card-header bg-white py-3">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search by name, SKU..." value="{{ request('search') }}" style="font-size: 11.5px;">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 11.5px; padding-right: 20px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 11.5px;">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2 justify-content-end align-items-center">
                <button type="submit" class="btn btn-sm btn-primary px-3" style="font-size: 12px;"><i class="fas fa-filter me-1"></i>Filter</button>
                @if(request()->anyFilled(['search', 'category_id', 'status', 'trash']))
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="font-size: 12px;">Reset</a>
                @endif
                <a href="{{ route('admin.products.index', array_merge(request()->query(), ['trash' => request('trash') == '1' ? '0' : '1'])) }}" class="btn btn-sm {{ request('trash') == '1' ? 'btn-danger' : 'btn-outline-danger' }} px-3" style="font-size: 12px;">
                    <i class="fas fa-trash-alt me-1"></i>{{ request('trash') == '1' ? 'View All' : 'Trash' }}
                </a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <!-- Bulk Actions Form -->
        <form action="{{ route('admin.products.bulk-action') }}" method="POST" id="bulkActionForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <select name="action" class="form-select form-select-sm" style="width: 180px;" required>
                        <option value="">-- Bulk Actions --</option>
                        @if(request('trash') == '1')
                            <option value="restore">Restore Selected</option>
                            <option value="force_delete">Permanently Delete</option>
                        @else
                            <option value="publish">Publish (Active)</option>
                            <option value="unpublish">Unpublish (Inactive)</option>
                            <option value="feature">Mark Featured</option>
                            <option value="unfeature">Remove Featured</option>
                            <option value="delete">Move to Trash</option>
                        @endif
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Apply bulk action to selected products?')">Apply</button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="selectedCountText">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th style="width: 60px;">Image</th>
                            <th>Name</th>
                            <th>Price / Cost</th>
                            <th>Profit</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="form-check-input product-checkbox">
                                </td>
                                <td>
                                    <img src="{{ $product->primary_image_url }}" alt="img" class="image-preview rounded shadow-sm" style="width:40px; height:40px; object-fit:cover;">
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    @if($product->is_featured || $product->is_flash_sale)
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @if($product->is_featured)
                                                <span class="badge bg-success text-white" style="font-size:10px;">Featured</span>
                                            @endif
                                            @if($product->is_flash_sale)
                                                <span class="badge bg-warning text-dark" style="font-size:10px;">Flash</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">৳{{ number_format($product->current_price) }}</span>
                                    <small class="text-muted d-block" style="font-size:11px;">Cost: ৳{{ number_format($product->cost ?? 0) }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">৳{{ number_format($product->profit) }}</span>
                                    <small class="text-muted d-block" style="font-size:11px;">({{ $product->profit_margin }}%)</small>
                                </td>
                                <td>
                                    <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->quantity }} units
                                    </span>
                                </td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" title="Clone Product" onclick="if(confirm('Clone this product?')) { document.getElementById('clone-form-{{ $product->id }}').submit(); }">
                                            <i class="fas fa-clone"></i>
                                        </button>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" onclick="if(confirm('Delete product?')) { document.getElementById('delete-form-{{ $product->id }}').submit(); }"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($products as $product)
            <form id="clone-form-{{ $product->id }}" action="{{ route('admin.products.clone', $product) }}" method="POST" class="d-none">
                @csrf
            </form>
            <form id="delete-form-{{ $product->id }}" action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="d-flex justify-content-center p-3">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const countText = document.getElementById('selectedCountText');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        countText.textContent = checkedCount + ' items selected';
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateCount);
    });
});
</script>
@endsection
