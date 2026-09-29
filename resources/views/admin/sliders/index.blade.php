@extends('layouts.admin')

@section('title', 'Sliders')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Sliders</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Sliders</h4>
    <a href="{{ route('admin.sliders.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Slider</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Hero Sliders</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.sliders.bulk-delete') }}" method="POST" id="bulkSliderForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkSliderDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="sliderSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllSliders">
                            </th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Button Text</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sliders as $slider)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $slider->id }}" class="form-check-input slider-checkbox">
                                </td>
                                <td>
                                    <img src="{{ asset('storage/' . $slider->image) }}" alt="slider" class="image-preview" style="height: 45px; width: 90px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td><strong>{{ $slider->title ?? '-' }}</strong></td>
                                <td>{{ $slider->subtitle ?? '-' }}</td>
                                <td>{{ $slider->button_text ?? '-' }}</td>
                                <td>{{ $slider->order }}</td>
                                <td>
                                    <span class="badge {{ $slider->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $slider->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete this slider?')) document.getElementById('delete-slider-{{ $slider->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No sliders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($sliders as $slider)
            <form id="delete-slider-{{ $slider->id }}" action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $sliders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkSliderDelete() {
    const checkedCount = document.querySelectorAll('.slider-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one slider to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected slider(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllSliders');
    const checkboxes = document.querySelectorAll('.slider-checkbox');
    const countText = document.getElementById('sliderSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.slider-checkbox:checked').length;
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
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.slider-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
