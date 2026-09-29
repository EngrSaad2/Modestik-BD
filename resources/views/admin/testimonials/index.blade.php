@extends('layouts.admin')

@section('title', 'Testimonials')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Testimonials</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Testimonials</h4>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Testimonial</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Customer Testimonials</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.testimonials.bulk-delete') }}" method="POST" id="bulkTestimonialForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkTestimonialDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="testimonialSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllTestimonials">
                            </th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Rating</th>
                            <th>Content</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($testimonials as $testimonial)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $testimonial->id }}" class="form-check-input testimonial-checkbox">
                                </td>
                                <td>
                                    @if($testimonial->image)
                                        <img src="{{ asset('storage/' . $testimonial->image) }}" alt="avatar" class="image-preview" style="height: 40px; width: 40px; object-fit: cover; border-radius: 50%;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td><strong>{{ $testimonial->name }}</strong></td>
                                <td>{{ $testimonial->designation ?? '-' }}</td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </td>
                                <td><small class="text-muted">{{ Str::limit($testimonial->content, 50) }}</small></td>
                                <td>{{ $testimonial->order }}</td>
                                <td>
                                    <span class="badge {{ $testimonial->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $testimonial->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete this testimonial?')) document.getElementById('delete-testi-{{ $testimonial->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No testimonials found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($testimonials as $testimonial)
            <form id="delete-testi-{{ $testimonial->id }}" action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $testimonials->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkTestimonialDelete() {
    const checkedCount = document.querySelectorAll('.testimonial-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one testimonial to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected testimonial(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllTestimonials');
    const checkboxes = document.querySelectorAll('.testimonial-checkbox');
    const countText = document.getElementById('testimonialSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.testimonial-checkbox:checked').length;
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
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.testimonial-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
