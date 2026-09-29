@extends('layouts.admin')

@section('title', 'Incomplete Orders')

@section('styles')
<style>
    .recovery-drawer {
        width: 500px !important;
        max-width: 90vw;
    }
    .action-center-card {
        background-color: #ffffff;
        color: #1e293b;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e2e8f0;
    }
    .action-center-card select, 
    .action-center-card input {
        background-color: #ffffff;
        color: #1e293b;
        border: 1px solid #cbd5e1;
    }
    .action-center-card select:focus {
        background-color: #ffffff;
        color: #1e293b;
        border-color: #94a3b8;
    }
    .badge-stage {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 4px 10px;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-0">

    <!-- Header & Potential Revenue Lost Banner -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-dark"><i class="fas fa-shopping-cart text-danger me-2"></i>Incomplete Orders</h3>
            <p class="text-muted mb-0" style="font-size: 13px;">Track & recover abandoned checkouts efficiently.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.abandoned-carts.analytics') }}" class="btn btn-outline-primary btn-sm fw-bold rounded-3">
                <i class="fas fa-chart-pie me-1"></i>Recovery Analytics
            </a>
            <div class="bg-white border rounded-4 p-3 shadow-sm text-end" style="min-width: 220px;">
                <span class="text-xs fw-bold text-uppercase text-muted d-block" style="letter-spacing: 1px;">Potential Revenue Lost</span>
                <h3 class="fw-extrabold text-danger mb-0 mt-1">৳{{ number_format($metrics['potential_revenue_lost'], 0) }}</h3>
            </div>
        </div>
    </div>

    <!-- Order Status Tabs -->
    <div class="bg-white rounded-3 p-3 shadow-sm mb-4">
        <div class="d-flex gap-2 flex-wrap" style="font-size: 13px;">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light">All Orders</a>
            @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'] as $st)
                <a href="{{ route('admin.orders.index', ['status' => $st]) }}" class="btn btn-sm btn-light text-capitalize">{{ $st }}</a>
            @endforeach
            <a href="{{ route('admin.orders.incomplete') }}" class="btn btn-sm btn-danger fw-bold border-2 d-inline-flex align-items-center gap-1">
                <i class="fas fa-shopping-cart me-1"></i>Incomplete Orders
                <span class="badge bg-white text-danger rounded-pill">{{ $metrics['total_incomplete_carts'] }}</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.orders.incomplete') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, phone, email, product..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="stage" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Filter Stage</option>
                        <option value="cart_created" {{ request('stage') == 'cart_created' ? 'selected' : '' }}>Cart Created</option>
                        <option value="cart_viewed" {{ request('stage') == 'cart_viewed' ? 'selected' : '' }}>Cart Viewed</option>
                        <option value="checkout_started" {{ request('stage') == 'checkout_started' ? 'selected' : '' }}>Checkout Started</option>
                        <option value="shipping_info" {{ request('stage') == 'shipping_info' ? 'selected' : '' }}>Shipping Info</option>
                        <option value="payment_started" {{ request('stage') == 'payment_started' ? 'selected' : '' }}>Payment Started</option>
                        <option value="abandoned" {{ request('stage') == 'abandoned' ? 'selected' : '' }}>Abandoned</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="crm_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Filter CRM Status</option>
                        <option value="new" {{ request('crm_status') == 'new' ? 'selected' : '' }}>NEW</option>
                        <option value="contacted" {{ request('crm_status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="in_progress" {{ request('crm_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="recovered" {{ request('crm_status') == 'recovered' ? 'selected' : '' }}>Recovered</option>
                        <option value="lost" {{ request('crm_status') == 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="assigned_staff_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Staff</option>
                        <option value="unassigned" {{ request('assigned_staff_id') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" {{ request('assigned_staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3"><i class="fas fa-filter me-1"></i>Filter</button>
                    <a href="{{ route('admin.orders.incomplete') }}" class="btn btn-sm btn-light border rounded-3"><i class="fas fa-redo"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Form & Incomplete Orders Table -->
    <form action="{{ route('admin.abandoned-carts.bulk-action') }}" method="POST" id="bulkForm">
        @csrf
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <select name="action" class="form-select form-select-sm" id="bulkActionSelect" style="width: 180px;">
                        <option value="">Bulk Actions</option>
                        <option value="mark_recovered">Mark as Recovered</option>
                        <option value="mark_lost">Mark as Lost</option>
                        <option value="assign_staff">Assign Staff</option>
                        <option value="delete">Delete Records</option>
                    </select>
                    <div id="staffSelectWrapper" style="display: none;">
                        <select name="staff_id" class="form-select form-select-sm" style="width: 160px;">
                            <option value="unassigned">Unassigned</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-sm btn-outline-secondary rounded-3">Apply</button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="bg-light">
                            <tr class="text-muted text-xs text-uppercase" style="letter-spacing: 0.8px;">
                                <th class="ps-4" style="width: 40px;"><input type="checkbox" id="selectAllCarts"></th>
                                <th>Customer Profile</th>
                                <th>Cart Value</th>
                                <th>Drop-off Stage</th>
                                <th>CRM Status</th>
                                <th>Last Activity</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($carts as $cart)
                                <tr>
                                    <td class="ps-4">
                                        <input type="checkbox" name="ids[]" value="{{ $cart->id }}" class="cart-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                                {{ strtoupper(substr($cart->customer_display_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block mb-0">{{ $cart->customer_display_name }}</span>
                                                <small class="text-muted"><i class="fas fa-phone me-1 text-xs"></i>{{ $cart->customer_display_phone }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-extrabold text-dark d-block">৳{{ number_format($cart->total_value, 0) }}</span>
                                        <small class="text-muted text-uppercase" style="font-size: 11px;">{{ $cart->total_items }} ITEMS</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-stage {{ $cart->stage_badge_class }}">
                                            {{ $cart->stage_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $cart->crm_badge_class }} rounded-pill px-3 py-1 text-uppercase" style="font-size: 10px;">
                                            {{ $cart->crm_status }}
                                        </span>
                                    </td>
                                    <td class="font-monospace text-muted" style="font-size: 12px;">
                                        {{ $cart->last_activity_at ? $cart->last_activity_at->format('n/j/Y | g:i A') : $cart->updated_at->format('n/j/Y | g:i A') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-dark btn-sm rounded-3 px-3 open-recovery-drawer" data-cart-id="{{ $cart->id }}">
                                            Manage
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                                        No incomplete orders or abandoned carts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($carts->hasPages())
                <div class="card-footer bg-white border-0 p-3">
                    {{ $carts->links() }}
                </div>
            @endif
        </div>
    </form>
</div>

<!-- Right Side Recovery Console Drawer (Offcanvas) -->
<div class="offcanvas offcanvas-end recovery-drawer shadow-lg border-0" tabindex="-1" id="recoveryConsoleDrawer" aria-labelledby="recoveryConsoleLabel">
    <div class="offcanvas-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="offcanvas-title fw-extrabold text-dark mb-0" id="recoveryConsoleLabel">Recovery Console</h4>
                <span class="badge badge-stage bg-warning bg-opacity-20 text-warning border border-warning" id="drawerStageBadge">SHIPPING INFO</span>
            </div>
            <small class="text-muted font-monospace" id="drawerCartId">ID: Loading...</small>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4" style="background: #f8fafc;">
        <!-- Lead Details Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <small class="text-uppercase text-muted fw-bold text-xs d-block mb-3" style="letter-spacing: 0.8px;"><i class="fas fa-user me-1 text-danger"></i>Lead Details</small>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-dark text-white rounded-4 d-flex align-items-center justify-content-center fw-extrabold fs-4" style="width: 52px; height: 52px;" id="drawerAvatar">
                            M
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0" id="drawerCustomerName">Md fahim</h5>
                            <small class="text-muted d-block" id="drawerPhone">+880 1677111480</small>
                            <small class="text-muted d-block" id="drawerAddress">97 D N Road Golachipa Narayanganj</small>
                        </div>
                    </div>
                    <a href="#" id="drawerWhatsappBtn" target="_blank" class="btn btn-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fab fa-whatsapp fa-xl"></i>
                    </a>
                </div>

                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-xs text-muted fw-bold d-block text-uppercase">Traffic Origin</span>
                        <span class="badge bg-light text-dark font-monospace border" id="drawerIpAddress">103.125.31.150</span>
                    </div>
                    <button type="button" class="btn btn-xs btn-outline-danger rounded-pill px-2" onclick="Swal.fire('IP Blocked', 'IP Address has been flagged.', 'info')">
                        <i class="fas fa-ban me-1"></i>Block IP
                    </button>
                </div>
            </div>
        </div>

        <!-- Abandoned Items Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <small class="text-uppercase text-muted fw-bold text-xs d-block mb-3" style="letter-spacing: 0.8px;"><i class="fas fa-shopping-basket me-1 text-danger"></i>Abandoned Items</small>
                <div id="drawerItemsContainer" class="d-flex flex-column gap-2">
                    <!-- Items rendered dynamically -->
                </div>
                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-muted text-uppercase text-xs">Total Value</span>
                    <h3 class="fw-extrabold text-dark mb-0" id="drawerTotalValue">৳650</h3>
                </div>
            </div>
        </div>

        <!-- Action Center Card (White Theme) -->
        <div class="action-center-card shadow-sm mb-4">
            <small class="text-uppercase text-danger fw-bold text-xs d-block mb-3" style="letter-spacing: 1px;"><i class="fas fa-bolt me-1"></i>Action Center</small>

            <div class="row g-2 mb-3">
                <div class="col-12">
                    <label class="form-label text-xs text-muted fw-bold mb-1">UPDATE CRM STATUS</label>
                    <select class="form-select form-select-sm rounded-3 text-dark bg-white border" id="drawerCrmStatusSelect">
                        <option value="new">New Lead</option>
                        <option value="contacted">Contacted</option>
                        <option value="in_progress">In Progress</option>
                        <option value="recovered">Recovered</option>
                        <option value="lost">Lost</option>
                    </select>
                </div>
            </div>

            <!-- Action Grid Buttons -->
            <div class="d-flex flex-column gap-2 mt-3">
                <button type="button" class="btn btn-success fw-bold py-2 w-100 rounded-3 shadow-sm" id="drawerCreateOrderBtn">
                    <i class="fas fa-shopping-bag me-2"></i>Create Order (One-Click Conversion)
                </button>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="#" id="drawerCallBtn" class="btn btn-outline-dark btn-sm w-100 rounded-3 py-2 fw-bold">
                            <i class="fas fa-phone me-1 text-success"></i>Call Customer
                        </a>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-dark btn-sm w-100 rounded-3 py-2 fw-bold" id="drawerCopyLinkBtn">
                            <i class="fas fa-link me-1 text-warning"></i>Copy Cart Link
                        </button>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-dark btn-sm w-100 rounded-3 py-2 fw-bold" id="drawerSmsBtn">
                            <i class="fas fa-comment-dots me-1 text-info"></i>Send SMS
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-dark btn-sm w-100 rounded-3 py-2 fw-bold" id="drawerEmailBtn">
                            <i class="fas fa-envelope me-1 text-primary"></i>Send Email
                        </button>
                    </div>
                </div>
            </div>

            <!-- Add Note -->
            <div class="mt-4 pt-3 border-top border-light">
                <label class="form-label text-xs text-muted fw-bold mb-1">ADD NOTE / LOG</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control text-dark bg-white border" id="drawerNoteInput" placeholder="Add staff note...">
                    <button class="btn btn-danger fw-bold px-3" type="button" id="drawerSaveNoteBtn">Save</button>
                </div>
                <div class="mt-3 text-xs text-muted" id="drawerLogsContainer" style="max-height: 120px; overflow-y: auto;">
                    <!-- Logs list -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        let currentCartId = null;
        let recoveryDrawer = new bootstrap.Offcanvas(document.getElementById('recoveryConsoleDrawer'));

        // Bulk select checkboxes
        $('#selectAllCarts').on('change', function() {
            $('.cart-checkbox').prop('checked', $(this).is(':checked'));
        });

        $('#bulkActionSelect').on('change', function() {
            if ($(this).val() === 'assign_staff') {
                $('#staffSelectWrapper').show();
            } else {
                $('#staffSelectWrapper').hide();
            }
        });

        // Open Recovery Drawer and load JSON
        $('.open-recovery-drawer').on('click', function() {
            currentCartId = $(this).data('cart-id');
            $('#drawerCartId').text('ID: Loading...');
            
            $.get(`/admin/abandoned-carts/${currentCartId}/json`, function(data) {
                $('#drawerCartId').text(`ID: ${data.recovery_token.substring(0, 8).toUpperCase()}`);
                $('#drawerStageBadge').text(data.stage_label).attr('class', `badge badge-stage ${data.stage_badge}`);
                $('#drawerCustomerName').text(data.customer_name);
                $('#drawerPhone').text(data.phone);
                $('#drawerAddress').text(data.address + ' (' + data.district + ')');
                $('#drawerAvatar').text(data.customer_name.charAt(0).toUpperCase());
                $('#drawerIpAddress').text(data.ip_address);
                $('#drawerTotalValue').text(`৳${parseFloat(data.total_value).toLocaleString()}`);
                
                $('#drawerWhatsappBtn').attr('href', `https://wa.me/${data.phone.replace(/[^0-9]/g, '')}`);
                $('#drawerCallBtn').attr('href', `tel:${data.phone}`);

                // Render Abandoned Items
                let itemsHtml = '';
                data.items.forEach(function(item) {
                    itemsHtml += `
                    <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-3 border mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${item.image}" width="42" height="42" class="rounded-3 object-fit-cover border">
                            <div>
                                <span class="fw-bold text-dark d-block text-sm mb-0">${item.name}</span>
                                <small class="text-muted" style="font-size: 11px;">Qty: ${item.quantity} | ${item.variant}</small>
                            </div>
                        </div>
                        <span class="fw-extrabold text-dark">৳${parseFloat(item.total).toLocaleString()}</span>
                    </div>`;
                });
                $('#drawerItemsContainer').html(itemsHtml);

                // Set Status dropdowns
                $('#drawerCrmStatusSelect').val(data.crm_status);
                $('#drawerAssignStaffSelect').val(data.assigned_staff_id || 'unassigned');

                // Render Logs
                let logsHtml = '';
                data.logs.forEach(function(log) {
                    logsHtml += `<div class="border-bottom border-light pb-1 mb-1 text-dark">
                        <strong class="text-dark">${log.user_name}:</strong> ${log.description}
                        <small class="d-block text-muted" style="font-size:10px;">${log.created_at}</small>
                    </div>`;
                });
                $('#drawerLogsContainer').html(logsHtml || '<span class="text-muted">No notes yet.</span>');

                // Copy recovery link action
                $('#drawerCopyLinkBtn').off('click').on('click', function() {
                    navigator.clipboard.writeText(data.recovery_url);
                    Swal.fire({ icon: 'success', title: 'Cart restoration link copied to clipboard!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
                });

                recoveryDrawer.show();
            });
        });

        // Update CRM status or Assign staff on change
        $('#drawerCrmStatusSelect, #drawerAssignStaffSelect').on('change', function() {
            if (!currentCartId) return;
            $.post(`/admin/abandoned-carts/${currentCartId}/status`, {
                _token: '{{ csrf_token() }}',
                crm_status: $('#drawerCrmStatusSelect').val(),
                assigned_staff_id: $('#drawerAssignStaffSelect').val()
            }, function() {
                Swal.fire({ icon: 'success', title: 'Lead status updated', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
            });
        });

        // Save Note
        $('#drawerSaveNoteBtn').on('click', function() {
            const note = $('#drawerNoteInput').val();
            if (!note || !currentCartId) return;

            $.post(`/admin/abandoned-carts/${currentCartId}/status`, {
                _token: '{{ csrf_token() }}',
                note: note
            }, function() {
                $('#drawerNoteInput').val('');
                Swal.fire({ icon: 'success', title: 'Note added', toast: true, position: 'top-end', timer: 1500, showConfirmButton: false });
                $('.open-recovery-drawer[data-cart-id="' + currentCartId + '"]').click();
            });
        });

        // Create Order Conversion
        $('#drawerCreateOrderBtn').on('click', function() {
            if (!currentCartId) return;
            Swal.fire({
                title: 'Convert to Order?',
                text: 'This will automatically create a completed order from this abandoned cart.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Yes, Create Order!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(`/admin/abandoned-carts/${currentCartId}/create-order`, {
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        if (res.success) {
                            Swal.fire('Success', res.message, 'success').then(() => {
                                window.location.reload();
                            });
                        }
                    }).fail(function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Order creation failed.', 'error');
                    });
                }
            });
        });

        // SMS action
        $('#drawerSmsBtn').on('click', function() {
            if (!currentCartId) return;
            $.post(`/admin/abandoned-carts/${currentCartId}/send-sms`, { _token: '{{ csrf_token() }}' }, function() {
                Swal.fire({ icon: 'success', title: 'SMS Action Logged!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
            });
        });

        // Email action
        $('#drawerEmailBtn').on('click', function() {
            if (!currentCartId) return;
            $.post(`/admin/abandoned-carts/${currentCartId}/send-email`, { _token: '{{ csrf_token() }}' }, function() {
                Swal.fire({ icon: 'success', title: 'Email Action Logged!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
            });
        });
    });
</script>
@endsection
