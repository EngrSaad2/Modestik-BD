@extends('layouts.admin')

@section('title', 'Pages')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Pages</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Pages</h4>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Page</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Static Pages</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.pages.bulk-delete') }}" method="POST" id="bulkPageForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkPageDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="pageSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllPages">
                            </th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Meta Title</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $page->id }}" class="form-check-input page-checkbox">
                                </td>
                                <td>
                                    @if($page->featured_image)
                                        <img src="{{ asset('storage/' . $page->featured_image) }}" alt="page" class="image-preview" style="height: 40px; width: 60px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td><strong>{{ $page->title }}</strong></td>
                                <td><code>/page/{{ $page->slug }}</code></td>
                                <td>{{ $page->meta_title ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $page->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $page->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete page {{ addslashes($page->title) }}?')) document.getElementById('delete-page-{{ $page->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No pages found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($pages as $page)
            <form id="delete-page-{{ $page->id }}" action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $pages->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkPageDelete() {
    const checkedCount = document.querySelectorAll('.page-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one page to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected page(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllPages');
    const checkboxes = document.querySelectorAll('.page-checkbox');
    const countText = document.getElementById('pageSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.page-checkbox:checked').length;
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
            if (selectAll && document.querySelectorAll('.page-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
