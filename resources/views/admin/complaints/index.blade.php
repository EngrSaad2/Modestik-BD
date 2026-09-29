@extends('layouts.admin')

@section('title', 'Complaints')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Complaints</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Complaints</h4>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Customer Complaints</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.complaints.bulk-delete') }}" method="POST" id="bulkComplaintForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkComplaintDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="complaintSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllComplaints">
                            </th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Order ID</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $comp)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $comp->id }}" class="form-check-input complaint-checkbox">
                                </td>
                                <td><strong>{{ $comp->name }}</strong></td>
                                <td>{{ $comp->phone }}</td>
                                <td>{{ $comp->order_number ?? 'N/A' }}</td>
                                <td>{{ Str::limit($comp->subject, 40) }}</td>
                                <td>
                                    <span class="badge {{ $comp->status === 'resolved' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($comp->status) }}
                                    </span>
                                </td>
                                <td>{{ $comp->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.complaints.show', $comp) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete this complaint?')) document.getElementById('delete-comp-{{ $comp->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No complaints found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($complaints as $comp)
            <form id="delete-comp-{{ $comp->id }}" action="{{ route('admin.complaints.destroy', $comp) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $complaints->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkComplaintDelete() {
    const checkedCount = document.querySelectorAll('.complaint-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one complaint to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected complaint(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllComplaints');
    const checkboxes = document.querySelectorAll('.complaint-checkbox');
    const countText = document.getElementById('complaintSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.complaint-checkbox:checked').length;
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
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.complaint-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
