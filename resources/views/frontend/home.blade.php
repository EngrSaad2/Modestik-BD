@extends('layouts.frontend')

@section('title', 'Modestik - Premium Organic Mehendi & Modest Beauty Shop in Bangladesh')
@section('meta_description', 'Modestik — Shop 100% natural & organic mehendi, nail powder, halal beauty products. Pure, chemical-free, premium quality. Free shipping across Bangladesh.')

@section('styles')
<style>
    /* Hero Slider styling overrides */
    .hero-section {
        position: relative;
    }
    @media (min-width: 768px) {
        .hero-slider, .hero-slide, .hero-slider .owl-item img {
            height: 600px !important;
            max-height: 600px !important;
            border-radius: 0 !important;
            object-fit: cover;
        }
    }
    @media (max-width: 767.98px) {
        .hero-slider, .hero-slide, .hero-slider .owl-item img {
            height: 42vw !important;
            max-height: none !important;
            border-radius: 0 !important;
            object-fit: cover !important;
        }
    }
    .hero-slider .owl-nav button.owl-prev,
    .hero-slider .owl-nav button.owl-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.8) !important;
        color: var(--primary) !important;
        font-size: 16px !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        z-index: 10;
        border: none !important;
    }
    .hero-slider .owl-nav button.owl-prev:hover,
    .hero-slider .owl-nav button.owl-next:hover {
        background: var(--primary) !important;
        color: #fff !important;
    }
    .hero-slider .owl-nav button.owl-prev {
        left: 20px;
    }
    .hero-slider .owl-nav button.owl-next {
        right: 20px;
    }
    .hero-slider .owl-dots {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        margin-top: 0 !important;
    }
    .hero-slider .owl-dots .owl-dot span {
        width: 10px;
        height: 10px;
        background: rgba(255, 255, 255, 0.5) !important;
        transition: all 0.3s ease;
    }
    .hero-slider .owl-dots .owl-dot.active span {
        width: 25px;
        background: #fff !important;
    }

    /* Category Diamond Scroll Layout */
    .diamond-scroll-wrapper {
        position: relative;
        width: 100%;
        overflow: hidden;
        margin: 20px 0;
    }

    .diamond-row-scrollable {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        justify-content: flex-start;
        padding: 35px 25px;
        gap: 30px;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .diamond-row-scrollable::-webkit-scrollbar {
        display: none;
    }
    @media (min-width: 1400px) {
        .diamond-row-scrollable {
            justify-content: center;
        }
    }
    .diamond-cell {
        flex: 0 0 auto;
        width: 140px;
        height: 140px;
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .diamond-cell:hover {
        transform: translateY(-10px);
        z-index: 10;
    }
    .diamond-link {
        display: block;
        width: 100%;
        height: 100%;
        text-decoration: none !important;
    }
    .diamond-shape {
        width: 100%;
        height: 100%;
        background: #ffffff;
        border: none;
        transform: rotate(45deg);
        border-radius: 18px;
        box-shadow: none;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }
    .diamond-shape::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.2) 30%,
            rgba(255, 255, 255, 0.8) 50%,
            rgba(255, 255, 255, 0.2) 70%,
            rgba(255, 255, 255, 0) 100%
        );
        transform: rotate(-45deg);
        transition: all 0.6s ease;
        opacity: 0;
        z-index: 2;
    }
    .diamond-content {
        transform: rotate(-45deg);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 135%;
        height: 135%;
        padding: 10px;
        text-align: center;
        z-index: 3;
    }
    .diamond-icon {
        font-size: 24px;
        color: var(--primary);
        margin-bottom: 6px;
        background: var(--primary-light);
        width: 68px;
        height: 68px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .diamond-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .diamond-title {
        font-size: 11px;
        font-weight: 700;
        color: #1e293b;
        max-width: 110px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .diamond-cell:hover .diamond-shape {
        border-color: transparent;
        box-shadow: none;
        background: linear-gradient(135deg, #ffffff, #fff1f2);
    }
    .diamond-cell:hover .diamond-shape::before {
        opacity: 1;
        left: 100%;
    }
    .diamond-cell:hover .diamond-icon {
        transform: scale(1.15);
        background: var(--primary);
        color: #ffffff;
    }
    .diamond-cell:hover .diamond-title {
        color: var(--primary-dark);
    }
    .categories-carousel {
        padding: 10px 0;
    }
    .categories-carousel .owl-stage-outer {
        padding: 15px 0;
        margin-top: -15px;
        margin-bottom: -15px;
    }
    .categories-carousel .owl-item {
        display: flex;
        justify-content: center;
    }
    @media (max-width: 768px) {
        .diamond-scroll-wrapper {
            margin: 10px 0;
        }
        .diamond-cell {
            width: 115px;
            height: 115px;
        }
        .diamond-shape {
            border-radius: 14px;
            border-width: 1.5px;
        }
        .diamond-icon {
            font-size: 20px;
            width: 68px !important;
            height: 68px !important;
            margin-bottom: 4px;
        }
        .diamond-title {
            font-size: 9px;
            max-width: 95px;
            line-height: 1.3;
        }

    }
</style>
@endsection

@section('content')
<!-- 1. Hero Section (Full Width Slider) -->
<section class="hero-section py-0" data-aos="fade-in">
    <div class="container-fluid px-0">
        <div class="hero-slider owl-carousel shadow-sm overflow-hidden">
            @forelse($sliders as $slider)
                @php
                    $webpImage = preg_replace('/\.(png|jpe?g)$/i', '.webp', $slider->image);
                    $slideSrc = file_exists(storage_path('app/public/' . $webpImage)) ? asset('storage/' . $webpImage) : asset('storage/' . $slider->image);
                @endphp
                <div class="hero-slide position-relative">
                    @if($slider->button_url)
                        <a href="{{ $slider->button_url }}">
                            <img src="{{ $slideSrc }}" alt="{{ $slider->title }}" class="w-100" width="1200" height="600" fetchpriority="high">
                        </a>
                    @else
                        <img src="{{ $slideSrc }}" alt="{{ $slider->title }}" class="w-100" width="1200" height="600" fetchpriority="high">
                    @endif
                </div>
            @empty
                <div class="hero-slide position-relative">
                    <img src="{{ asset('images/slider.png') }}" alt="মোডেসটিক মেহেদি কালেকশন" class="w-100" width="1200" height="600" fetchpriority="high">
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- 2. Explore Our Collections -->
<section class="section py-3" data-aos="fade-up">
    <div class="container text-center">
        <div class="mb-2">
            <h2 class="fw-bold d-inline-block position-relative pb-3" style="color: var(--dark); font-size: 18px;">
                Explore Our Collections
                <span style="content: ''; position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 3px; background: var(--primary); border-radius: 3px;"></span>
            </h2>
        </div>
        
        <div class="diamond-scroll-wrapper">
            <div class="categories-carousel owl-carousel" id="categoriesCarousel">
                @foreach($categories as $category)
                    <div class="diamond-cell" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 30 }}">
                        <a href="{{ route('products.category', $category->slug) }}" class="diamond-link">
                            <div class="diamond-shape">
                                <div class="diamond-content">
                                    <div class="diamond-icon">
                                        @if($category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                                        @else
                                            {!! $category->icon ?? '<i class="fas fa-tag"></i>' !!}
                                        @endif
                                    </div>
                                    <span class="diamond-title">{{ $category->name }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


<!-- 4. Top Selling Products Carousel -->
@if($flashSaleProducts->count() > 0)
<section class="section py-3" data-aos="fade-up">
    <div class="container">
        <div class="section-header text-center mb-2">
            <h2 class="section-title d-inline-block">Our Top Selling Items</h2>
        </div>
        <div class="products-carousel owl-carousel owl-theme shadow-sm p-4 bg-white rounded-3">
            @foreach($flashSaleProducts as $product)
                @include('components.product-card', [
                    'product' => $product, 
                    'gridClass' => 'w-100'
                ])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- 5. All Products Grid -->
@if($otherProducts->count() > 0)
<section class="section py-3" data-aos="fade-up">
    <div class="container">
        <div class="section-header text-center mb-2">
            <h2 class="section-title d-inline-block">All Products</h2>
        </div>
        <div class="row g-3 g-md-4">
            @foreach($otherProducts as $index => $product)
                @include('components.product-card', [
                    'product' => $product, 
                    'gridClass' => ($index >= 12 ? 'col-6 col-md-4 col-lg-3 d-none load-more-product' : 'col-6 col-md-4 col-lg-3')
                ])
            @endforeach
        </div>
        @if($otherProducts->count() > 12)
            <div class="text-center mt-5">
                <button class="btn btn-outline-primary px-4 py-2 fw-semibold" id="loadMoreBtn" style="border-radius: 20px;">Load More</button>
            </div>
        @endif
    </div>
</section>
@endif

<!-- 6. Customer Reviews & Testimonials -->
<section class="section py-3 bg-light" data-aos="fade-up">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title d-inline-block" style="font-size: 18px;">Our Valuable Customer Review</h2>
        </div>

        <div class="reviews-carousel owl-carousel owl-theme" id="reviewsCarousel">
            @forelse($featuredReviews->merge($highestRatedReviews)->unique('id')->take(8) as $rev)
            <div class="h-100 p-2">
                <div class="bg-white rounded-3 p-4 h-100 shadow-sm border d-flex flex-column justify-content-between">
                    <div>
                        <div class="text-warning mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $rev->rating ? 'text-warning' : 'text-muted' }}" style="font-size:13px;"></i>
                            @endfor
                        </div>
                        <p class="text-muted mb-3" style="font-size:13px; line-height: 1.5; font-style: italic; height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                            "{{ Str::limit($rev->comment, 100) }}"
                        </p>
                        @if($rev->images && is_array($rev->images) && count($rev->images) > 0)
                            <div class="d-flex gap-1 mb-3">
                                @foreach($rev->images as $img)
                                    <img src="{{ asset('storage/' . $img) }}" alt="review" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="d-flex align-items-center border-top pt-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:35px;height:35px;font-size:12px;flex-shrink:0;">
                            {{ strtoupper(substr($rev->user->name ?? 'গ্রাহক', 0, 1)) }}
                        </div>
                        <div class="ms-2">
                            <strong style="font-size:13px;" class="d-block text-dark">{{ $rev->user->name ?? 'ভেরিফাইড ক্রেতা' }}</strong>
                            <small class="text-muted" style="font-size:11px;">{{ $rev->product->name ?? 'পণ্য' }} এর রিভিউ</small>
                        </div>
                    </div>
                </div>
            </div>
            @empty
                @foreach($testimonials as $testimonial)
                <div class="h-100 p-2">
                    <div class="bg-white rounded-3 p-4 h-100 shadow-sm border d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-warning mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}" style="font-size:13px;"></i>
                                @endfor
                            </div>
                            <p class="text-muted mb-3" style="font-size:13px; line-height: 1.5; font-style: italic; height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                "{{ Str::limit($testimonial->content, 100) }}"
                            </p>
                        </div>
                        <div class="d-flex align-items-center border-top pt-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:35px;height:35px;font-size:12px;flex-shrink:0;">
                                {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                            </div>
                            <div class="ms-2">
                                <strong style="font-size:13px;" class="d-block text-dark">{{ $testimonial->name }}</strong>
                                <small class="text-muted" style="font-size:11px;">{{ $testimonial->designation }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Hero Slider
    $('.hero-slider').owlCarousel({
        loop: true, 
        items: 1, 
        autoplay: true,
        autoplayTimeout: 5000, 
        dots: false, 
        nav: false,
        animateOut: 'fadeOut', 
        animateIn: 'fadeIn',
    });

    // Categories Carousel
    $('#categoriesCarousel').owlCarousel({
        loop: true,
        autoplay: true,
        autoplayTimeout: 2000,
        autoplaySpeed: 800,
        autoplayHoverPause: true,
        dots: false,
        nav: false,
        responsive: {
            0: { items: 3, margin: 10 },
            480: { items: 3, margin: 15 },
            768: { items: 5, margin: 20 },
            992: { items: 6, margin: 25 },
            1200: { items: 8, margin: 30 }
        }
    });

    // Top Selling Products Carousel
    $('.products-carousel').owlCarousel({
        loop: true,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplaySpeed: 1000,
        autoplayHoverPause: true,
        margin: 20,
        nav: false,
        dots: true,
        responsive: {
            0: { items: 2 },
            768: { items: 3 },
            992: { items: 4 }
        }
    });

    // Customer Reviews Carousel
    $('#reviewsCarousel').owlCarousel({
        loop: true,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplaySpeed: 1000,
        autoplayHoverPause: true,
        margin: 20,
        nav: false,
        dots: true,
        responsive: {
            0: { items: 1 },
            576: { items: 2 },
            992: { items: 4 }
        }
    });

    // Load More functionality
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const hiddenProducts = document.querySelectorAll('.load-more-product.d-none');
            const showCount = Math.min(12, hiddenProducts.length);
            for (let i = 0; i < showCount; i++) {
                hiddenProducts[i].classList.remove('d-none');
            }
            if (document.querySelectorAll('.load-more-product.d-none').length === 0) {
                loadMoreBtn.parentElement.style.display = 'none';
            }
        });
    }
</script>
@endsection
