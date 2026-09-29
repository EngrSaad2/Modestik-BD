@extends('layouts.admin')

@section('title', 'Reviews')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Reviews</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Reviews</h4>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Product Reviews</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.reviews.bulk-delete') }}" method="POST" id="bulkReviewForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkReviewDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="reviewSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllReviews">
                            </th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $review->id }}" class="form-check-input review-checkbox">
                                </td>
                                <td><strong>{{ $review->user->name ?? 'Unknown User' }}</strong></td>
                                <td>
                                    @if($review->product)
                                        <a href="{{ route('products.show', $review->product->slug) }}" target="_blank">
                                            {{ Str::limit($review->product->name, 30) }}
                                        </a>
                                    @else
                                        <span class="text-muted">Deleted Product</span>
                                    @endif
                                </td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </td>
                                <td><small class="text-muted">{{ Str::limit($review->comment, 60) }}</small></td>
                                <td>
                                    <span class="badge {{ $review->status === 'approved' ? 'bg-success' : ($review->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ ucfirst($review->status) }}
                                    </span>
                                </td>
                                <td>{{ $review->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No reviews found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        <div class="p-3">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkReviewDelete() {
    const count = document.querySelectorAll('.review-checkbox:checked').length;
    if (count === 0) {
        alert('Please select at least one review to delete.');
        return false;
    }
    return confirm('⚠️ Are you sure you want to delete ' + count + ' selected review(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllReviews');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    const countText = document.getElementById('reviewSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.review-checkbox:checked').length;
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
