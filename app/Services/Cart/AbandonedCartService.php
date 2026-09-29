<?php

namespace App\Services\Cart;

use App\Models\AbandonedCartLog;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\StockHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AbandonedCartService
{
    /**
     * Track and update cart activity with customer details and stage.
     */
    public function trackCartActivity(Cart $cart, ?string $stage = null, array $customerData = []): Cart
    {
        $data = [
            'last_activity_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if (!empty($stage)) {
            $data['stage'] = $stage;
        }

        if (array_key_exists('name', $customerData) && $customerData['name'] !== null && $customerData['name'] !== '') {
            $data['customer_name'] = trim($customerData['name']);
        }
        if (array_key_exists('phone', $customerData) && $customerData['phone'] !== null && $customerData['phone'] !== '') {
            $data['phone'] = trim($customerData['phone']);
        }
        if (array_key_exists('email', $customerData) && $customerData['email'] !== null && $customerData['email'] !== '') {
            $data['email'] = trim($customerData['email']);
        }
        if (array_key_exists('address', $customerData) && $customerData['address'] !== null && $customerData['address'] !== '') {
            $data['address'] = trim($customerData['address']);
        }
        if (array_key_exists('division', $customerData) && $customerData['division'] !== null && $customerData['division'] !== '') {
            $data['division'] = trim($customerData['division']);
        }
        if (array_key_exists('district', $customerData) && $customerData['district'] !== null && $customerData['district'] !== '') {
            $data['district'] = trim($customerData['district']);
        }
        if (array_key_exists('thana', $customerData) && $customerData['thana'] !== null && $customerData['thana'] !== '') {
            $data['thana'] = trim($customerData['thana']);
        }

        // If user logged in after guest cart creation, attach user_id and default info if missing
        if (auth()->check()) {
            if (!$cart->user_id) {
                $data['user_id'] = auth()->id();
            }
            $user = auth()->user();
            if (empty($cart->customer_name) && empty($data['customer_name']) && !empty($user->name)) {
                $data['customer_name'] = $user->name;
            }
            if (empty($cart->phone) && empty($data['phone']) && !empty($user->phone)) {
                $data['phone'] = $user->phone;
            }
            if (empty($cart->email) && empty($data['email']) && !empty($user->email)) {
                $data['email'] = $user->email;
            }
        }

        $cart->update($data);
        return $cart->fresh();
    }

    /**
     * Automatically mark inactive carts past threshold as abandoned.
     */
    public function detectInactiveCarts(): int
    {
        $inactivityMinutes = (int) (Setting::where('key', 'abandoned_cart_inactivity_minutes')->value('value') ?? 30);
        $thresholdTime = now()->subMinutes($inactivityMinutes);

        $inactiveCarts = Cart::whereHas('items')
            ->whereNotIn('stage', ['order_completed', 'recovered', 'lost', 'abandoned'])
            ->where(function ($q) use ($thresholdTime) {
                $q->where('last_activity_at', '<', $thresholdTime)
                  ->orWhere(function ($sub) use ($thresholdTime) {
                      $sub->whereNull('last_activity_at')
                          ->where('updated_at', '<', $thresholdTime);
                  });
            })
            ->get();

        $count = 0;
        foreach ($inactiveCarts as $cart) {
            $cart->update([
                'abandoned_at' => now(),
            ]);

            AbandonedCartLog::create([
                'cart_id' => $cart->id,
                'action' => 'marked_abandoned',
                'description' => "System automatically marked cart as abandoned after {$inactivityMinutes} minutes of inactivity.",
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * One-click conversion of abandoned cart to an Order.
     */
    public function convertCartToOrder(Cart $cart, array $overrideData = []): Order
    {
        return DB::transaction(function () use ($cart, $overrideData) {
            $cart->load('items.product', 'items.variant');

            if ($cart->items->count() === 0) {
                throw new \Exception("Cart has no items to create an order.");
            }

            $orderNumber = 'ORD' . date('Ymd') . rand(1000, 9999);
            $subtotal = $cart->subtotal;
            $discount = $cart->discount;
            $shipping = 100; // Default shipping
            $tax = 0;
            $total = max(0, $subtotal - $discount + $shipping);

            $name = $overrideData['name'] ?? $cart->customer_display_name;
            $phone = $overrideData['phone'] ?? $cart->customer_display_phone;
            $email = $overrideData['email'] ?? $cart->email ?? $cart->user?->email;
            $address = $overrideData['address'] ?? $cart->address ?? 'Address not specified';
            $district = $overrideData['district'] ?? $cart->district ?? 'Dhaka';

            $order = Order::create([
                'user_id' => $cart->user_id,
                'order_number' => $orderNumber,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'address' => $address,
                'district' => $district,
                'subtotal' => $subtotal,
                'shipping_charge' => $shipping,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'coupon_id' => $cart->coupon_id,
                'coupon_code' => $cart->coupon?->code,
                'payment_method' => $overrideData['payment_method'] ?? 'cod',
                'payment_status' => $overrideData['payment_status'] ?? 'pending',
                'status' => 'pending',
                'notes' => "Recovered from Incomplete Cart #" . $cart->id . " by " . (auth()->user()?->name ?? 'Admin'),
            ]);

            foreach ($cart->items as $item) {
                $itemCost = $item->variant?->cost ?? $item->product->cost ?? 0;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'variant_info' => $item->variant?->display_name,
                    'price' => $item->price,
                    'cost' => $itemCost,
                    'quantity' => $item->quantity,
                    'total' => $item->price * $item->quantity,
                ]);

                // Inventory decrement
                if ($item->variant_id) {
                    $item->variant->decrement('quantity', $item->quantity);
                    $item->product->decrement('quantity', $item->quantity);
                } else {
                    $item->product->decrement('quantity', $item->quantity);
                }

                StockHistory::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type' => 'sale',
                    'quantity' => $item->quantity,
                    'cost' => $itemCost,
                    'notes' => "অর্ডার রিকভারি (#{$order->order_number})",
                ]);
            }

            // Mark cart as recovered
            $cart->update([
                'stage' => 'recovered',
                'crm_status' => 'recovered',
                'recovered_order_id' => $order->id,
                'recovered_at' => now(),
                'recovered_by_id' => auth()->id(),
            ]);

            AbandonedCartLog::create([
                'cart_id' => $cart->id,
                'user_id' => auth()->id(),
                'action' => 'order_created',
                'description' => "Created Order #{$order->order_number} and marked cart as Recovered.",
            ]);

            return $order;
        });
    }

    /**
     * Restore abandoned cart into active user session.
     */
    public function restoreCartFromToken(string $token): Cart
    {
        $abandonedCart = Cart::with('items')->where('recovery_token', $token)->firstOrFail();

        // Get current active session cart
        $currentSessionId = session()->getId();
        $targetCart = null;

        if (auth()->check()) {
            $targetCart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        } else {
            $targetCart = Cart::firstOrCreate(['session_id' => $currentSessionId]);
        }

        // Copy items if target cart is different
        if ($targetCart->id !== $abandonedCart->id) {
            foreach ($abandonedCart->items as $item) {
                CartItem::updateOrCreate(
                    [
                        'cart_id' => $targetCart->id,
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                    ],
                    [
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ]
                );
            }
        }

        $abandonedCart->update([
            'stage' => 'checkout_started',
            'last_activity_at' => now(),
        ]);

        AbandonedCartLog::create([
            'cart_id' => $abandonedCart->id,
            'action' => 'cart_restored',
            'description' => "Customer restored cart via recovery link.",
        ]);

        return $targetCart;
    }

    /**
     * Summary Metrics for Dashboard.
     */
    public function getMetrics(): array
    {
        $incompleteQuery = Cart::incomplete();

        $totalIncompleteCarts = (clone $incompleteQuery)->count();
        
        $potentialRevenueLost = (float) (clone $incompleteQuery)
            ->get()
            ->sum(fn($c) => $c->total_value);

        $cartsToday = Cart::incomplete()->whereDate('created_at', now()->toDateString())->count();
        $cartsMonth = Cart::incomplete()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $totalAbandonedCount = Cart::whereIn('stage', ['abandoned', 'recovered', 'lost'])->count();
        $recoveredCount = Cart::where('stage', 'recovered')->orWhere('crm_status', 'recovered')->count();

        $recoveryRate = $totalAbandonedCount > 0 ? round(($recoveredCount / $totalAbandonedCount) * 100, 1) : 0;

        $recoveredRevenue = (float) Order::whereIn('id', Cart::whereNotNull('recovered_order_id')->pluck('recovered_order_id'))->sum('total');

        return [
            'total_incomplete_carts' => $totalIncompleteCarts,
            'potential_revenue_lost' => $potentialRevenueLost,
            'carts_today' => $cartsToday,
            'carts_month' => $cartsMonth,
            'recovery_rate' => $recoveryRate,
            'recovered_count' => $recoveredCount,
            'recovered_revenue' => $recoveredRevenue,
        ];
    }
}
