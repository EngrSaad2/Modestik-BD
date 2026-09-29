@extends('layouts.admin')

@section('title', 'Navigation Menus')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Menus</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Navigation Menus</h4>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Store Menus</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.menus.bulk-delete') }}" method="POST" id="bulkMenuForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkMenuDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="menuSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllMenus">
                            </th>
                            <th>Menu Name</th>
                            <th>Location</th>
                            <th>Items Count</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $menu->id }}" class="form-check-input menu-checkbox">
                                </td>
                                <td><strong>{{ $menu->name }}</strong></td>
                                <td><code>{{ $menu->location ?? 'default' }}</code></td>
                                <td><span class="badge bg-secondary">{{ $menu->allItems->count() }}</span></td>
                                <td>
                                    <span class="badge {{ $menu->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $menu->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete menu {{ addslashes($menu->name) }}?')) document.getElementById('delete-menu-{{ $menu->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No navigation menus found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($menus as $menu)
            <form id="delete-menu-{{ $menu->id }}" action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $menus->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkMenuDelete() {
    const checkedCount = document.querySelectorAll('.menu-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one menu to delete.');
        return false;
    }
    return confirm('Are you sure you want to delete ' + checkedCount + ' selected menu(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllMenus');
    const checkboxes = document.querySelectorAll('.menu-checkbox');
    const countText = document.getElementById('menuSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.menu-checkbox:checked').length;
        if (countText) countText.textContent = checkedCount + ' items selected';
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked && selectAll) selectAll.checked = false;
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.menu-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
