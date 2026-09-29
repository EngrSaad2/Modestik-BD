@extends('layouts.frontend')

@section('title', 'Track Your Order - Modestik')

@section('content')
<section class="section">
    <div class="container">
        <div class="auth-card">
            <h2 class="text-center">অর্ডার ট্র্যাক করুন</h2>
            <p class="text-center">আপনার অর্ডারের ডেলিভারি অবস্থা জানতে নিচে আপনার অর্ডার নম্বর এবং মোবাইল নম্বর লিখুন।</p>

            <form action="{{ route('track-order.search') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:13px;">অর্ডার নম্বর</label>
                    <input type="text" name="order_number" class="form-control" required placeholder="যেমন: ORD202607090001" value="{{ old('order_number') }}">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold" style="font-size:13px;">মোবাইল নম্বর</label>
                    <input type="text" name="phone" class="form-control" required placeholder="যেমন: 01XXXXXXXXX" value="{{ old('phone') }}">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">অর্ডার ট্র্যাক করুন</button>
            </form>
        </div>
    </div>
</section>
@endsection
