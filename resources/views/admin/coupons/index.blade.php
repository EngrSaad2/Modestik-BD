@extends('layouts.admin')

@section('title', 'Coupons')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Coupons</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Coupons</h4>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Coupon</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Discount Coupons</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.coupons.bulk-delete') }}" method="POST" id="bulkCouponForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkCouponDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="couponSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllCoupons">
                            </th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Min Order</th>
                            <th>Usage Limit</th>
                            <th>Used Count</th>
                            <th>Expires At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $coupon->id }}" class="form-check-input coupon-checkbox">
                                </td>
                                <td><strong><code>{{ $coupon->code }}</code></strong></td>
                                <td class="text-capitalize">{{ $coupon->type }}</td>
                                <td>
                                    {{ $coupon->type === 'percentage' ? $coupon->value . '%' : '৳' . number_format($coupon->value, 2) }}
                                </td>
                                <td>{{ $coupon->min_order ? '৳' . number_format($coupon->min_order, 2) : '-' }}</td>
                                <td>{{ $coupon->usage_limit ?? 'Unlimited' }}</td>
                                <td>{{ $coupon->used_count }}</td>
                                <td>{{ $coupon->expires_at ? $coupon->expires_at->format('d M Y') : 'Never' }}</td>
                                <td>
                                    <span class="badge {{ $coupon->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $coupon->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-4">No coupons found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        <div class="p-3">
            {{ $coupons->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkCouponDelete() {
    const count = document.querySelectorAll('.coupon-checkbox:checked').length;
    if (count === 0) {
        alert('Please select at least one coupon to delete.');
        return false;
    }
    return confirm('⚠️ Are you sure you want to delete ' + count + ' selected coupon(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllCoupons');
    const checkboxes = document.querySelectorAll('.coupon-checkbox');
    const countText = document.getElementById('couponSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.coupon-checkbox:checked').length;
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
