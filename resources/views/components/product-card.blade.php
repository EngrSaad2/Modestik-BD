{{-- Product Card Component --}}
<div class="{{ $gridClass ?? 'col-6 col-md-4 col-lg-3' }}">
    <div class="product-card">
        <div class="product-image">
            <a href="{{ route('products.show', $product->slug) }}">
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" loading="lazy" width="220" height="220" style="aspect-ratio: 1/1; object-fit: cover;">
            </a>


            {{-- Action Buttons --}}
            <div class="product-actions">
                <button class="product-action-btn wishlist-btn" data-product="{{ $product->id }}" title="উইশলিস্টে রাখুন">
                    <i class="far fa-heart"></i>
                </button>
                <a href="{{ route('products.show', $product->slug) }}" class="product-action-btn" title="বিস্তারিত">
                    <i class="far fa-eye"></i>
                </a>
            </div>
        </div>

        <div class="product-info">
            <div class="product-category">{{ $product->category->name ?? '' }}</div>
            <h3 class="product-name">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>

            @if($product->average_rating > 0)
            <div class="product-rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star{{ $i <= round($product->average_rating) ? '' : '-half-alt' }}"></i>
                @endfor
                <span>({{ $product->review_count }})</span>
            </div>
            @endif

            <div class="product-price">
                <span class="current-price">{{ $product->formatted_price }}</span>
                @if(!$product->has_variants && $product->discount_percentage > 0)
                    <span class="original-price">{{ $product->formatted_original_price }}</span>
                @endif
            </div>

            <div class="product-card-buttons">
                <button class="add-to-cart-btn" onclick="addToCart(event, {{ $product->id }})">
                    <i class="fas fa-shopping-cart"></i> Add To Cart
                </button>
                <button class="buy-now-btn" onclick="buyNow(event, {{ $product->id }})">
                    <i class="fas fa-shopping-bag"></i> Buy Now
                </button>
            </div>
        </div>
    </div>
</div>


