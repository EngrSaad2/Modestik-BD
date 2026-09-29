@extends('layouts.admin')

@section('title', 'Supplier Purchases')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-shopping-cart text-secondary me-2"></i>Supplier Purchases</h4>
                <p class="text-muted mb-0 small">Tracking purchases from suppliers. (Recording purchases automatically increases inventory stock)</p>
            </div>
            <button type="button" class="btn btn-primary rounded-3 px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
                <i class="fas fa-plus me-1"></i>Add New Purchase
            </button>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted text-xs text-uppercase">
                            <th class="ps-4">Invoice #</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Payment Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $p)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">#{{ $p->invoice_number }}</td>
                                <td>{{ $p->purchase_date->format('d M, Y') }}</td>
                                <td><span class="fw-bold text-dark">{{ $p->supplier?->name ?? 'General Purchase' }}</span></td>
                                <td class="fw-bold text-dark">৳{{ number_format($p->total_amount, 2) }}</td>
                                <td class="fw-bold text-success">৳{{ number_format($p->paid_amount, 2) }}</td>
                                <td class="fw-bold text-danger">৳{{ number_format($p->due_amount, 2) }}</td>
                                <td>
                                    @if($p->payment_status === 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Paid</span>
                                    @elseif($p->payment_status === 'partial')
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Partial</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Unpaid</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-xs btn-outline-info rounded-pill px-2" data-bs-toggle="collapse" data-bs-target="#items-{{ $p->id }}"><i class="fas fa-eye me-1"></i>Items</button>
                                </td>
                            </tr>
                            <tr class="collapse bg-light" id="items-{{ $p->id }}">
                                <td colspan="8" class="p-3">
                                    <div class="table-responsive bg-white rounded-3 p-2 shadow-sm">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr class="text-muted text-xs">
                                                    <th>Product</th>
                                                    <th>Variant</th>
                                                    <th>Unit Cost</th>
                                                    <th>Quantity</th>
                                                    <th class="text-end">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($p->items as $item)
                                                    <tr>
                                                        <td class="fw-bold">{{ $item->product?->name ?? 'Product' }}</td>
                                                        <td>{{ $item->variant?->display_name ?? 'Standard' }}</td>
                                                        <td>৳{{ number_format($item->unit_cost, 2) }}</td>
                                                        <td><span class="badge bg-primary rounded-pill">{{ $item->quantity }} pcs</span></td>
                                                        <td class="text-end fw-bold">৳{{ number_format($item->subtotal, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No purchase records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($purchases->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Purchase -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold"><i class="fas fa-shopping-cart text-primary me-2"></i>Record New Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.accounting.purchases.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-bold">Supplier</label>
                            <select name="supplier_id" class="form-select rounded-3">
                                <option value="">General Purchase (No Supplier)</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->company_name ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-sm fw-bold">Purchase Date *</label>
                            <input type="date" name="purchase_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-2 text-primary border-bottom pb-2">Purchased Products:</h6>
                    <div id="purchaseItemsContainer">
                        <div class="row g-2 mb-2 item-row">
                            <div class="col-md-5">
                                <select name="items[0][product_id]" class="form-select rounded-3 product-select" required>
                                    <option value="">Select Product...</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}" data-cost="{{ $prod->cost ?? 0 }}">{{ $prod->name }} (Stock: {{ $prod->quantity }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.01" name="items[0][unit_cost]" class="form-control rounded-3 unit-cost" placeholder="Unit Cost (৳)" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="items[0][quantity]" class="form-control rounded-3 item-qty" placeholder="Quantity" value="1" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-3 pt-3 border-top">
                        <div class="col-md-4">
                            <label class="form-label text-sm fw-bold">Paid Amount (৳) *</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-sm fw-bold">Payment Method *</label>
                            <select name="payment_method" class="form-select rounded-3" required>
                                <option value="cash">Cash</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="bank">Bank Account</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-sm fw-bold">Money Account</label>
                            <select name="account_id" class="form-select rounded-3">
                                <option value="">Default Account</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Save Purchase (Update Stock)</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
