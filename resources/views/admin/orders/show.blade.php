@extends('layouts.admin')

@section('title', 'Order Details: ' . $order->order_number)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Order: {{ $order->order_number }}</h4>
    <div class="d-flex gap-2">
        @if(!$order->consignment_id)
            <form action="{{ route('admin.orders.push-courier') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <button type="submit" class="btn btn-info text-white fw-bold"><i class="fas fa-paper-plane me-1"></i>Push to SteadFast</button>
            </form>
        @else
            <form action="{{ route('admin.orders.track-parcel', $order) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-info fw-bold"><i class="fas fa-sync-alt me-1"></i>Track Parcel</button>
            </form>
        @endif
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-admin-primary"><i class="fas fa-file-pdf me-1"></i>Download Invoice</a>
    </div>
</div>

<div class="row g-4">
    <!-- Items detail column -->
    <div class="col-lg-8">
        <div class="table-card mb-4">
            <div class="card-header">
                <h5>Ordered Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->name }}</strong>
                                    @if($item->variant_info)
                                        <small class="text-muted d-block">Option: {{ $item->variant_info }}</small>
                                    @endif
                                </td>
                                <td>৳{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="fw-bold">৳{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3 justify-content-end p-3">
                    <div class="col-md-5">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <strong>৳{{ number_format($order->subtotal, 2) }}</strong>
                        </div>
                        @if($order->discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Discount:</span>
                                <strong>-৳{{ number_format($order->discount, 2) }}</strong>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping charge:</span>
                            <strong>৳{{ number_format($order->shipping_charge, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold text-primary fs-5">
                            <span>Grand Total:</span>
                            <span>৳{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courier API Logs -->
        @if($order->courierLogs->isNotEmpty())
        <div class="table-card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-history text-primary me-2"></i>Courier API Logs</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:12px;">
                        <thead class="table-light">
                            <tr>
                                <th>Action</th>
                                <th>Status</th>
                                <th>Error / Info</th>
                                <th>By</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->courierLogs as $log)
                                <tr>
                                    <td><strong class="text-uppercase">{{ $log->action }}</strong></td>
                                    <td>
                                        @if($log->status_code == 200)
                                            <span class="badge bg-success">200 OK</span>
                                        @else
                                            <span class="badge bg-danger">{{ $log->status_code ?? 'Err' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->error_message)
                                            <span class="text-danger fw-semibold">{{ $log->error_message }}</span>
                                        @else
                                            <span class="text-muted">Payload sent successfully</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->createdBy->name ?? 'System' }}</td>
                                    <td><small class="text-muted">{{ $log->created_at->format('d M H:i A') }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Timeline trace history -->
        <div class="table-card">
            <div class="card-header">
                <h5>Status History Logs</h5>
            </div>
            <div class="card-body p-3">
                <ul class="list-unstyled mb-0">
                    @foreach($order->statusHistory as $hist)
                        <li class="pb-3 border-bottom mb-3 last-border-none">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-capitalize" style="font-size:14px;">{{ $hist->status }}</strong>
                                <small class="text-muted">{{ $hist->created_at->format('d M Y, h:i A') }}</small>
                            </div>
                            <p class="text-muted mb-0" style="font-size:13px;">{{ $hist->comment }}</p>
                            <small class="text-muted" style="font-size: 11px;">Changed by: {{ $hist->changedBy->name ?? 'System' }}</small>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Sidebar column -->
    <div class="col-lg-4">
        <!-- SteadFast Courier Card -->
        <div class="form-card mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-shipping-fast me-2 text-info"></i>Courier Parcel Details</h5>
            @if($order->consignment_id)
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Consignment ID:</span>
                        <strong class="text-primary">{{ $order->consignment_id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Tracking Code:</span>
                        <strong class="text-dark">{{ $order->tracking_code }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Courier Status:</span>
                        <div>{!! $order->courier_status_badge !!}</div>
                    </div>
                    @if($order->last_courier_sync_at)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Last Synced:</span>
                            <small class="text-muted">{{ $order->last_courier_sync_at->format('d M Y, h:i A') }}</small>
                        </div>
                    @endif
                </div>
                @if($order->tracking_url)
                    <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-outline-primary w-100 btn-sm fw-bold mb-2">
                        <i class="fas fa-external-link-alt me-1"></i>Open Public Tracking Page
                    </a>
                @endif
            @else
                <div class="alert alert-warning mb-3" style="font-size:13px;">
                    <i class="fas fa-exclamation-circle me-1"></i> This order has not been pushed to SteadFast Courier yet.
                </div>
                <form action="{{ route('admin.orders.push-courier') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <button type="submit" class="btn btn-info text-white w-100 fw-bold py-2 mb-2">
                        <i class="fas fa-paper-plane me-1"></i> Push to SteadFast API
                    </button>
                </form>
            @endif

            <hr>
            <div class="mb-3">
                <span class="text-muted d-block" style="font-size: 12px;">Courier Settlement</span>
                {!! $order->courier_settlement_badge !!}
                @if($order->courier_settled_at)
                    <small class="text-muted d-block mt-1">Settled on: {{ $order->courier_settled_at->format('d M Y, h:i A') }}</small>
                    <small class="text-muted d-block">Settled by: {{ $order->courierSettledBy->name ?? 'Admin' }}</small>
                @endif
            </div>

            @if($order->courier_settlement_status !== 'settled')
                <form action="{{ route('admin.orders.settle-courier', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 fw-bold py-2" onclick="return confirm('Settle courier for this order? Order will be set to Confirmed & Paid and pushed to SteadFast if needed.')">
                        <i class="fas fa-handshake me-1"></i> Settle Courier
                    </button>
                </form>
            @endif
        </div>

        <div class="form-card mb-4">
            <h5 class="fw-bold mb-3">Update Order Status</h5>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Order Status</label>
                    <select name="status" class="form-select text-capitalize">
                        @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'] as $st)
                            <option value="{{ $st }}" {{ $order->status == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select text-capitalize">
                        @foreach(['pending', 'paid', 'partially_paid', 'failed', 'refunded'] as $ps)
                            <option value="{{ $ps }}" {{ $order->payment_status == $ps ? 'selected' : '' }}>{{ str_replace('_', ' ', $ps) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Comment / Remark</label>
                    <textarea name="comment" rows="3" class="form-control" placeholder="Notes for status transition..."></textarea>
                </div>
                <button type="submit" class="btn w-100 fw-bold transition" style="background: linear-gradient(135deg, #d97d8c, #c25f6f); border: none; padding: 10px; border-radius: 6px; box-shadow: 0 4px 10px rgba(217, 125, 140, 0.25); color: #fff;">Update Order</button>
            </form>
        </div>

        <div class="form-card">
            <h5 class="fw-bold mb-3">Delivery Information</h5>
            <div class="mb-3 border-bottom pb-2">
                <span class="text-muted d-block" style="font-size: 12px;">Order Source Channel</span>
                {!! $order->source_badge !!}
            </div>
            <div class="mb-2">
                <span class="text-muted d-block" style="font-size: 12px;">Customer Details</span>
                <strong>{{ $order->name }}</strong><br>
                <small class="text-muted">Phone: {{ $order->phone }}</small>
            </div>
            <div class="mb-2">
                <span class="text-muted d-block" style="font-size: 12px;">Delivery Address</span>
                <strong>{{ $order->address }}</strong><br>
                <small class="text-muted">{{ $order->area }}, {{ $order->district }}, {{ $order->division }}</small>
            </div>
            <div>
                <span class="text-muted d-block" style="font-size: 12px;">Payment Details</span>
                <strong>Method: {{ strtoupper($order->payment_method) }}</strong><br>
                @if($order->payment_method === 'bkash')
                    <small class="text-muted d-block">Sender No: {{ $order->bkash_number }}</small>
                    <small class="text-muted d-block">TrxID: {{ $order->bkash_trx_id }}</small>
                @endif
                <small class="text-muted">Payment status: {{ strtoupper($order->payment_status) }}</small>
            </div>
        </div>
    </div>
</div>
@endsection
