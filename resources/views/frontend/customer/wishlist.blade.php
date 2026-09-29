@extends('layouts.frontend')

@section('title', 'My Wishlist - Modestik')

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">হোম</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">ড্যাশবোর্ড</a></li>
                <li class="breadcrumb-item active" aria-current="page">উইশলিস্ট</li>
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
                    @auth
                    <div class="text-center py-3 border-bottom mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold" style="width:60px;height:60px;font-size:22px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold mb-0">{{ auth()->user()->name }}</h6>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                    @else
                    <div class="text-center py-3 border-bottom mb-3">
                        <i class="fas fa-user-circle text-muted" style="font-size:48px;"></i>
                        <h6 class="fw-bold mt-2">অতিথি ব্যবহারকারী</h6>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm mt-2">লগইন করুন</a>
                    </div>
                    @endauth
                    
                    <div class="list-group list-group-flush" style="font-size: 14px;">
                        @auth
                        <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-tachometer-alt me-2"></i>ড্যাশবোর্ড</a>
                        <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-shopping-bag me-2"></i>আমার অর্ডারসমূহ</a>
                        @endauth
                        <a href="{{ route('customer.wishlist') }}" class="list-group-item list-group-item-action border-0 py-2.5 fw-bold text-primary"><i class="fas fa-heart me-2"></i>পছন্দের তালিকা (উইশলিস্ট)</a>
                        @auth
                        <a href="{{ route('customer.addresses') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-map-marker-alt me-2"></i>ঠিকানাসমূহ</a>
                        <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action border-0 py-2.5"><i class="fas fa-user me-2"></i>প্রোফাইল আপডেট</a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Content Panel -->
            <div class="col-lg-9">
                <h5 class="fw-bold mb-4">আমার পছন্দের তালিকা</h5>

                <div class="bg-white rounded-3 shadow-sm p-4">
                    @if($wishlist->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>পণ্য</th>
                                        <th>নাম</th>
                                        <th>মূল্য</th>
                                        <th class="text-end">অ্যাকশন</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wishlist as $item)
                                        <tr id="wishlist-row-{{ $item->product->id }}">
                                            <td style="width: 80px;">
                                                <a href="{{ route('products.show', $item->product->slug) }}">
                                                    <img src="{{ $item->product->primary_image_url }}" alt="img" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="text-dark fw-bold text-decoration-none">
                                                    {{ $item->product->name }}
                                                </a>
                                                <span class="text-muted d-block" style="font-size: 12px;">{{ $item->product->category->name ?? '' }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-primary">{{ $item->product->formatted_price }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-primary me-2" onclick="moveItemToCart({{ $item->product->id }})">
                                                    <i class="fas fa-shopping-cart me-1"></i> কার্টে নিন
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="removeWishlistItem({{ $item->product->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-heart text-muted mb-3" style="font-size:48px;"></i>
                            <p class="text-muted mb-0">আপনার পছন্দের তালিকাটি বর্তমানে খালি রয়েছে।</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm mt-3">এখনই শপিং করুন</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    function moveItemToCart(productId) {
        $.ajax({
            url: "{{ route('customer.wishlist.move-to-cart') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'সফল!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Update cart count in header
                    $('.cart-count').text(response.count);
                    
                    // Remove row from table
                    $('#wishlist-row-' + productId).fadeOut(500, function() {
                        $(this).remove();
                        if ($('tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                }
            },
            error: function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'দুঃখিত',
                    text: 'পণ্যটি কার্টে যোগ করা যায়নি।'
                });
            }
        });
    }

    function removeWishlistItem(productId) {
        $.ajax({
            url: "{{ route('customer.wishlist.toggle') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                product_id: productId
            },
            success: function(response) {
                if (response.status === 'removed') {
                    Swal.fire({
                        icon: 'success',
                        title: 'সফল!',
                        text: 'পছন্দের তালিকা থেকে মুছে ফেলা হয়েছে।',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    $('#wishlist-row-' + productId).fadeOut(500, function() {
                        $(this).remove();
                        if ($('tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                }
            }
        });
    }
</script>
@endsection
