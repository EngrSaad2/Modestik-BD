@extends('layouts.frontend')

@section('title', 'Order Confirmed - Modestik')

@section('styles')
<style>
    .confirmation-section {
        background-color: #ffffff;
        padding: 40px 0;
    }
    .confirmation-title {
        font-size: 32px;
        font-weight: 800;
        color: #2d2325;
        line-height: 1.2;
        margin-bottom: 12px;
    }
    .confirmation-subtitle {
        font-size: 14px;
        color: #7b686a;
        line-height: 1.5;
        margin-bottom: 25px;
    }
    .billing-address-title {
        font-size: 18px;
        font-weight: 700;
        color: #2d2325;
        margin-bottom: 15px;
    }
    .address-table {
        width: 100%;
        margin-bottom: 25px;
    }
    .address-table td {
        padding: 5px 0;
        vertical-align: top;
        font-size: 13px;
    }
    .address-table .label-col {
        font-weight: 700;
        color: #7b686a;
        width: 80px;
    }
    .address-table .val-col {
        color: #2d2325;
        font-weight: 500;
    }
    .track-btn {
        background-color: #d97d8c;
        color: #ffffff !important;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 30px;
        border-radius: 50px;
        border: none;
        display: inline-block;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(217, 125, 140, 0.2);
        transition: all 0.3s ease;
        text-align: center;
    }
    .track-btn:hover {
        background-color: #c25f6f;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(217, 125, 140, 0.3);
    }
    
    /* Receipt Styles */
    .receipt-wrapper {
        position: relative;
        width: 100%;
        max-width: 440px;
        margin: 0 auto;
    }
    .receipt-slot {
        height: 10px;
        background: #e2e8f0;
        border-radius: 6px;
        margin: 0 auto -5px auto;
        width: 90%;
        position: relative;
        z-index: 5;
    }
    .receipt-box {
        position: relative;
        background: #f8fafc;
        border-radius: 16px 16px 0 0;
        padding: 25px 20px;
        border: 1px solid #e2e8f0;
        border-bottom: none;
        box-shadow: var(--shadow-md);
    }
    .receipt-box::after {
        content: "";
        position: absolute;
        bottom: -16px;
        left: 0;
        right: 0;
        height: 16px;
        background-color: transparent;
        background-image: 
            linear-gradient(-45deg, #f8fafc 8px, transparent 0),
            linear-gradient(45deg, #f8fafc 8px, transparent 0);
        background-position: left bottom;
        background-repeat: repeat-x;
        background-size: 16px 16px;
        z-index: 10;
    }
    .receipt-title {
        font-size: 20px;
        font-weight: 800;
        color: #2d2325;
        margin-bottom: 20px;
    }
    .receipt-meta {
        display: flex;
        justify-content: space-between;
        border-bottom: 1.5px dashed #cbd5e1;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    .meta-item {
        flex: 1;
        padding: 0 8px;
        border-right: 1px solid #e2e8f0;
        text-align: center;
    }
    .meta-item:last-child {
        border-right: none;
        padding-right: 0;
    }
    .meta-item:first-child {
        padding-left: 0;
        text-align: left;
    }
    .meta-item:last-child {
        text-align: right;
    }
    .meta-label {
        font-size: 10px;
        color: #7b686a;
        font-weight: 500;
        display: block;
        margin-bottom: 2px;
    }
    .meta-value {
        font-size: 11px;
        font-weight: 700;
        color: #2d2325;
        white-space: nowrap;
    }
    .receipt-products {
        margin-bottom: 20px;
        max-height: 180px;
        overflow-y: auto;
    }
    .receipt-product-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }
    .product-img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 12px;
        border: 1px solid #e2e8f0;
    }
    .product-details {
        flex-grow: 1;
    }
    .product-title {
        font-size: 12px;
        font-weight: 700;
        color: #2d2325;
        margin-bottom: 1px;
        line-height: 1.3;
    }
    .product-meta {
        font-size: 10px;
        color: #7b686a;
        margin: 0;
    }
    .product-price {
        font-size: 13px;
        font-weight: 700;
        color: #2d2325;
        margin-left: 12px;
        white-space: nowrap;
    }
    .receipt-summary {
        border-top: 1.5px dashed #cbd5e1;
        padding-top: 15px;
        margin-bottom: 15px;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 8px;
        color: #7b686a;
        font-weight: 500;
    }
    .summary-row.total-row {
        border-top: 1px solid #e2e8f0;
        padding-top: 12px;
        margin-top: 12px;
        font-size: 14px;
        color: #2d2325;
        font-weight: 800;
    }
    .total-value {
        color: #2d2325;
        font-size: 18px;
        font-weight: 800;
    }
</style>
@endsection

@section('content')
<section class="confirmation-section">
    <div class="container" style="max-width: 1000px;">
        <div class="row align-items-start g-4">
            <!-- Left Info Panel -->
            <div class="col-lg-6 col-md-12">
                <h1 class="confirmation-title">Thank you for your purchase!</h1>
                <p class="confirmation-subtitle">
                    <span class="d-block text-muted" style="font-size: 12px; color: #7b686a;">
                        <i class="fas fa-info-circle me-1"></i> ঢাকা সিটির ভিতরে: ২ থেকে ৩ দিন | ঢাকা সিটির বাইরে: ২ থেকে ৫ দিন।
                    </span>
                </p>

                <h3 class="billing-address-title">Billing address</h3>
                <table class="address-table">
                    <tr>
                        <td class="label-col">Name</td>
                        <td class="val-col">{{ $order->name }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Address</td>
                        <td class="val-col">
                            {{ $order->address }}<br>
                            {{ $order->area ? $order->area . ', ' : '' }}{{ $order->district }}, {{ $order->division }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Phone</td>
                        <td class="val-col">{{ $order->phone }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Email</td>
                        <td class="val-col">{{ $order->email ?: auth()->user()?->email ?: 'N/A' }}</td>
                    </tr>
                </table>

                <a href="{{ route('track-order') }}" class="track-btn">Track Your Order</a>
            </div>

            <!-- Right Receipt Panel -->
            <div class="col-lg-6 col-md-12">
                <div class="receipt-wrapper">
                    <!-- Top Slot Fold Effect -->
                    <div class="receipt-slot"></div>
                    <!-- Receipt Box -->
                    <div class="receipt-box">
                        <h2 class="receipt-title">Order Summary</h2>

                        <!-- Receipt Meta Row -->
                        <div class="receipt-meta">
                            <div class="meta-item">
                                <span class="meta-label">Date</span>
                                <span class="meta-value">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="meta-item" style="flex: 1.2;">
                                <span class="meta-label">Order Number</span>
                                <span class="meta-value" style="font-size: 10px;">{{ $order->order_number }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Payment Method</span>
                                <span class="meta-value">
                                    @if($order->payment_method === 'bkash')
                                        bKash
                                    @elseif($order->payment_method === 'cod')
                                        COD
                                    @else
                                        {{ strtoupper($order->payment_method) }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Product List -->
                        <div class="receipt-products">
                            @foreach($order->items as $item)
                                <div class="receipt-product-item">
                                    <img src="{{ $item->product ? $item->product->primary_image_url : asset('images/placeholder.png') }}" alt="{{ $item->name }}" class="product-img">
                                    <div class="product-details">
                                        <h4 class="product-title">{{ $item->name }}</h4>
                                        <p class="product-meta">
                                            @if($item->variant_info)
                                                Pack: {{ $item->variant_info }} | 
                                            @endif
                                            Qty: {{ $item->quantity }}
                                        </p>
                                    </div>
                                    <span class="product-price">৳{{ number_format($item->total) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals Summary -->
                        <div class="receipt-summary">
                            <div class="summary-row">
                                <span>Sub Total</span>
                                <span>৳{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span>৳{{ number_format($order->shipping_charge, 2) }}</span>
                            </div>
                            @if($order->tax > 0)
                                <div class="summary-row">
                                    <span>Tax</span>
                                    <span>৳{{ number_format($order->tax, 2) }}</span>
                                </div>
                            @endif
                            @if($order->discount > 0)
                                <div class="summary-row" style="color: #dc2626;">
                                    <span>Discount</span>
                                    <span>-৳{{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="summary-row total-row">
                                <span>Order Total</span>
                                <span class="total-value">৳{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
