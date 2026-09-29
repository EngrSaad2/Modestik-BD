@extends('layouts.frontend')

@section('title', 'Shopping Cart - Modestik')

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">হোম</a></li>
                <li class="breadcrumb-item active" aria-current="page">শপিং কার্ট</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <h4 class="fw-bold mb-4">আমার শপিং কার্ট</h4>

        <div class="row g-4" id="cartContentArea">
            @if($cart->items->isEmpty())
                <div class="col-12 text-center py-5">
                    <i class="fas fa-shopping-cart text-muted mb-3" style="font-size: 48px;"></i>
                    <h5 class="text-muted">আপনার কার্টটি বর্তমানে খালি রয়েছে।</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">এখনই শপিং করুন</a>
                </div>
            @else
                <!-- Items Column -->
                <div class="col-lg-8">
                    <div class="bg-white rounded-3 shadow-sm p-3">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>পণ্য</th>
                                        <th>মূল্য</th>
                                        <th>পরিমাণ</th>
                                        <th>মোট</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->items as $item)
                                    <tr id="cart-row-{{ $item->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->primary_image_url }}" alt="img" class="rounded" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px;">
                                                <div>
                                                    <a href="{{ route('products.show', $item->product->slug) }}" class="fw-bold text-dark d-block text-decoration-none">{{ $item->product->name }}</a>
                                                    @if($item->variant)
                                                        <small class="text-muted">অপশন: {{ $item->variant->display_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>৳{{ number_format($item->price, 2) }}</td>
                                        <td>
                                            <div class="input-group" style="width: 100px;">
                                                <button class="btn btn-outline-secondary btn-sm" onclick="updateQty({{ $item->id }}, -1)"><i class="fas fa-minus" style="font-size:10px;"></i></button>
                                                <input type="text" class="form-control text-center form-control-sm item-qty-input" data-id="{{ $item->id }}" value="{{ $item->quantity }}" readonly>
                                                <button class="btn btn-outline-secondary btn-sm" onclick="updateQty({{ $item->id }}, 1)"><i class="fas fa-plus" style="font-size:10px;"></i></button>
                                            </div>
                                        </td>
                                        <td class="fw-bold" id="item-total-{{ $item->id }}">৳{{ number_format($item->total, 2) }}</td>
                                        <td>
                                            <button class="btn btn-sm text-danger" onclick="removeItem({{ $item->id }})"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Summary Column -->
                <div class="col-lg-4">
                    <div class="bg-white rounded-3 shadow-sm p-4" id="checkoutSummaryCard">
                        <h5 class="fw-bold mb-3">অর্ডার সারাংশ</h5>
                        
                        @php
                            $freeShippingThreshold = 1000;
                            $subtotal = $cart->subtotal;
                            $percentage = min(100, ($subtotal / $freeShippingThreshold) * 100);
                            $remaining = $freeShippingThreshold - $subtotal;
                        @endphp

                        <!-- Free Shipping Progress Bar -->
                        <div class="minimum-order-progress-wrapper mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                                @if($remaining > 0)
                                    <span class="text-muted">ফ্রি ডেলিভারি পেতে আর মাত্র <strong>৳{{ number_format($remaining) }}</strong> টাকার অর্ডার করুন</span>
                                @else
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>অভিনন্দন! আপনি ফ্রি ডেলিভারি পাচ্ছেন!</span>
                                @endif
                                <span class="fw-bold">{{ number_format($percentage, 0) }}%</span>
                            </div>
                            <div class="progress" style="height: 10px; background-color: #f1f5f9; border-radius: 5px; overflow: hidden;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                     style="width: {{ $percentage }}%; background: var(--primary-gradient); transition: width 0.4s ease;" 
                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">সাবটোটাল</span>
                            <strong id="cartSubtotal">৳{{ number_format($cart->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ডিসকাউন্ট</span>
                            <strong id="cartDiscount" class="text-danger">-৳{{ number_format($cart->discount, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">সর্বমোট</span>
                            <strong id="cartTotal" class="text-primary fs-5">৳{{ number_format($cart->subtotal - $cart->discount, 2) }}</strong>
                        </div>

                        <!-- Coupon Input -->
                        <div class="coupon-section mb-4">
                            <label class="form-label fw-bold" style="font-size: 13px;">কুপন কোড আছে?</label>
                            @if($cart->coupon)
                                <div class="alert alert-success d-flex justify-content-between align-items-center py-2 px-3">
                                    <span>কোড <strong>{{ $cart->coupon->code }}</strong> সক্রিয় আছে</span>
                                    <button class="btn btn-sm btn-outline-danger py-0" onclick="removeCoupon()">বাদ দিন</button>
                                </div>
                            @else
                                <div class="input-group">
                                    <input type="text" id="couponCode" class="form-control" placeholder="কুপন কোড লিখুন">
                                    <button class="btn btn-dark" onclick="applyCoupon()">প্রয়োগ করুন</button>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 py-2 fw-bold">চেকআউট করতে এগিয়ে যান</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    function updateQty(itemId, change) {
        let input = $(`.item-qty-input[data-id="${itemId}"]`);
        let currentQty = parseInt(input.val());
        let newQty = currentQty + change;
        if (newQty < 1 || newQty > 10) return;

        input.val(newQty);

        $.ajax({
            url: '{{ route("cart.update") }}',
            method: 'PATCH',
            data: { item_id: itemId, quantity: newQty },
            success: function(res) {
                // Update total label for row
                let rowPrice = parseFloat($(`#cart-row-${itemId}`).find('td:nth-child(2)').text().replace('৳', '').replace(/,/g, ''));
                $(`#item-total-${itemId}`).text('৳' + (rowPrice * newQty).toFixed(2));
                
                // Update layout totals
                refreshTotals();
                updateCartCount();
            }
        });
    }

    function removeItem(itemId) {
        Swal.fire({
            title: 'পণ্যটি বাদ দিতে চান?',
            text: 'আপনি কি এই পণ্যটি কার্ট থেকে বাদ দিতে চান?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d97d8c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'হ্যাঁ, বাদ দিন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("cart/remove") }}/' + itemId,
                    method: 'DELETE',
                    success: function() {
                        location.reload();
                    }
                });
            }
        });
    }

    function applyCoupon() {
        let code = $('#couponCode').val();
        if (!code) return;

        $.post('{{ route("cart.apply-coupon") }}', { code: code }, function(res) {
            Swal.fire({ icon: 'success', title: 'সফল!', text: 'কুপন কোড প্রয়োগ করা হয়েছে।' });
            location.reload();
        }).fail(function(xhr) {
            Swal.fire({ icon: 'error', title: 'দুঃখিত', text: xhr.responseJSON?.message || 'ভুল বা মেয়াদোত্তীর্ণ কুপন কোড' });
        });
    }

    function removeCoupon() {
        $.ajax({
            url: '{{ route("cart.remove-coupon") }}',
            method: 'DELETE',
            success: function(res) {
                Swal.fire({ icon: 'info', title: 'বাতিল', text: 'কুপন কোডটি বাদ দেওয়া হয়েছে।' });
                location.reload();
            }
        });
    }

    function refreshTotals() {
        $.get('{{ route("cart.index") }}', function(html) {
            let tempDom = $(html);
            $('#checkoutSummaryCard').html(tempDom.find('#checkoutSummaryCard').html());
        });
    }
</script>
@endsection
