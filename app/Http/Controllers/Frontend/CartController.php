<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{Cart, CartItem, Product, Coupon};
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart()
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        if (!session()->has('session_active')) {
            session()->put('session_active', true);
        }
        $sessionId = session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.product.primaryImage', 'items.variant', 'coupon');
        return view('frontend.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:10',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = $this->getCart();

        // Determine price: use variant price if variant selected
        $price = $product->current_price;
        if ($request->variant_id) {
            $variant = \App\Models\ProductVariant::find($request->variant_id);
            if ($variant) {
                $price = $variant->current_price;
            }
        }

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $cartItem->update(['quantity' => $cartItem->quantity + ($request->quantity ?? 1)]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity ?? 1,
                'price' => $price,
            ]);
        }

        // Track abandoned cart activity
        app(\App\Services\Cart\AbandonedCartService::class)->trackCartActivity($cart, 'cart_created');

        return response()->json(['message' => 'Product added to cart', 'count' => $cart->fresh()->total_items]);
    }

    public function restore($token, \App\Services\Cart\AbandonedCartService $cartService)
    {
        try {
            $cartService->restoreCartFromToken($token);
            return redirect()->route('checkout.index')->with('success', 'Your cart items have been restored!');
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Unable to restore cart session.');
        }
    }

    public function update(Request $request)
    {
        $request->validate(['item_id' => 'required', 'quantity' => 'required|integer|min:1']);

        $cart = $this->getCart();
        $item = CartItem::where('cart_id', $cart->id)->where('id', $request->item_id)->firstOrFail();
        $item->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cart updated', 'subtotal' => $cart->fresh()->subtotal]);
    }

    public function remove($id)
    {
        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)->where('id', $id)->delete();
        return response()->json(['message' => 'Item removed']);
    }

    public function count()
    {
        $cart = $this->getCart();
        return response()->json(['count' => $cart->total_items]);
    }

    public function sidebar()
    {
        $cart = $this->getCart();
        $cart->load('items.product.primaryImage');
        return view('components.cart-sidebar', compact('cart'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::valid()->where('code', $request->code)->first();
        if (!$coupon) {
            return response()->json(['message' => 'Invalid or expired coupon'], 422);
        }

        $cart = $this->getCart();
        if ($coupon->min_order && $cart->subtotal < $coupon->min_order) {
            return response()->json(['message' => 'Minimum order amount is ৳' . number_format($coupon->min_order)], 422);
        }

        $cart->update(['coupon_id' => $coupon->id]);
        return response()->json(['message' => 'Coupon applied!', 'discount' => $coupon->calculateDiscount($cart->subtotal)]);
    }

    public function removeCoupon()
    {
        $cart = $this->getCart();
        $cart->update(['coupon_id' => null]);
        return response()->json(['message' => 'Coupon removed']);
    }
}
