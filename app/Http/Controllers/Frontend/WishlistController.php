<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    private function getCart()
    {
        if (auth()->check()) {
            return \App\Models\Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        if (!session()->has('session_active')) {
            session()->put('session_active', true);
        }
        $sessionId = session()->getId();
        return \App\Models\Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function index()
    {
        if (auth()->check()) {
            $wishlist = Wishlist::where('user_id', auth()->id())
                ->with('product.primaryImage', 'product.category')
                ->latest()
                ->get();
        } else {
            $productIds = session()->get('wishlist', []);
            $products = \App\Models\Product::whereIn('id', $productIds)
                ->with('primaryImage', 'category')
                ->get();
            $wishlist = $products->map(fn($p) => (object)[
                'id' => $p->id,
                'product_id' => $p->id,
                'product' => $p
            ]);
        }
        return view('frontend.customer.wishlist', compact('wishlist'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $productId = $request->product_id;

        if (auth()->check()) {
            $userId = auth()->id();
            $wish = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();
            if ($wish) {
                $wish->delete();
                return response()->json(['message' => 'Removed from wishlist', 'status' => 'removed']);
            }
            Wishlist::create(['user_id' => $userId, 'product_id' => $productId]);
            return response()->json(['message' => 'Added to wishlist', 'status' => 'added']);
        } else {
            $wishlist = session()->get('wishlist', []);
            if (in_array($productId, $wishlist)) {
                $wishlist = array_values(array_diff($wishlist, [$productId]));
                session()->put('wishlist', $wishlist);
                return response()->json(['message' => 'Removed from wishlist', 'status' => 'removed']);
            }
            $wishlist[] = $productId;
            session()->put('wishlist', $wishlist);
            return response()->json(['message' => 'Added to wishlist', 'status' => 'added']);
        }
    }

    public function moveToCart(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $productId = $request->product_id;
        $product = \App\Models\Product::findOrFail($productId);
        $cart = $this->getCart();

        $cartItem = \App\Models\CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->whereNull('variant_id') // Move simple product
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => 1,
                'price' => $product->sale_price ?? $product->price,
            ]);
        }

        // Remove from wishlist
        if (auth()->check()) {
            Wishlist::where('user_id', auth()->id())->where('product_id', $productId)->delete();
        } else {
            $wishlist = session()->get('wishlist', []);
            $wishlist = array_values(array_diff($wishlist, [$productId]));
            session()->put('wishlist', $wishlist);
        }

        return response()->json([
            'success' => true,
            'message' => 'Moved to cart successfully',
            'count' => $cart->fresh()->total_items
        ]);
    }
}
