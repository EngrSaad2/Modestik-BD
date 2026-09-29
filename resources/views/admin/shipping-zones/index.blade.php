@extends('layouts.admin')

@section('title', 'Shipping Zones')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Shipping Zones</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Shipping Zones</h4>
    <a href="{{ route('admin.shipping-zones.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Shipping Zone</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Shipping Zones & Rates</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.shipping-zones.bulk-delete') }}" method="POST" id="bulkShippingForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkShippingDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="shippingSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllShipping">
                            </th>
                            <th>Name</th>
                            <th>Charge</th>
                            <th>Delivery Days</th>
                            <th>Free Shipping threshold</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shippingZones as $zone)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $zone->id }}" class="form-check-input shipping-checkbox">
                                </td>
                                <td><strong>{{ $zone->name }}</strong></td>
                                <td>৳{{ number_format($zone->charge, 2) }}</td>
                                <td>{{ $zone->min_days }} - {{ $zone->max_days }} days</td>
                                <td>{{ $zone->free_shipping_min ? '৳' . number_format($zone->free_shipping_min, 2) : 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $zone->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $zone->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.shipping-zones.edit', $zone) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete shipping zone {{ addslashes($zone->name) }}?')) document.getElementById('delete-zone-{{ $zone->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No shipping zones found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($shippingZones as $zone)
            <form id="delete-zone-{{ $zone->id }}" action="{{ route('admin.shipping-zones.destroy', $zone) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $shippingZones->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkShippingDelete() {
    const checkedCount = document.querySelectorAll('.shipping-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one shipping zone to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected shipping zone(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllShipping');
    const checkboxes = document.querySelectorAll('.shipping-checkbox');
    const countText = document.getElementById('shippingSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.shipping-checkbox:checked').length;
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
            if (selectAll && checkboxes.length > 0 && document.querySelectorAll('.shipping-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
