@extends('layouts.admin')

@section('title', 'Create Order')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-plus-circle text-success me-2"></i>Create New Order</h4>
                <p class="text-muted mb-0 small">Manually place a custom or telephone order for a customer</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="fas fa-arrow-left me-1"></i>Back to Orders
            </a>
        </div>
    </div>

    <form action="{{ route('admin.orders.store') }}" method="POST" id="createOrderForm">
        @csrf
        <div class="row g-4">
            <!-- Left: Customer & Shipping Details -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-user-tag text-primary me-2"></i>Customer & Shipping Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-bold">Customer Name *</label>
                                <input type="text" name="name" class="form-control rounded-3" placeholder="Full name..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-bold">Phone Number *</label>
                                <input type="text" name="phone" class="form-control rounded-3" placeholder="e.g. 017XXXXXXXX" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-sm fw-bold">Email Address (Optional)</label>
                                <input type="email" name="email" class="form-control rounded-3" placeholder="email@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-bold">District *</label>
                                <select name="district" class="form-select rounded-3" required>
                                    <option value="">Select District</option>
                                    @foreach($districts as $dist)
                                        <option value="{{ $dist['bn_name'] }}">{{ $dist['bn_name'] }} ({{ $dist['name'] }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-bold">Shipping Charge (৳)</label>
                                <input type="number" step="0.01" name="shipping_charge" id="shippingCharge" class="form-control rounded-3" value="100">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-sm fw-bold">Order Source (Sales Channel) *</label>
                                <select name="source" class="form-select rounded-3" required>
                                    <option value="direct_call">📞 Direct Call</option>
                                    <option value="messenger">💬 Messenger</option>
                                    <option value="instagram">📸 Instagram</option>
                                    <option value="whatsapp">💬 WhatsApp</option>
                                    <option value="website">🌐 Website</option>
                                    <option value="other">🏪 Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-sm fw-bold">Full Address *</label>
                                <textarea name="address" class="form-control rounded-3" rows="3" placeholder="House, Road, Area..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-credit-card text-success me-2"></i>Payment Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-bold">Payment Method *</label>
                                <select name="payment_method" class="form-select rounded-3" required>
                                    <option value="cod">Cash on Delivery (COD)</option>
                                    <option value="bkash">bKash</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="cash">Direct Cash</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-bold">Payment Status *</label>
                                <select name="payment_status" class="form-select rounded-3" required>
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-sm fw-bold">Admin Notes</label>
                                <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Internal notes or customer requests..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Products & Order Summary -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0"><i class="fas fa-boxes text-warning me-2"></i>Order Products</h5>
                            <button type="button" class="btn btn-sm btn-dark rounded-3" id="addItemBtn">
                                <i class="fas fa-plus me-1"></i>Add Product Item
                            </button>
                        </div>

                        <div id="itemsContainer" class="d-flex flex-column gap-3 mb-3">
                            <div class="p-3 bg-light rounded-3 border item-row">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-bold text-muted mb-1">Product *</label>
                                        <select name="items[0][product_id]" class="form-select form-select-sm rounded-3 product-select" required>
                                            <option value="">Select Product...</option>
                                            @foreach($products as $prod)
                                                <option value="{{ $prod->id }}" data-price="{{ $prod->current_price }}">{{ $prod->name }} (৳{{ number_format($prod->current_price, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-xs fw-bold text-muted mb-1">Unit Price (৳) *</label>
                                        <input type="number" step="0.01" name="items[0][unit_price]" class="form-control form-control-sm rounded-3 unit-price" placeholder="0.00" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label text-xs fw-bold text-muted mb-1">Qty *</label>
                                        <input type="number" name="items[0][quantity]" class="form-control form-control-sm rounded-3 item-qty" value="1" min="1" required>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <label class="form-label text-xs d-block mb-1">&nbsp;</label>
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row-btn"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-light rounded-4 border">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <strong class="font-monospace text-dark" id="displaySubtotal">৳0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Shipping Charge:</span>
                                <strong class="font-monospace text-dark" id="displayShipping">৳100.00</strong>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                <h5 class="fw-bold mb-0">Total Amount:</h5>
                                <h4 class="fw-extrabold text-success font-monospace mb-0" id="displayTotal">৳100.00</h4>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-success text-white fw-bold px-4 py-2 rounded-3">
                                <i class="fas fa-check-circle me-1"></i>Create Order Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        let itemIndex = 1;

        $('#addItemBtn').on('click', function() {
            let rowHtml = `
            <div class="p-3 bg-light rounded-3 border item-row">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-bold text-muted mb-1">Product *</label>
                        <select name="items[${itemIndex}][product_id]" class="form-select form-select-sm rounded-3 product-select" required>
                            <option value="">Select Product...</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}" data-price="{{ $prod->current_price }}">{{ $prod->name }} (৳{{ number_format($prod->current_price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-xs fw-bold text-muted mb-1">Unit Price (৳) *</label>
                        <input type="number" step="0.01" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm rounded-3 unit-price" placeholder="0.00" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-xs fw-bold text-muted mb-1">Qty *</label>
                        <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm rounded-3 item-qty" value="1" min="1" required>
                    </div>
                    <div class="col-md-1 text-end">
                        <label class="form-label text-xs d-block mb-1">&nbsp;</label>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row-btn"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
            </div>`;
            $('#itemsContainer').append(rowHtml);
            itemIndex++;
        });

        $(document).on('click', '.remove-row-btn', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                calculateTotals();
            }
        });

        $(document).on('change', '.product-select', function() {
            let price = $(this).find(':selected').data('price') || 0;
            $(this).closest('.item-row').find('.unit-price').val(price);
            calculateTotals();
        });

        $(document).on('input change', '.unit-price, .item-qty, #shippingCharge', function() {
            calculateTotals();
        });

        function calculateTotals() {
            let subtotal = 0;
            $('.item-row').each(function() {
                let price = parseFloat($(this).find('.unit-price').val()) || 0;
                let qty = parseInt($(this).find('.item-qty').val()) || 0;
                subtotal += price * qty;
            });
            let shipping = parseFloat($('#shippingCharge').val()) || 0;
            let total = subtotal + shipping;

            $('#displaySubtotal').text('৳' + subtotal.toFixed(2));
            $('#displayShipping').text('৳' + shipping.toFixed(2));
            $('#displayTotal').text('৳' + total.toFixed(2));
        }
    });
</script>
@endsection
@endsection
