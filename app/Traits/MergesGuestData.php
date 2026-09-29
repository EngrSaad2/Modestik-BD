<?php

namespace App\Traits;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait MergesGuestData
{
    protected function mergeGuestData(Request $request)
    {
        $user = auth()->user();
        if (!$user) return;

        // Merge Cart
        try {
            $sessionId = $request->session()->getId();
            $guestCart = Cart::where('session_id', $sessionId)->first();
            
            if ($guestCart) {
                $userCart = Cart::firstOrCreate(['user_id' => $user->id]);
                
                foreach ($guestCart->items as $item) {
                    $existingItem = CartItem::where('cart_id', $userCart->id)
                        ->where('product_id', $item->product_id)
                        ->where('variant_id', $item->variant_id)
                        ->first();
                    
                    if ($existingItem) {
                        $existingItem->update([
                            'quantity' => $existingItem->quantity + $item->quantity
                        ]);
                        $item->delete();
                    } else {
                        $item->update(['cart_id' => $userCart->id]);
                    }
                }
                
                if ($guestCart->coupon_id && !$userCart->coupon_id) {
                    $userCart->update(['coupon_id' => $guestCart->coupon_id]);
                }
                
                $guestCart->delete();
            }
        } catch (\Throwable $e) {
            Log::error('Cart Merging failed: ' . $e->getMessage());
        }

        // Merge Wishlist
        try {
            $sessionWishlist = $request->session()->get('wishlist', []);
            foreach ($sessionWishlist as $productId) {
                Wishlist::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $productId
                ]);
            }
            $request->session()->forget('wishlist');
        } catch (\Throwable $e) {
            Log::error('Wishlist Merging failed: ' . $e->getMessage());
        }
    }
}
