{{-- Live Search Suggestions Dropdown --}}
@forelse($products as $product)
    <a href="{{ route('products.show', $product->slug) }}" class="search-suggestion-item text-dark">
        <img src="{{ $product->primary_image_url }}" alt="img">
        <div class="flex-grow-1">
            <div class="name">{{ $product->name }}</div>
            <div class="price">{{ $product->formatted_price }}</div>
        </div>
    </a>
@empty
    <div class="p-3 text-center text-muted" style="font-size: 13px;">No products found</div>
@endforelse
