@extends('layouts.frontend')

@section('title', 'Track Order Result - Modestik')

@section('content')
<section class="section">
    <div class="container">
        <div class="bg-white rounded-3 shadow-sm p-5 max-width-800 mx-auto">
            <h4 class="fw-bold mb-4 text-center">অর্ডার স্ট্যাটাস: {{ $order->order_number }}</h4>

            <!-- Status Timeline -->
            <div class="order-tracking-timeline mb-5">
                @php
                    $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
                    $statusLabels = [
                        'pending' => 'অপেক্ষমান',
                        'confirmed' => 'নিশ্চিত',
                        'processing' => 'প্রক্রিয়াধীন',
                        'shipped' => 'পাঠানো হয়েছে',
                        'delivered' => 'ডেলিভারি সম্পন্ন'
                    ];
                    $currentIdx = array_search($order->status, $statuses);
                    if ($currentIdx === false) $currentIdx = -1; // e.g. cancelled/returned
                @endphp

                <div class="d-flex justify-content-between position-relative timeline-line-container">
                    <div class="timeline-progress-line" style="width: {{ $currentIdx * 25 }}%;"></div>
                    @foreach($statuses as $idx => $st)
                        <div class="text-center position-relative" style="z-index: 2; width: 60px;">
                            <div class="timeline-circle mx-auto {{ $idx <= $currentIdx ? 'completed' : '' }}">
                                @if($idx < $currentIdx)
                                    <i class="fas fa-check"></i>
                                @else
                                    {{ $idx + 1 }}
                                @endif
                            </div>
                            <span class="d-block mt-2 fw-bold" style="font-size: 11px;">{{ $statusLabels[$st] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Specs -->
            <div class="bg-light p-4 rounded-3 text-start">
                <div class="row g-3">
                    <div class="col-md-6">
                        <span class="text-muted d-block">গ্রাহকের নাম:</span>
                        <strong>{{ $order->name }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted d-block">মোবাইল নম্বর:</span>
                        <strong>{{ $order->phone }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted d-block">ডেলিভারি ঠিকানা:</span>
                        <strong>{{ $order->address }}</strong>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted d-block">বর্তমান অবস্থা:</span>
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
                        <strong>{!! $orderStatus !!}</strong>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('track-order') }}" class="btn btn-outline-primary">আরেকটি অর্ডার ট্র্যাক করুন</a>
            </div>
        </div>
    </div>
</section>

<style>
.order-tracking-timeline { padding: 20px 0; }
.timeline-line-container { height: 4px; background: #e2e8f0; margin-top: 15px; }
.timeline-progress-line { position: absolute; left: 0; top: 0; bottom: 0; background: var(--primary); transition: width 0.4s ease; }
.timeline-circle { width: 30px; height: 30px; border-radius: 50%; background: #e2e8f0; border: 3px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #64748b; margin-top: -13px; }
.timeline-circle.completed { background: var(--primary); color: #fff; }
</style>
@endsection
