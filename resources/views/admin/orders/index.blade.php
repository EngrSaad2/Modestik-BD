@extends('layouts.admin')

@section('title', 'Orders')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Orders</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Orders Management</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Manage customer orders, automated SteadFast Courier dispatch, settlement, and tracking.</p>
    </div>
    <a href="{{ route('admin.couriers.settings') }}" class="btn btn-outline-primary btn-sm fw-bold">
        <i class="fas fa-cog me-1"></i>Courier Setup
    </a>
</div>

<!-- Status Tabs -->
<div class="bg-white rounded-3 p-3 shadow-sm mb-4">
    <div class="d-flex gap-2 flex-wrap" style="font-size: 13px;">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-light' }}">All Orders</a>
        @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'] as $st)
            <a href="{{ route('admin.orders.index', array_merge(request()->except('page'), ['status' => $st])) }}" 
               class="btn btn-sm {{ request('status') == $st ? 'btn-primary' : 'btn-light' }} text-capitalize">
               {{ $st }}
            </a>
        @endforeach
        @php $incompleteCount = \App\Models\Cart::incomplete()->count(); @endphp
        <a href="{{ route('admin.orders.incomplete') }}" class="btn btn-sm {{ request()->routeIs('admin.orders.incomplete') ? 'btn-danger' : 'btn-outline-danger' }} fw-bold border-2 d-inline-flex align-items-center gap-1">
            <i class="fas fa-shopping-cart me-1"></i>Incomplete Orders
            @if($incompleteCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $incompleteCount }}</span>
            @endif
        </a>
    </div>
</div>

<div class="table-card">
    <div class="card-header bg-white py-3">
        <!-- Advanced Search & Filter Form -->
        <form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
            <div class="input-group input-group-sm" style="flex: 1; min-width: 180px; max-width: 250px;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search order #, customer, phone..." value="{{ request('search') }}" style="font-size: 11px;">
            </div>
            <div style="flex: 1; min-width: 125px;">
                <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 11px; padding-right: 18px;">
                    <option value="">Payment Status</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partially_paid" {{ request('payment_status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 120px;">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 11px; padding-right: 18px;">
                    <option value="">Order Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 120px;">
                <select name="courier_status" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 11px; padding-right: 18px;">
                    <option value="">Courier Status</option>
                    <option value="unassigned" {{ request('courier_status') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                    <option value="courier_assigned" {{ request('courier_status') == 'courier_assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="pending_pickup" {{ request('courier_status') == 'pending_pickup' ? 'selected' : '' }}>Pending Pickup</option>
                    <option value="in_transit" {{ request('courier_status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="delivered" {{ request('courier_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('courier_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 120px;">
                <select name="source" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 11px; padding-right: 18px;">
                    <option value="">Order Source</option>
                    <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
                    <option value="messenger" {{ request('source') == 'messenger' ? 'selected' : '' }}>Messenger</option>
                    <option value="instagram" {{ request('source') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                    <option value="whatsapp" {{ request('source') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="direct_call" {{ request('source') == 'direct_call' ? 'selected' : '' }}>Direct Call</option>
                    <option value="other" {{ request('source') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 120px;">
                <input type="text" name="district" class="form-control form-control-sm" placeholder="District (e.g. Dhaka)" value="{{ request('district') }}" style="font-size: 11px;">
            </div>
            <div class="d-flex gap-1 ms-auto">
                <button type="submit" class="btn btn-sm btn-primary px-3" style="font-size: 11px;"><i class="fas fa-filter me-1"></i>Filter</button>
                @if(request()->anyFilled(['search', 'status', 'payment_status', 'courier_status', 'source', 'district']))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size: 11px;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <!-- Bulk Actions Form -->
        <form action="{{ route('admin.orders.bulk-status') }}" method="POST" id="bulkOrderForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    
                    <button type="submit" formaction="{{ route('admin.orders.push-courier') }}" class="btn btn-sm btn-info text-white fw-bold px-2" style="font-size:11px;">
                        <i class="fas fa-paper-plane me-1"></i>Push to SteadFast
                    </button>

                    <button type="submit" name="action" value="settle_courier" class="btn btn-sm btn-success fw-bold px-2 ms-1" style="font-size:11px;" onclick="return confirm('Settle courier for selected orders? Sets Status to Confirmed & Paid and pushes to SteadFast if needed.')">
                        <i class="fas fa-handshake me-1"></i>Settle Courier
                    </button>

                    <div class="border-start ps-2 ms-1 d-flex gap-2 align-items-center">
                        <select name="status" class="form-select form-select-sm" style="width: 125px; font-size: 10.5px; padding-right: 16px;">
                            <option value="">Order Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="returned">Returned</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select name="payment_status" class="form-select form-select-sm" style="width: 135px; font-size: 10.5px; padding-right: 16px;">
                            <option value="">Payment Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="partially_paid">Partially Paid</option>
                            <option value="refunded">Refunded</option>
                        </select>
                        <button type="submit" name="action" value="update" class="btn btn-sm btn-outline-secondary px-2" style="font-size:11px;" onclick="return confirm('Update status for selected orders?')">Apply</button>
                    </div>

                    <div class="border-start ps-2 ms-1">
                        <button type="submit" formaction="{{ route('admin.orders.bulk-delete') }}" class="btn btn-sm btn-danger fw-bold px-2" style="font-size:11px;" onclick="return confirmBulkDelete()">
                            <i class="fas fa-trash-alt me-1"></i>Delete Selected
                        </button>
                    </div>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="orderSelectedCount">0 items selected</span>
            </div>

            @php
                $sort = request('sort', 'created_at');
                $direction = request('direction', 'desc') === 'asc' ? 'desc' : 'asc';
                function sortUrl($field, $direction) {
                    return route('admin.orders.index', array_merge(request()->query(), ['sort' => $field, 'direction' => $direction]));
                }
            @endphp

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="text-center py-3">
                                <input type="checkbox" class="form-check-input" id="selectAllOrders">
                            </th>
                            <th class="py-3"><a href="{{ sortUrl('order_number', $direction) }}" class="text-dark text-decoration-none fw-bold">Order # <i class="fas fa-sort text-muted ms-1"></i></a></th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Payment</th>
                            <th class="py-3">Order Status</th>
                            <th class="py-3"><a href="{{ sortUrl('created_at', $direction) }}" class="text-dark text-decoration-none">Date <i class="fas fa-sort text-muted ms-1"></i></a></th>
                            <th class="text-end py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 14px;">
                        @forelse($orders as $order)
                            <tr>
                                <td class="text-center py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="form-check-input order-checkbox">
                                </td>
                                <td class="py-3">
                                    <strong class="text-dark">{{ $order->order_number }}</strong>
                                    <div class="mt-1">{!! $order->source_badge !!}</div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">{{ $order->name }}</div>
                                    <small class="text-muted d-block mt-1"><code>{{ $order->phone }}</code></small>
                                </td>
                                <td class="fw-bold text-dark py-3">৳{{ number_format($order->total) }}</td>
                                <td class="py-3">
                                    <span class="badge bg-light text-dark mb-1" style="font-size: 11px;">{{ strtoupper($order->payment_method) }}</span>
                                    <div>{!! $order->payment_status_badge !!}</div>
                                </td>
                                <td class="py-3">{!! $order->status_badge !!}</td>
                                <td class="py-3"><small class="text-muted" style="font-size: 13px;">{{ $order->created_at->format('d M Y') }}</small></td>
                                <td class="text-end py-3">
                                    <div class="d-inline-flex gap-1 flex-wrap justify-content-end">
                                        @if(!$order->consignment_id)
                                            <button type="button" class="btn btn-sm btn-info text-white py-1 px-2" style="font-size: 11px;" onclick="document.getElementById('push-form-{{ $order->id }}').submit();" title="Push directly to SteadFast">
                                                <i class="fas fa-paper-plane me-1"></i> Push
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-info py-1 px-2" style="font-size: 11px;" onclick="document.getElementById('track-form-{{ $order->id }}').submit();" title="Refresh real-time tracking">
                                                <i class="fas fa-sync-alt me-1"></i> Track
                                            </button>
                                        @endif

                                        @if($order->courier_settlement_status !== 'settled')
                                            <button type="button" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 11px;" onclick="document.getElementById('settle-form-{{ $order->id }}').submit();" title="Settle Courier (Confirm & Pay)">
                                                <i class="fas fa-check-circle me-1"></i> Settle
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 11px;" title="View details"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" title="Download invoice"><i class="fas fa-file-pdf"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 11px;" onclick="if(confirm('Delete order #{{ $order->order_number }} permanently?')) document.getElementById('delete-form-{{ $order->id }}').submit();" title="Delete Order">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No orders found matching criteria.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($orders as $order)
            @if(!$order->consignment_id)
                <form id="push-form-{{ $order->id }}" action="{{ route('admin.orders.push-courier') }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                </form>
            @else
                <form id="track-form-{{ $order->id }}" action="{{ route('admin.orders.track-parcel', $order) }}" method="POST" class="d-none">
                    @csrf
                </form>
            @endif

            @if($order->courier_settlement_status !== 'settled')
                <form id="settle-form-{{ $order->id }}" action="{{ route('admin.orders.settle-courier', $order) }}" method="POST" class="d-none">
                    @csrf
                </form>
            @endif

            <form id="delete-form-{{ $order->id }}" action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="d-flex justify-content-center p-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkDelete() {
    const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one order to delete.');
        return false;
    }
    return confirm('⚠️ Are you sure you want to permanently delete ' + checkedCount + ' selected order(s)? This cannot be undone!');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllOrders');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    const countText = document.getElementById('orderSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
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
