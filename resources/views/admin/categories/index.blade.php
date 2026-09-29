@extends('layouts.admin')

@section('title', 'Categories')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Categories</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Category</a>
</div>

<div class="table-card">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search categories by name, slug, or description..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-6 d-flex gap-2 justify-content-end">
                <button type="submit" class="btn btn-sm btn-primary px-3"><i class="fas fa-search me-1"></i>Search</button>
                @if(request()->filled('search'))
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.categories.bulk-delete') }}" method="POST" id="bulkCategoryForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkCategoryDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="catSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllCategories">
                            </th>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th>Subcategories</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody">
                        @forelse($categories as $cat)
                            <tr data-id="{{ $cat->id }}">
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $cat->id }}" class="form-check-input cat-checkbox">
                                </td>
                                <td><span style="font-size: 18px; color: var(--admin-primary);">{!! $cat->icon ?? '<i class="fas fa-folder"></i>' !!}</span></td>
                                <td><strong>{{ $cat->name }}</strong></td>
                                <td>{{ $cat->slug }}</td>
                                <td>
                                    <span class="badge {{ $cat->is_featured ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $cat->is_featured ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $cat->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $cat->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $cat->children->count() }} Subcategories</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @foreach($cat->children as $child)
                                <tr data-id="{{ $child->id }}" class="bg-light">
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $child->id }}" class="form-check-input cat-checkbox">
                                    </td>
                                    <td></td>
                                    <td><span class="text-muted me-2">—</span> {{ $child->name }}</td>
                                    <td>{{ $child->slug }}</td>
                                    <td>-</td>
                                    <td>
                                        <span class="badge {{ $child->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $child->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>Child Category</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('admin.categories.destroy', $child) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No categories found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        @if(method_exists($categories, 'links'))
            <div class="p-3">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<script>
function confirmBulkCategoryDelete() {
    const count = document.querySelectorAll('.cat-checkbox:checked').length;
    if (count === 0) {
        alert('Please select at least one category to delete.');
        return false;
    }
    return confirm('⚠️ Are you sure you want to delete ' + count + ' selected category(ies)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllCategories');
    const checkboxes = document.querySelectorAll('.cat-checkbox');
    const countText = document.getElementById('catSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.cat-checkbox:checked').length;
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
