@extends('layouts.frontend')

@section('title', $product->meta_title ?? ($product->name . ' - Modestik'))
@section('meta_description', $product->meta_description ?? Str::limit(strip_tags($product->short_description ?? $product->description), 160))
@section('meta_keywords', $product->meta_keywords ?? ($product->name . ', ' . ($product->category->name ?? '') . ', Modestik'))
@section('meta_tags')
    <meta property="og:title" content="{{ $product->meta_title ?? ($product->name . ' - Modestik') }}">
    <meta property="og:description" content="{{ $product->meta_description ?? Str::limit(strip_tags($product->short_description ?? $product->description), 160) }}">
    <meta property="og:image" content="{{ $product->primary_image_url }}">
    <meta property="og:type" content="product">
    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="bg-white rounded-3 shadow-sm p-4">
            <div class="row g-4">
                <!-- Gallery Column -->
                <div class="col-md-6">
                    <div class="product-gallery">
                        <div class="product-main-image text-center p-3">
                            <img id="mainProductImg" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" style="max-height: 450px; object-fit: contain;">
                        </div>
                        <div class="product-thumbnails">
                            @foreach($product->images as $img)
                            <div class="product-thumbnail {{ $img->is_primary ? 'active' : '' }}" onclick="changeMainImage('{{ asset('storage/' . $img->image) }}', this)">
                                <img src="{{ asset('storage/' . $img->image) }}" alt="thumbnail">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Info Column -->
                <div class="col-md-6">
                    <span class="badge bg-primary mb-2">{{ $product->category->name ?? 'Catalog' }}</span>
                    <h2 class="fw-bold mb-2">{{ $product->name }}</h2>

                    @if($product->average_rating > 0)
                    <div class="product-rating mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($product->average_rating) ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <span class="text-muted ms-2">({{ $product->review_count }} Customer Reviews)</span>
                    </div>
                    @endif

                    <div class="product-price mb-3">
                        <h3 class="current-price fw-bold text-primary mb-0" id="displayPrice">{{ $product->formatted_price }}</h3>
                        @if(!$product->has_variants && $product->discount_percentage > 0)
                            <span class="original-price text-decoration-line-through text-muted ms-2" id="displayOriginalPrice">{{ $product->formatted_original_price }}</span>
                            <span class="badge bg-danger ms-2" id="displayDiscount">-{{ $product->discount_percentage }}% Off</span>
                        @else
                            <span class="original-price text-decoration-line-through text-muted ms-2" id="displayOriginalPrice" style="display: none;"></span>
                            <span class="badge bg-danger ms-2" id="displayDiscount" style="display: none;"></span>
                        @endif
                    </div>

                    <p class="text-muted mb-4">{{ $product->short_description }}</p>

                    <!-- Variants Form -->
                    <div class="product-options mb-4">
                        @if($product->has_variants && $product->variants->count() > 0)
                            <h6 class="fw-bold mb-2">Select Options</h6>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($product->variants as $variant)
                                    <button class="btn btn-outline-secondary btn-sm variant-select-btn" 
                                            data-id="{{ $variant->id }}"
                                            data-price="{{ $variant->price }}"
                                            data-sale-price="{{ $variant->sale_price }}"
                                            data-qty="{{ $variant->quantity }}"
                                            data-image="{{ $variant->image_url ?? '' }}">
                                        {{ $variant->display_name }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" id="selectedVariantId" value="">
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-4">
                        <span class="fw-bold me-2">Availability:</span>
                        <span id="stockStatus" class="badge {{ $product->is_in_stock ? 'bg-success' : 'bg-danger' }}">
                            {{ $product->is_in_stock ? 'In Stock (' . $product->quantity . ' units)' : 'Out of Stock' }}
                        </span>
                    </div>

                    <!-- Quantity & Action Grid -->
                    <div class="mb-4 d-flex align-items-center gap-3">
                        <span class="fw-bold" style="font-size: 16px;">Quantity:</span>
                        <div class="input-group" style="width: 120px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-radius: 4px;">
                            <button class="btn btn-outline-secondary border-secondary-subtle" onclick="changeQty(-1)" style="border-radius: 4px 0 0 4px;"><i class="fas fa-minus"></i></button>
                            <input type="text" id="productQty" class="form-control text-center border-secondary-subtle fw-semibold" value="1" readonly style="font-size: 15px;">
                            <button class="btn btn-outline-secondary border-secondary-subtle" onclick="changeQty(1)" style="border-radius: 0 4px 4px 0;"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    <!-- Custom Action Grid Buttons -->
                    <style>
                        .product-action-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 12px;
                            max-width: 500px;
                            margin-top: 15px;
                        }
                        .product-action-grid .btn {
                            padding: 12px 6px !important;
                            font-size: 12px !important;
                            font-weight: 700;
                            border-radius: 6px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 6px;
                            text-transform: uppercase;
                            border: none;
                            transition: all 0.25s ease;
                            color: #fff !important;
                            cursor: pointer;
                            text-decoration: none;
                            white-space: nowrap !important;
                        }
                        .product-action-grid .btn:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
                        }
                        .pag-add-to-cart {
                            background-color: #f28b27 !important; /* Orange */
                        }
                        .pag-add-to-cart:hover {
                            background-color: #e07a1b !important;
                        }
                        .pag-buy-now {
                            background-color: #0c201d !important; /* Dark green/black */
                        }
                        .pag-buy-now:hover {
                            background-color: #06100e !important;
                        }
                        .pag-whatsapp {
                            background-color: #128c7e !important; /* WhatsApp color */
                        }
                        .pag-whatsapp:hover {
                            background-color: #075e54 !important;
                        }
                        .pag-call {
                            background-color: #1b3d82 !important; /* Royal Blue */
                        }
                        .pag-call:hover {
                            background-color: #112a5e !important;
                        }
                        @media (max-width: 480px) {
                            .product-action-grid .btn {
                                font-size: 10px !important;
                                padding: 10px 4px !important;
                                gap: 4px !important;
                            }
                            .product-action-grid {
                                gap: 8px !important;
                            }
                        }

                    </style>

                    <div class="product-action-grid">
                        <button type="button" class="btn pag-add-to-cart" onclick="handleAddToCart(event)">
                            <i class="fas fa-shopping-bag"></i> ADD TO CART
                        </button>
                        <button type="button" class="btn pag-buy-now" onclick="handleBuyNow(event)">
                            BUY NOW
                        </button>
                        <a href="https://wa.me/8801621106653?text=I%20would%20like%20to%20order%20product:%20{{ urlencode($product->name) }}" class="btn pag-whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i> Order On WhatsApp
                        </a>
                        <a href="tel:+8801621106653" class="btn pag-call">
                            <i class="fas fa-phone-alt"></i> Call For Order
                        </a>
                    </div>

            <!-- Description & Reviews Tab -->
            <div class="mt-5 border-top pt-4">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active fw-bold" id="nav-desc-tab" data-bs-toggle="tab" data-bs-target="#nav-desc" type="button" role="tab">Description</button>
                        <button class="nav-link fw-bold" id="nav-reviews-tab" data-bs-toggle="tab" data-bs-target="#nav-reviews" type="button" role="tab">Reviews ({{ $product->review_count }})</button>
                    </div>
                </nav>
                <div class="tab-content p-3" id="nav-tabContent">
                    <!-- Description -->
                    <div class="tab-pane fade show active" id="nav-desc" role="tabpanel">
                        <div class="py-3">
                            {!! $product->description !!}
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div class="tab-pane fade" id="nav-reviews" role="tabpanel">
                        <div class="row py-3">
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-4">Customer Reviews</h5>
                                @forelse($product->approvedReviews as $review)
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong style="font-size:14px;">{{ $review->user->name }}</strong>
                                            <div class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size:12px;"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-muted d-block mb-2" style="font-size:11px;">{{ $review->created_at->format('d M Y') }}</span>
                                        <p class="text-muted mb-0" style="font-size:13px;">{{ $review->comment }}</p>
                                    </div>
                                @empty
                                    <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                                @endforelse
                            </div>

                            <!-- Write a review -->
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-4">Write a Review</h5>
                                @auth
                                    <form action="{{ route('customer.reviews.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold" style="font-size:13px;">Rating</label>
                                            <div class="rating-stars">
                                                <select class="form-select" name="rating" required>
                                                    <option value="5">5 Stars (Excellent)</option>
                                                    <option value="4">4 Stars (Good)</option>
                                                    <option value="3">3 Stars (Average)</option>
                                                    <option value="2">2 Stars (Poor)</option>
                                                    <option value="1">1 Star (Very Bad)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold" style="font-size:13px;">Your Comment</label>
                                            <textarea class="form-control" name="comment" rows="4" required placeholder="Write your review here..."></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </form>
                                @else
                                    <div class="bg-light p-3 rounded text-center">
                                        <p class="mb-2 text-muted">You must be logged in to post a review.</p>
                                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login Now</a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
        <div class="mt-5">
            <h4 class="fw-bold mb-4">Related Products</h4>
            <div class="row g-3">
                @foreach($relatedProducts as $rel)
                    @include('components.product-card', ['product' => $rel])
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@section('scripts')
<script>
    function changeMainImage(src, element) {
        $('#mainProductImg').attr('src', src);
        $('.product-thumbnail').removeClass('active');
        $(element).addClass('active');
    }

    // Touch swipe gesture support for product gallery
    $(document).ready(function() {
        let touchstartX = 0;
        let touchendX = 0;
        const mainImgContainer = document.querySelector('.product-main-image');

        if (mainImgContainer) {
            mainImgContainer.addEventListener('touchstart', function(e) {
                touchstartX = e.changedTouches[0].screenX;
            }, { passive: true });

            mainImgContainer.addEventListener('touchend', function(e) {
                touchendX = e.changedTouches[0].screenX;
                handleSwipeGesture();
            }, { passive: true });
        }

        function handleSwipeGesture() {
            let thumbnails = $('.product-thumbnail');
            if (thumbnails.length <= 1) return;

            let activeIndex = thumbnails.index($('.product-thumbnail.active'));
            if (activeIndex === -1) activeIndex = 0;

            if (touchendX < touchstartX - 50) {
                // Swiped Left -> Next Image
                let nextIndex = (activeIndex + 1) % thumbnails.length;
                thumbnails.eq(nextIndex).trigger('click');
            } else if (touchendX > touchstartX + 50) {
                // Swiped Right -> Previous Image
                let prevIndex = (activeIndex - 1 + thumbnails.length) % thumbnails.length;
                thumbnails.eq(prevIndex).trigger('click');
            }
        }
    });

    function changeQty(amount) {
        let qtyVal = parseInt($('#productQty').val());
        qtyVal += amount;
        if (qtyVal < 1) qtyVal = 1;
        if (qtyVal > 10) qtyVal = 10;
        $('#productQty').val(qtyVal);
    }

    // Default product image for fallback
    const defaultProductImage = '{{ $product->primary_image_url }}';

    // Handles variant selection
    $('.variant-select-btn').click(function() {
        $('.variant-select-btn').removeClass('btn-primary text-white').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary text-white');

        let variantId = $(this).data('id');
        let rawPrice = parseFloat($(this).data('price'));
        let rawSalePrice = $(this).data('sale-price') ? parseFloat($(this).data('sale-price')) : null;
        let qty = $(this).data('qty');
        let variantImage = $(this).data('image');

        $('#selectedVariantId').val(variantId);

        // Swap main image if variant has its own image
        if (variantImage) {
            $('#mainProductImg').attr('src', variantImage);
        } else {
            $('#mainProductImg').attr('src', defaultProductImage);
        }

        // Update display price
        let currentPrice = rawSalePrice ? rawSalePrice : rawPrice;
        $('#displayPrice').text('৳' + currentPrice.toFixed(2));

        if (rawSalePrice) {
            $('#displayOriginalPrice').text('৳' + rawPrice.toFixed(2)).show();
            let discountPercent = Math.round(((rawPrice - rawSalePrice) / rawPrice) * 100);
            $('#displayDiscount').text('-' + discountPercent + '% Off').show();
        } else {
            $('#displayOriginalPrice').hide();
            $('#displayDiscount').hide();
        }

        // Update stock
        if (qty > 0) {
            $('#stockStatus').removeClass('bg-danger').addClass('bg-success').text('In Stock (' + qty + ' units)');
        } else {
            $('#stockStatus').removeClass('bg-success').addClass('bg-danger').text('Out of Stock');
        }
    });

    // Handle add to cart with variant
    function handleAddToCart(e) {
        let hasVariants = {{ $product->has_variants ? 'true' : 'false' }};
        let variantId = $('#selectedVariantId').val();

        if (hasVariants && !variantId) {
            Swal.fire({ icon: 'warning', title: 'Attention', text: 'Please select an option first!' });
            return;
        }

        let qty = parseInt($('#productQty').val());
        addToCart(e, {{ $product->id }}, variantId ? variantId : null, qty);
    }

    // Handle buy now with variant
    function handleBuyNow(e) {
        let hasVariants = {{ $product->has_variants ? 'true' : 'false' }};
        let variantId = $('#selectedVariantId').val();

        if (hasVariants && !variantId) {
            Swal.fire({ icon: 'warning', title: 'Attention', text: 'Please select an option first!' });
            return;
        }

        let qty = parseInt($('#productQty').val());
        buyNow(e, {{ $product->id }}, variantId ? variantId : null, qty);
    }
</script>
@endsection
