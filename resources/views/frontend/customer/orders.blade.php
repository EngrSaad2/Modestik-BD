@extends('layouts.frontend')

@section('title', 'My Orders - Modestik')

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">হোম</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">ড্যাশবোর্ড</a></li>
                <li class="breadcrumb-item active" aria-current="page">আমার অর্ডারসমূহ</li>
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
                        <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-tachometer-alt me-2"></i>ড্যাশবোর্ড</a>
                        <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action border-0 py-2.5 fw-bold text-primary"><i class="fas fa-shopping-bag me-2"></i>আমার অর্ডারসমূহ</a>
                        <a href="{{ route('customer.wishlist') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-heart me-2"></i>পছন্দের তালিকা</a>
                        <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-map-marker-alt me-2"></i>সংরক্ষিত ঠিকানা</a>
                        <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-user me-2"></i>প্রোফাইল আপডেট</a>
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="col-lg-9">
                <h5 class="fw-bold mb-4">আমার অর্ডারসমূহ</h5>

                <div class="bg-white rounded-3 shadow-sm p-4">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>অর্ডার #</th>
                                    <th>তারিখ</th>
                                    <th>মূল্য</th>
                                    <th>পেমেন্ট পদ্ধতি</th>
                                    <th>স্ট্যাটাস</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td><strong>{{ $order->order_number }}</strong></td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td class="fw-bold">৳{{ number_format($order->total) }}</td>
                                    <td>{{ $order->payment_method === 'bkash' ? 'বিকাশ' : 'ক্যাশ অন ডেলিভারি' }}</td>
                                    <td>
                                        @php
                                            $orderStatus = match($order->status) {
                                                'pending' => '<span class="badge bg-warning">অপেক্ষমান</span>',
                                                'confirmed' => '<span class="badge bg-info">নিশ্চিত</span>',
                                                'processing' => '<span class="badge bg-primary">প্রক্রিয়াধীন</span>',
                                                'shipped' => '<span class="badge bg-secondary">পাঠানো হয়েছে</span>',
                                                'delivered' => '<span class="badge bg-success">ডেলিভারি সম্পন্ন</span>',
                                                'cancelled' => '<span class="badge bg-danger">বাতিল</span>',
                                                'returned' => '<span class="badge bg-dark">ফেরত এসেছে</span>',
                                                default => '<span class="badge bg-light text-dark">'.ucfirst($order->status).'</span>',
                                            };
                                        @endphp
                                        {!! $orderStatus !!}
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.orders.detail', $order->order_number) }}" class="btn btn-primary btn-sm">বিস্তারিত দেখুন</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">এখনো কোনো অর্ডার করা হয়নি।</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
