{{-- Cart Sidebar Content --}}
@if($cart->items->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart text-muted mb-3" style="font-size:36px;"></i>
        <p class="text-muted mb-0">Your cart is empty</p>
    </div>
@else
    <div class="cart-sidebar-list mb-4">
        @foreach($cart->items as $item)
            <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                <img src="{{ $item->product->primary_image_url }}" alt="img" class="rounded" style="width:50px;height:50px;object-fit:cover;margin-right:12px;">
                <div class="flex-grow-1">
                    <h6 class="mb-0 fw-bold" style="font-size:13px; line-height:1.3;">
                        <a href="{{ route('products.show', $item->product->slug) }}" class="text-dark">{{ $item->product->name }}</a>
                    </h6>
                    @if($item->variant)
                        <span class="text-muted d-block" style="font-size:11px;">Option: {{ $item->variant->display_name }}</span>
                    @endif
                    <small class="text-muted">{{ $item->quantity }} x ৳{{ number_format($item->price) }}</small>
                </div>
                <button class="btn btn-sm text-danger" onclick="sidebarRemoveItem({{ $item->id }})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        @endforeach
    </div>

    <div class="cart-sidebar-summary border-top pt-3">
        @php
            $freeShippingThreshold = 1000;
            $subtotal = $cart->subtotal;
            $percentage = min(100, ($subtotal / $freeShippingThreshold) * 100);
            $remaining = $freeShippingThreshold - $subtotal;
        @endphp

        <!-- Free Shipping Progress Bar -->
        <div class="minimum-order-progress-wrapper mb-3">
            <div class="d-flex justify-content-between mb-1" style="font-size: 12px;">
                @if($remaining > 0)
                    <span class="text-muted">ফ্রি ডেলিভারি পেতে আর মাত্র <strong>৳{{ number_format($remaining) }}</strong> প্রয়োজন</span>
                @else
                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>আপনি ফ্রি ডেলিভারি পাচ্ছেন!</span>
                @endif
                <span class="fw-bold">{{ number_format($percentage, 0) }}%</span>
            </div>
            <div class="progress" style="height: 8px; background-color: #f1f5f9; border-radius: 4px; overflow: hidden;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                     style="width: {{ $percentage }}%; background: var(--primary-gradient); transition: width 0.4s ease;" 
                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Subtotal:</span>
            <strong class="text-dark">৳{{ number_format($cart->subtotal) }}</strong>
        </div>
        @if($cart->discount > 0)
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Discount:</span>
                <strong class="text-danger">-৳{{ number_format($cart->discount) }}</strong>
            </div>
        @endif
        <hr>
        <div class="d-flex justify-content-between mb-4">
            <span class="fw-bold">Total:</span>
            <strong class="text-primary fs-5">৳{{ number_format($cart->subtotal - $cart->discount) }}</strong>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('cart.index') }}" class="btn btn-outline-primary flex-grow-1">View Cart</a>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary flex-grow-1">Checkout</a>
        </div>
    </div>
@endif

<script>
function sidebarRemoveItem(itemId) {
    $.ajax({
        url: '{{ url("cart/remove") }}/' + itemId,
        method: 'DELETE',
        success: function() {
            loadCartSidebar();
            updateCartCount();
        }
    });
}
</script>
