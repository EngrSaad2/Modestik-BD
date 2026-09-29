@extends('layouts.frontend')

@section('title', 'Order details - Modestik')

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.orders') }}">My Orders</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Panel -->
            <div class="col-lg-3">
                <div class="bg-white rounded-3 shadow-sm p-3">
                    <div class="text-center py-3 border-bottom mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold" style="width:60px;height:60px;font-size:22px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-0">{{ auth()->user()->name }}</h6>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    <div class="list-group list-group-flush" style="font-size: 14px;">
                        <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action border-0 py-2.5 fw-bold text-primary"><i class="fas fa-shopping-bag me-2"></i>My Orders</a>
                        <a href="{{ route('customer.wishlist') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-heart me-2"></i>Wishlist</a>
                        <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-map-marker-alt me-2"></i>Addresses</a>
                        <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-user me-2"></i>Profile Update</a>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Order: {{ $order->order_number }}</h5>
                    <a href="{{ route('customer.orders.invoice', $order->order_number) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-pdf me-2"></i>Invoice PDF</a>
                </div>

                <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                    <h6 class="fw-bold mb-3">Order Status Details</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted d-block" style="font-size: 12px;">Customer Details</span>
                            <strong>{{ $order->name }}</strong><br>
                            <small class="text-muted">Phone: {{ $order->phone }}</small>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block" style="font-size: 12px;">Delivery Address</span>
                            <strong>{{ $order->address }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block" style="font-size: 12px;">Shipping Zone</span>
                            <strong>{{ $order->shippingZone->name ?? '' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted d-block" style="font-size: 12px;">Status Details</span>
                            <span>Order status is </span> <strong>{!! $order->status_badge !!}</strong>
                        </div>
                        @if($order->tracking_code || $order->tracking_number)
                        <div class="col-12 mt-3 pt-3 border-top">
                            <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 11px;">কুরিয়ার ট্র্যাকিং নম্বর (SteadFast Courier):</span>
                                    <strong class="text-primary fs-6">{{ $order->tracking_code ?? $order->tracking_number }}</strong>
                                    {!! $order->courier_status_badge !!}
                                </div>
                                <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-sm btn-primary fw-bold">
                                    <i class="fas fa-truck me-1"></i> লাইভ পার্সেল ট্র্যাক করুন
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Products list inside order -->
                <div class="bg-white rounded-3 shadow-sm p-4">
                    <h6 class="fw-bold mb-3">Ordered Items</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        @if($item->variant_info)
                                            <span class="text-muted d-block" style="font-size:11px;">Option: {{ $item->variant_info }}</span>
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

                    <div class="row mt-4 justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <strong>৳{{ number_format($order->subtotal, 2) }}</strong>
                            </div>
                            @if($order->discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Discount</span>
                                <strong>-৳{{ number_format($order->discount, 2) }}</strong>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Shipping</span>
                                <strong>৳{{ number_format($order->shipping_charge, 2) }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold text-primary fs-5">
                                <span>Grand Total</span>
                                <span>৳{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
