@extends('layouts.admin')

@section('title', 'Customers')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Customers</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Customers</h4>
</div>

<div class="table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Customers</h5>
        <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex gap-2">
            <select name="vip" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Customers</option>
                <option value="1" {{ request('vip') == '1' ? 'selected' : '' }}>VIP Customers Only</option>
                <option value="0" {{ request('vip') == '0' ? 'selected' : '' }}>Non-VIP Customers Only</option>
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.customers.bulk-delete') }}" method="POST" id="bulkCustomerForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkCustomerDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="customerSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllCustomers">
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Orders</th>
                            <th>Status</th>
                            <th>Registered At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $customer->id }}" class="form-check-input customer-checkbox">
                                </td>
                                <td>
                                    <strong>{{ $customer->name }}</strong>
                                    @if($customer->is_vip)
                                        <span class="badge bg-warning text-dark ms-1" style="font-size:10px;"><i class="fas fa-crown"></i> VIP</span>
                                    @endif
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $customer->orders_count }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $customer->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                                <td>{{ $customer->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-info" title="View Customer Details"><i class="fas fa-eye"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete customer {{ addslashes($customer->name) }}?')) document.getElementById('delete-cust-{{ $customer->id }}').submit();" title="Delete Customer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($customers as $customer)
            <form id="delete-cust-{{ $customer->id }}" action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkCustomerDelete() {
    const checkedCount = document.querySelectorAll('.customer-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one customer to delete.');
        return false;
    }
    return confirm('⚠️ Are you sure you want to permanently delete ' + checkedCount + ' selected customer(s)? This cannot be undone!');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllCustomers');
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    const countText = document.getElementById('customerSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.customer-checkbox:checked').length;
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
