@extends('layouts.frontend')

@section('title', 'Submit Complaint - Modestik')

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">হোম</a></li>
                <li class="breadcrumb-item active" aria-current="page">অভিযোগ বক্স</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="auth-card max-width-600 mx-auto">
            <h2 class="text-center fw-bold mb-3 text-primary">অভিযোগ বক্স</h2>
            <p class="text-center text-muted mb-4">আমাদের পণ্য বা ডেলিভারি নিয়ে কোনো অভিযোগ বা পরামর্শ থাকলে নিচে তা আমাদের জানান। আপনার অভিযোগটি সম্পূর্ণরূপে গোপন রাখা হবে।</p>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('complaints.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:13px;">আপনার নাম <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="আপনার সম্পূর্ণ নাম লিখুন" value="{{ old('name', auth()->user()?->name) }}">
                </div>
                <div class="row g-2">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold" style="font-size:13px;">মোবাইল নম্বর <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required placeholder="যেমন: 01XXXXXXXXX" value="{{ old('phone', auth()->user()?->phone) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold" style="font-size:13px;">অর্ডার নম্বর (যদি থাকে)</label>
                        <input type="text" name="order_number" class="form-control" placeholder="যেমন: ORD2026XXXXXXXX" value="{{ old('order_number') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:13px;">অভিযোগের বিষয় <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" required placeholder="যেমন: ডেলিভারি সংক্রান্ত বিলম্ব / ড্যামেজ পণ্য" value="{{ old('subject') }}">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold" style="font-size:13px;">অভিযোগের বিবরণ <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control" rows="5" required placeholder="এখানে বিস্তারিতভাবে আপনার অভিযোগটি লিখুন..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold text-white fs-6">অভিযোগ জমা দিন</button>
            </form>
        </div>
    </div>
</section>
@endsection
