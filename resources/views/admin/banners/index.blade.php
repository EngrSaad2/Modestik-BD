@extends('layouts.admin')

@section('title', 'Banners')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Banners</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Banners</h4>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Banner</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Promotional Banners</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.banners.bulk-delete') }}" method="POST" id="bulkBannerForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkBannerDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="bannerSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllBanners">
                            </th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Position</th>
                            <th>URL</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $banner->id }}" class="form-check-input banner-checkbox">
                                </td>
                                <td>
                                    @if($banner->image)
                                        <img src="{{ asset('storage/' . $banner->image) }}" alt="banner" class="image-preview" style="height: 45px; width: 90px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td><strong>{{ $banner->title ?? '-' }}</strong></td>
                                <td class="text-capitalize">{{ $banner->position }}</td>
                                <td>{{ $banner->url ?? '-' }}</td>
                                <td>{{ $banner->order }}</td>
                                <td>
                                    <span class="badge {{ $banner->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $banner->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete this banner?')) document.getElementById('delete-banner-{{ $banner->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No banners found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($banners as $banner)
            <form id="delete-banner-{{ $banner->id }}" action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $banners->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkBannerDelete() {
    const checkedCount = document.querySelectorAll('.banner-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one banner to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected banner(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllBanners');
    const checkboxes = document.querySelectorAll('.banner-checkbox');
    const countText = document.getElementById('bannerSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.banner-checkbox:checked').length;
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
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.banner-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
