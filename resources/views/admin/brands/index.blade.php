@extends('layouts.admin')

@section('title', 'Brands')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Brands</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Brands</h4>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Brand</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Brands</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.brands.bulk-delete') }}" method="POST" id="bulkBrandForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkBrandDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="brandSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllBrands">
                            </th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Website</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $brand->id }}" class="form-check-input brand-checkbox">
                                </td>
                                <td>
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="logo" class="image-preview">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td><strong>{{ $brand->name }}</strong></td>
                                <td>{{ $brand->slug }}</td>
                                <td>{{ $brand->website ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $brand->is_featured ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $brand->is_featured ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $brand->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $brand->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No brands found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
function confirmBulkBrandDelete() {
    const count = document.querySelectorAll('.brand-checkbox:checked').length;
    if (count === 0) {
        alert('Please select at least one brand to delete.');
        return false;
    }
    return confirm('⚠️ Are you sure you want to delete ' + count + ' selected brand(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllBrands');
    const checkboxes = document.querySelectorAll('.brand-checkbox');
    const countText = document.getElementById('brandSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.brand-checkbox:checked').length;
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
