@extends('layouts.admin')

@section('title', 'Taxes')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Taxes</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Taxes</h4>
    <a href="{{ route('admin.taxes.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Tax Rate</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Tax Rules & Rates</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.taxes.bulk-delete') }}" method="POST" id="bulkTaxForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkTaxDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="taxSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllTaxes">
                            </th>
                            <th>Tax Name</th>
                            <th>Rate</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taxes as $tax)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $tax->id }}" class="form-check-input tax-checkbox">
                                </td>
                                <td><strong>{{ $tax->name }}</strong></td>
                                <td>{{ $tax->type === 'percentage' ? $tax->rate . '%' : '৳' . number_format($tax->rate, 2) }}</td>
                                <td class="text-capitalize">{{ $tax->type }}</td>
                                <td>
                                    <span class="badge {{ $tax->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $tax->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.taxes.edit', $tax) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete tax rule {{ addslashes($tax->name) }}?')) document.getElementById('delete-tax-{{ $tax->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No taxes found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($taxes as $tax)
            <form id="delete-tax-{{ $tax->id }}" action="{{ route('admin.taxes.destroy', $tax) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $taxes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkTaxDelete() {
    const checkedCount = document.querySelectorAll('.tax-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one tax rule to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected tax rule(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllTaxes');
    const checkboxes = document.querySelectorAll('.tax-checkbox');
    const countText = document.getElementById('taxSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.tax-checkbox:checked').length;
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
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.tax-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
