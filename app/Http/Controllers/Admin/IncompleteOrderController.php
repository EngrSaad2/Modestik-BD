<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartLog;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Cart\AbandonedCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncompleteOrderController extends Controller
{
    protected AbandonedCartService $cartService;

    public function __construct(AbandonedCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Incomplete Orders Dashboard & Table
     */
    public function incomplete(Request $request)
    {
        // Detect inactive carts prior to listing
        $this->cartService->detectInactiveCarts();

        $query = Cart::with([
            'items.product.primaryImage',
            'items.variant',
            'user',
            'assignedStaff',
            'recoveredOrder'
        ])->incomplete()->latest('updated_at');

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items.product', function ($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filters
        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('crm_status')) {
            $query->where('crm_status', $request->crm_status);
        }

        if ($request->filled('assigned_staff_id')) {
            if ($request->assigned_staff_id === 'unassigned') {
                $query->whereNull('assigned_staff_id');
            } else {
                $query->where('assigned_staff_id', $request->assigned_staff_id);
            }
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('updated_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $carts = $query->paginate(15)->withQueryString();

        $metrics = $this->cartService->getMetrics();
        $staffMembers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'super-admin']);
        })->orWhere('id', 1)->get();

        return view('admin.orders.incomplete', compact('carts', 'metrics', 'staffMembers'));
    }

    /**
     * Get JSON payload for Recovery Console drawer.
     */
    public function showJson(Cart $cart)
    {
        $cart->load([
            'items.product.primaryImage',
            'items.variant',
            'user',
            'assignedStaff',
            'recoveredOrder',
            'logs.user'
        ]);

        $lifetimeValue = 0;
        if ($cart->user_id) {
            $lifetimeValue = (float) Order::where('user_id', $cart->user_id)
                ->where('status', 'delivered')
                ->sum('total');
        }

        $recoveryUrl = route('cart.restore', $cart->recovery_token);

        return response()->json([
            'id' => $cart->id,
            'recovery_token' => $cart->recovery_token,
            'recovery_url' => $recoveryUrl,
            'customer_name' => $cart->customer_display_name,
            'phone' => $cart->customer_display_phone,
            'email' => $cart->email ?? $cart->user?->email ?? 'N/A',
            'address' => $cart->address ?? 'N/A',
            'district' => $cart->district ?? 'Dhaka',
            'is_guest' => empty($cart->user_id),
            'stage' => $cart->stage,
            'stage_label' => $cart->stage_label,
            'stage_badge' => $cart->stage_badge_class,
            'crm_status' => $cart->crm_status,
            'crm_badge' => $cart->crm_badge_class,
            'assigned_staff_id' => $cart->assigned_staff_id,
            'ip_address' => $cart->ip_address ?? '127.0.0.1',
            'traffic_source' => $cart->traffic_source ?? 'Direct / Organic',
            'last_activity' => $cart->last_activity_at ? $cart->last_activity_at->format('n/j/Y | g:i A') : $cart->updated_at->format('n/j/Y | g:i A'),
            'lifetime_value' => $lifetimeValue,
            'subtotal' => $cart->subtotal,
            'discount' => $cart->discount,
            'total_value' => $cart->total_value,
            'items' => $cart->items->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->product?->name ?? 'Product Deleted',
                'image' => $item->product?->primary_image_url ?? asset('images/placeholder.png'),
                'variant' => $item->variant?->display_name ?? 'Default',
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->price * $item->quantity,
            ]),
            'logs' => $cart->logs->map(fn($log) => [
                'action' => $log->action,
                'description' => $log->description,
                'user_name' => $log->user?->name ?? 'System',
                'created_at' => $log->created_at->format('n/j/Y g:i A'),
            ]),
        ]);
    }

    /**
     * Update CRM Status, Assigned Staff, Notes.
     */
    public function updateStatus(Request $request, Cart $cart)
    {
        $request->validate([
            'crm_status' => 'nullable|string',
            'assigned_staff_id' => 'nullable',
            'note' => 'nullable|string',
        ]);

        $updates = [];
        if ($request->filled('crm_status')) {
            $updates['crm_status'] = $request->crm_status;
        }

        if ($request->has('assigned_staff_id')) {
            $updates['assigned_staff_id'] = $request->assigned_staff_id === 'unassigned' || empty($request->assigned_staff_id) ? null : $request->assigned_staff_id;
        }

        if (!empty($updates)) {
            $cart->update($updates);
        }

        if ($request->filled('note')) {
            AbandonedCartLog::create([
                'cart_id' => $cart->id,
                'user_id' => auth()->id(),
                'action' => 'note_added',
                'description' => $request->note,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Cart lead updated successfully.']);
    }

    /**
     * Create Order directly from Recovery Console.
     */
    public function createOrder(Request $request, Cart $cart)
    {
        try {
            $order = $this->cartService->convertCartToOrder($cart, $request->all());
            return response()->json([
                'success' => true,
                'message' => "Order #{$order->order_number} created successfully and cart marked as Recovered!",
                'order_url' => route('admin.orders.show', $order->id),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Log SMS send action.
     */
    public function sendSms(Request $request, Cart $cart)
    {
        $cart->update(['crm_status' => 'contacted']);

        AbandonedCartLog::create([
            'cart_id' => $cart->id,
            'user_id' => auth()->id(),
            'action' => 'sms_sent',
            'description' => "SMS recovery offer sent to " . $cart->customer_display_phone,
        ]);

        return response()->json(['success' => true, 'message' => 'SMS recovery logged.']);
    }

    /**
     * Log Email send action.
     */
    public function sendEmail(Request $request, Cart $cart)
    {
        $cart->update(['crm_status' => 'contacted']);

        AbandonedCartLog::create([
            'cart_id' => $cart->id,
            'user_id' => auth()->id(),
            'action' => 'email_sent',
            'description' => "Email recovery offer sent to " . ($cart->email ?? $cart->user?->email ?? 'customer'),
        ]);

        return response()->json(['success' => true, 'message' => 'Email recovery logged.']);
    }

    /**
     * Bulk actions for incomplete carts.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:carts,id',
            'action' => 'required|string',
        ]);

        $carts = Cart::whereIn('id', $request->ids)->get();

        if ($request->action === 'mark_recovered') {
            foreach ($carts as $c) {
                $c->update(['crm_status' => 'recovered', 'stage' => 'recovered', 'recovered_at' => now(), 'recovered_by_id' => auth()->id()]);
            }
            return back()->with('success', count($carts) . ' carts marked as Recovered.');
        }

        if ($request->action === 'mark_lost') {
            foreach ($carts as $c) {
                $c->update(['crm_status' => 'lost', 'stage' => 'lost']);
            }
            return back()->with('success', count($carts) . ' carts marked as Lost.');
        }

        if ($request->action === 'assign_staff' && $request->filled('staff_id')) {
            $staffId = $request->staff_id === 'unassigned' ? null : $request->staff_id;
            foreach ($carts as $c) {
                $c->update(['assigned_staff_id' => $staffId]);
            }
            return back()->with('success', count($carts) . ' carts assigned to staff.');
        }

        if ($request->action === 'delete') {
            foreach ($carts as $c) {
                $c->items()->delete();
                $c->delete();
            }
            return back()->with('success', count($carts) . ' incomplete cart records deleted.');
        }

        return back()->with('warning', 'No valid bulk action specified.');
    }

    /**
     * Abandoned Cart Analytics & Product Level Analysis
     */
    public function analytics(Request $request)
    {
        $metrics = $this->cartService->getMetrics();

        // Product Level Drop-off Analysis
        $productAnalysis = DB::table('cart_items')
            ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
            ->join('products', 'products.id', '=', 'cart_items.product_id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('COUNT(DISTINCT carts.id) as abandoned_cart_count'),
                DB::raw('SUM(cart_items.quantity) as abandoned_qty'),
                DB::raw('SUM(cart_items.price * cart_items.quantity) as potential_revenue'),
                DB::raw('SUM(CASE WHEN carts.stage = "recovered" THEN cart_items.quantity ELSE 0 END) as recovered_qty'),
                DB::raw('SUM(CASE WHEN carts.stage = "recovered" THEN cart_items.price * cart_items.quantity ELSE 0 END) as recovered_revenue')
            )
            ->whereIn('carts.stage', ['abandoned', 'shipping_info', 'checkout_started', 'payment_started', 'cart_viewed', 'recovered'])
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('abandoned_cart_count')
            ->get();

        return view('admin.abandoned-carts.analytics', compact('metrics', 'productAnalysis'));
    }
}
