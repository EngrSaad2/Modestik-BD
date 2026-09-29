@extends('layouts.admin')

@section('title', 'FAQs')

@section('breadcrumbs')
    <li class="breadcrumb-item active">FAQs</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Frequently Asked Questions</h4>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage FAQs</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.faqs.bulk-delete') }}" method="POST" id="bulkFaqForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkFaqDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="faqSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllFaqs">
                            </th>
                            <th>Question</th>
                            <th>Category</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $faq->id }}" class="form-check-input faq-checkbox">
                                </td>
                                <td><strong>{{ $faq->question }}</strong></td>
                                <td><span class="badge bg-secondary">{{ $faq->category ?? 'General' }}</span></td>
                                <td>{{ $faq->order }}</td>
                                <td>
                                    <span class="badge {{ $faq->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $faq->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete this FAQ?')) document.getElementById('delete-faq-{{ $faq->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No FAQs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($faqs as $faq)
            <form id="delete-faq-{{ $faq->id }}" action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $faqs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkFaqDelete() {
    const checkedCount = document.querySelectorAll('.faq-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one FAQ to delete.');
        return false;
    }
    return confirm('Are you sure you want to delete ' + checkedCount + ' selected FAQ(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllFaqs');
    const checkboxes = document.querySelectorAll('.faq-checkbox');
    const countText = document.getElementById('faqSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.faq-checkbox:checked').length;
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
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.faq-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
