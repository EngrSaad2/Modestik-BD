<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{Cart, CartItem, Order, OrderItem, OrderStatusHistory, ShippingZone};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        if ($cart->items->isEmpty()) return redirect()->route('cart.index')->with('error', 'Cart is empty');

        // Track Checkout Started
        $customerData = [];
        if (auth()->check()) {
            $user = auth()->user();
            $customerData = [
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
            ];
        }

        app(\App\Services\Cart\AbandonedCartService::class)->trackCartActivity($cart, 'checkout_started', $customerData);

        $cart->load('items.product.primaryImage', 'items.variant', 'coupon');
        $shippingZones = ShippingZone::active()->get();
        $addresses = auth()->check() ? auth()->user()->addresses : collect();
        $districts = \App\Support\BangladeshLocations::getLocations();

        return view('frontend.checkout', compact('cart', 'shippingZones', 'addresses', 'districts'));
    }

    public function updateLead(Request $request)
    {
        $cart = $this->getCart();
        app(\App\Services\Cart\AbandonedCartService::class)->trackCartActivity($cart, 'shipping_info', [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'division' => $request->division,
            'district' => $request->district,
            'thana' => $request->thana,
        ]);

        return response()->json(['success' => true]);
    }

    public function placeOrder(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^01[3-9]\d{8}$/',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'division' => 'required|string',
            'district' => 'required|string',
            'shipping_zone_id' => 'required|exists:shipping_zones,id',
            'payment_method' => 'required|in:cod,bkash',
            'bkash_number' => 'required_if:payment_method,bkash|nullable|string|regex:/^01[3-9]\d{8}$/',
            'bkash_trx_id' => 'required_if:payment_method,bkash|nullable|string|min:8|max:20',
        ];

        if ($request->has('create_account') && !auth()->check()) {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $request->validate($rules, [
            'name.required' => 'অনুগ্রহ করে আপনার নাম লিখুন।',
            'phone.required' => 'অনুগ্রহ করে আপনার মোবাইল নম্বর লিখুন।',
            'phone.regex' => 'অনুগ্রহ করে একটি সঠিক মোবাইল নম্বর লিখুন (১১ ডিজিট)।',
            'address.required' => 'অনুগ্রহ করে আপনার সম্পূর্ণ ঠিকানা লিখুন।',
            'division.required' => 'থানা নির্বাচন/লিখন আবশ্যক।',
            'district.required' => 'জেলা নির্বাচন/লিখন আবশ্যক।',
            'shipping_zone_id.required' => 'ডেলিভারি এলাকা নির্বাচন করা আবশ্যক।',
            'payment_method.required' => 'পেমেন্ট পদ্ধতি নির্বাচন করা আবশ্যক।',
            'bkash_number.required_if' => 'বিকাশ নম্বরটি আবশ্যক।',
            'bkash_number.regex' => 'সঠিক বিকাশ মোবাইল নম্বর লিখুন (১১ ডিজিট)।',
            'bkash_trx_id.required_if' => 'বিকাশ ট্রানজেকশন আইডি আবশ্যক।',
            'email.required' => 'অ্যাকাউন্ট তৈরির জন্য ইমেইল আবশ্যক।',
            'email.unique' => 'এই ইমেইল দিয়ে ইতোমধ্যে একটি অ্যাকাউন্ট খোলা আছে।',
            'password.required' => 'পাসওয়ার্ড দেওয়া আবশ্যক।',
            'password.min' => 'পাসওয়ার্ড অন্তত ৬ অক্ষরের হতে হবে।',
            'password.confirmed' => 'পাসওয়ার্ড ও কনফার্ম পাসওয়ার্ড মিলছে না।',
        ]);

        $cart = $this->getCart();
        if ($cart->items->isEmpty()) return back()->with('error', 'কার্টটি খালি আছে');

        $cart->load('items.product', 'items.variant', 'coupon');
        
        // Dynamically find and validate shipping zone from district/thana selection
        $shippingZone = \App\Support\BangladeshLocations::getShippingZoneForLocation($request->district, $request->division);
        if ($shippingZone) {
            $shipping = $shippingZone->charge;
            $request->merge(['shipping_zone_id' => $shippingZone->id]);
        } else {
            // Default fallback
            $shipping = 130;
            $shippingZone = ShippingZone::find($request->shipping_zone_id);
        }

        $subtotal = $cart->subtotal;
        
        if ($subtotal >= 1000 || ($shippingZone && $shippingZone->free_shipping_min && $subtotal >= $shippingZone->free_shipping_min)) {
            $shipping = 0;
        }
        $discount = $cart->discount;
        $tax = 0;
        $total = $subtotal + $shipping - $discount + $tax;

        DB::beginTransaction();
        try {
            $userId = auth()->id();

            // Automatic customer account creation if requested
            if ($request->has('create_account') && !auth()->check()) {
                $user = \App\Models\User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                ]);
                if (method_exists($user, 'assignRole')) {
                    try { $user->assignRole('customer'); } catch (\Throwable $e) {}
                }
                auth()->login($user);
                $userId = $user->id;
            }

            $order = Order::create([
                'user_id' => $userId,
                'order_number' => Order::generateOrderNumber(),
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'division' => $request->division,
                'district' => $request->district,
                'area' => $request->area,
                'zip' => $request->zip,
                'subtotal' => $subtotal,
                'shipping_charge' => $shipping,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'coupon_id' => $cart->coupon_id,
                'coupon_code' => $cart->coupon?->code,
                'payment_method' => $request->payment_method,
                'bkash_number' => $request->payment_method === 'bkash' ? $request->bkash_number : null,
                'bkash_trx_id' => $request->payment_method === 'bkash' ? $request->bkash_trx_id : null,
                'payment_status' => 'pending',
                'status' => 'pending',
                'notes' => $request->notes,
                'shipping_zone_id' => $request->shipping_zone_id,
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

                // Decrement stock and log stock history
                if ($item->variant_id) {
                    $item->variant->decrement('quantity', $item->quantity);
                    $item->product->decrement('quantity', $item->quantity);
                } else {
                    $item->product->decrement('quantity', $item->quantity);
                }

                \App\Models\StockHistory::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type' => 'sale',
                    'quantity' => -$item->quantity,
                    'notes' => 'অর্ডার #' . $order->order_number . ' এর জন্য বিক্রি',
                ]);
            }

            // Order status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'comment' => 'Order placed',
                'changed_by' => auth()->id(),
            ]);

            // Notify admin users about the new order
            try {
                $admins = \App\Models\User::whereHas('roles', function($q) {
                    $q->whereIn('name', ['admin', 'super-admin']);
                })->orWhere('id', 1)->get();

                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\OrderPlacedNotification($order));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Order notification failed: ' . $e->getMessage());
            }

            // Increment coupon usage
            if ($cart->coupon) {
                $cart->coupon->increment('used_count');
            }

            // Clear cart & mark completed
            $cart->items()->delete();
            $cart->update(['coupon_id' => null, 'stage' => 'order_completed']);

            DB::commit();

            // Dispatch background Artisan command to send order confirmation emails instantly without blocking the page load
            try {
                $mailSent = false;
                $disabledFunctions = array_map('trim', explode(',', ini_get('disable_functions')));

                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    if (function_exists('popen') && !in_array('popen', $disabledFunctions)) {
                        $handle = popen("start /B php " . base_path('artisan') . " mail:send-order-confirmation " . $order->id, "r");
                        if ($handle !== false) {
                            pclose($handle);
                            $mailSent = true;
                        }
                    }
                } else {
                    if (function_exists('exec') && !in_array('exec', $disabledFunctions)) {
                        exec("php " . base_path('artisan') . " mail:send-order-confirmation " . $order->id . " > /dev/null 2>&1 &");
                        $mailSent = true;
                    }
                }

                if (!$mailSent) {
                    if ($order->email) {
                        \Illuminate\Support\Facades\Mail::to($order->email)->send(new \App\Mail\OrderConfirmationMail($order));
                    }
                    \Illuminate\Support\Facades\Mail::to(config('mail.from.address'))->send(new \App\Mail\OrderConfirmationMail($order));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Order Confirmation Mail failed: ' . $e->getMessage());
            }

            return redirect()->route('checkout.confirmation', $order->order_number);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Order placement transaction failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->with('error', 'Order placement failed. Please try again.');
        }
    }

    public function confirmation($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();
        return view('frontend.order-confirmation', compact('order'));
    }

    private function getCart()
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        if (!session()->has('session_active')) {
            session()->put('session_active', true);
        }
        return Cart::firstOrCreate(['session_id' => session()->getId()]);
    }
}
