<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Order, OrderStatusHistory, StockHistory};
use App\Services\Courier\CourierManager;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'courierSettledBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('consignment_id', 'like', "%{$search}%")
                  ->orWhere('tracking_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('district')) {
            $query->where('district', 'like', "%{$request->district}%");
        }

        if ($request->filled('courier_settlement_status')) {
            $query->where('courier_settlement_status', $request->courier_settlement_status);
        }

        if ($request->filled('courier_status')) {
            $query->where('courier_status', $request->courier_status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDir = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['id', 'order_number', 'name', 'status', 'payment_status', 'created_at', 'total', 'district', 'courier_status'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->latest();
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'statusHistory.changedBy', 'shippingZone', 'courierSettledBy', 'courierLogs.createdBy']);
        return view('admin.orders.show', compact('order'));
    }

    public function pushToCourier(Request $request, CourierManager $courierManager)
    {
        $request->validate([
            'ids' => 'nullable|array',
            'ids.*' => 'exists:orders,id',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $ids = $request->ids ?? ($request->order_id ? [$request->order_id] : []);

        if (empty($ids)) {
            return back()->with('error', 'No orders selected for Courier Push.');
        }

        $orders = Order::with('items')->whereIn('id', $ids)->get();

        $successCount = 0;
        $failedCount = 0;
        $alreadySentCount = 0;
        $messages = [];

        foreach ($orders as $order) {
            if ($order->consignment_id) {
                $alreadySentCount++;
                $messages[] = "Order #{$order->order_number}: Already sent (CID: {$order->consignment_id}).";
                continue;
            }

            $validationErrors = $order->validateForCourier();
            if (!empty($validationErrors)) {
                $failedCount++;
                $messages[] = "Order #{$order->order_number} Failed: " . implode(', ', $validationErrors);
                continue;
            }

            $res = $courierManager->driver('steadfast')->createParcel($order);

            if ($res['success']) {
                $successCount++;
                $order->update(['courier_status' => 'courier_assigned']);
                $messages[] = "Order #{$order->order_number}: Successfully pushed to SteadFast (Tracking: {$order->tracking_code}).";
            } else {
                $failedCount++;
                $messages[] = "Order #{$order->order_number} Failed: " . $res['message'];
            }
        }

        $summary = "Bulk Courier Push Finished — Sent: {$successCount}, Failed: {$failedCount}, Already Sent: {$alreadySentCount}.";
        
        if ($failedCount > 0) {
            return back()->with('warning', $summary . ' Details: ' . implode(' | ', $messages));
        }

        return back()->with('success', $summary);
    }

    public function trackParcel(Order $order, CourierManager $courierManager)
    {
        if (!$order->consignment_id && !$order->tracking_code) {
            return back()->with('error', 'Order has no Consignment ID or Tracking Code associated.');
        }

        $code = $order->consignment_id ?? $order->tracking_code;
        $res = $order->consignment_id 
            ? $courierManager->driver($order->courier_name ?? 'steadfast')->trackByConsignmentId($order->consignment_id)
            : $courierManager->driver($order->courier_name ?? 'steadfast')->trackByTrackingCode($order->tracking_code);

        if ($res['success'] && isset($res['status'])) {
            $oldStatus = $order->courier_status;
            $newStatus = $res['status'];

            $order->update([
                'courier_status' => $newStatus,
                'last_courier_sync_at' => now(),
            ]);

            if ($oldStatus !== $newStatus) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'comment' => "Real-time Courier Status updated from '{$oldStatus}' to '{$newStatus}' via API tracking.",
                    'changed_by' => auth()->id(),
                ]);
            }

            return back()->with('success', "Courier tracking refreshed. Current Status: " . ucfirst($newStatus));
        }

        return back()->with('error', "Tracking failed: " . ($res['message'] ?? 'Unknown error'));
    }

    public function updateStatus(Request $request, Order $order, \App\Services\Accounting\AccountingService $accountingService)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_status' => 'required|in:pending,paid,partially_paid,failed,refunded',
            'comment' => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->status;
        $oldPaymentStatus = $order->payment_status;

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        // Accounting Hook: Record payment in central ledger if paid for the first time
        if ($request->payment_status === 'paid' && $oldPaymentStatus !== 'paid') {
            $accountingService->recordOrderPayment($order, $order->total);
        }

        // Accounting Hook: If returned, restock inventory quantity
        if ($request->status === 'returned' && $oldStatus !== 'returned') {
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $variant = \App\Models\ProductVariant::find($item->variant_id);
                    if ($variant) $variant->increment('quantity', $item->quantity);
                }
                if ($item->product_id) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) $product->increment('quantity', $item->quantity);
                }

                \App\Models\StockHistory::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type' => 'return',
                    'quantity' => $item->quantity,
                    'cost' => $item->cost ?? 0,
                    'notes' => "অর্ডার রিটার্ন (#{$order->order_number})",
                ]);
            }
        }

        $comment = $request->comment;
        if (empty($comment)) {
            $comment = "Status changed from {$oldStatus} to {$request->status}";
            if ($request->payment_status !== $oldPaymentStatus) {
                $comment .= ", payment status changed from {$oldPaymentStatus} to {$request->payment_status}";
            }
        }

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $request->status,
            'comment' => $comment,
            'changed_by' => auth()->id(),
        ]);

        // Auto-VIP system trigger
        if ($request->status === 'delivered' && $order->user_id) {
            $user = $order->user;
            if ($user && !$user->is_vip) {
                $lifetimePurchases = Order::where('user_id', $user->id)
                    ->where('status', 'delivered')
                    ->sum('total');
                if ($lifetimePurchases >= 1000) {
                    $user->update(['is_vip' => true]);
                }
            }
        }

        return back()->with('success', 'Order status updated successfully.');
    }

    public function settleCourier(Order $order, CourierManager $courierManager, \App\Services\Accounting\AccountingService $accountingService)
    {
        // Auto push to SteadFast if not already pushed
        if (!$order->consignment_id) {
            $res = $courierManager->driver('steadfast')->createParcel($order);
            if (!$res['success']) {
                return back()->with('error', 'Settle Courier Failed: Parcel could not be created on SteadFast. ' . $res['message']);
            }
        }

        $order->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'courier_status' => $order->courier_status === 'unassigned' ? 'sent_to_courier' : $order->courier_status,
            'courier_settlement_status' => 'settled',
            'courier_settled_at' => now(),
            'courier_settled_by_id' => auth()->id(),
        ]);

        // Accounting Hook: Record courier settlement (COD collected, courier charges, net received)
        $codAmount = (float) $order->total;
        $courierCharge = (float) ($order->shipping_charge > 0 ? $order->shipping_charge : 100);
        $accountingService->recordCourierSettlement($order, $codAmount, $courierCharge);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => 'confirmed',
            'comment' => 'Courier Settled: Order confirmed, payment set to Paid, courier settlement marked Settled by ' . (auth()->user()?->name ?? 'Admin') . " (CID: {$order->consignment_id}, Tracking: {$order->tracking_code})",
            'changed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Courier settled successfully for order #' . $order->order_number);
    }

    public function bulkStatusUpdate(Request $request, CourierManager $courierManager)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
            'action' => 'nullable|string',
            'status' => 'nullable|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_status' => 'nullable|in:pending,paid,partially_paid,failed,refunded',
        ]);

        $orders = Order::with('items.product', 'items.variant')->whereIn('id', $request->ids)->get();

        if ($request->action === 'delete') {
            return $this->bulkDelete($request);
        }

        if ($request->action === 'settle_courier') {
            $pushed = 0;
            foreach ($orders as $order) {
                if (!$order->consignment_id) {
                    $res = $courierManager->driver('steadfast')->createParcel($order);
                    if ($res['success']) $pushed++;
                }
                $order->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'courier_status' => $order->courier_status === 'unassigned' ? 'sent_to_courier' : $order->courier_status,
                    'courier_settlement_status' => 'settled',
                    'courier_settled_at' => now(),
                    'courier_settled_by_id' => auth()->id(),
                ]);
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => 'confirmed',
                    'comment' => 'Bulk Courier Settled',
                    'changed_by' => auth()->id(),
                ]);
            }
            return back()->with('success', count($orders) . ' orders settled with courier (' . $pushed . ' auto-pushed).');
        }

        foreach ($orders as $order) {
            $oldStatus = $order->status;
            $updates = [];
            if ($request->filled('status')) $updates['status'] = $request->status;
            if ($request->filled('payment_status')) $updates['payment_status'] = $request->payment_status;

            if (!empty($updates)) {
                $order->update($updates);


                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'comment' => 'Bulk status update applied',
                    'changed_by' => auth()->id(),
                ]);
            }
        }

        return back()->with('success', 'Selected orders updated successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
        ]);

        $orders = Order::with('items')->whereIn('id', $request->ids)->get();
        $count = $orders->count();

        foreach ($orders as $order) {
            // Restore inventory if order was in active status
            if (!in_array($order->status, ['cancelled', 'returned'])) {
                foreach ($order->items as $item) {
                    if ($item->variant_id && $item->variant) {
                        $item->variant->increment('quantity', $item->quantity);
                    }
                    if ($item->product_id && $item->product) {
                        $item->product->increment('quantity', $item->quantity);
                    }
                }
            }

            $order->items()->delete();
            $order->statusHistory()->delete();
            if (\Illuminate\Support\Facades\Schema::hasTable('courier_logs')) {
                \Illuminate\Support\Facades\DB::table('courier_logs')->where('order_id', $order->id)->delete();
            }
            $order->forceDelete();
        }

        return back()->with('success', $count . ' selected order(s) deleted permanently.');
    }

    public function destroy(Order $order)
    {
        $orderNumber = $order->order_number;
        $order->loadMissing('items');

        if (!in_array($order->status, ['cancelled', 'returned'])) {
            foreach ($order->items as $item) {
                if ($item->variant_id && $item->variant) {
                    $item->variant->increment('quantity', $item->quantity);
                }
                if ($item->product_id && $item->product) {
                    $item->product->increment('quantity', $item->quantity);
                }
            }
        }

        $order->items()->delete();
        $order->statusHistory()->delete();
        if (\Illuminate\Support\Facades\Schema::hasTable('courier_logs')) {
            \Illuminate\Support\Facades\DB::table('courier_logs')->where('order_id', $order->id)->delete();
        }
        $order->forceDelete();

        return back()->with('success', "Order #{$orderNumber} deleted permanently.");
    }

    public function invoice(Order $order)
    {
        $order->load('items.product');
        
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [80, 220],
            'margin_left' => 4,
            'margin_right' => 4,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $html = view('frontend.invoice-pdf', compact('order'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output("invoice-{$order->order_number}.pdf", 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    /**
     * Show form to manually create an order by admin.
     */
    public function create()
    {
        $products = \App\Models\Product::with('variants', 'primaryImage')->latest()->get();
        $districts = \App\Support\BangladeshLocations::getLocations();
        return view('admin.orders.create', compact('products', 'districts'));
    }

    /**
     * Store manually created admin order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'address' => 'required|string',
            'district' => 'required|string',
            'source' => 'required|string|in:messenger,instagram,whatsapp,direct_call,website,other',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'shipping_charge' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $orderNumber = 'ORD' . date('Ymd') . rand(1000, 9999);
            $subtotal = 0;

            foreach ($request->items as $itemData) {
                $subtotal += $itemData['unit_price'] * $itemData['quantity'];
            }

            $shipping = $request->shipping_charge ?? 100;
            $discount = $request->discount ?? 0;
            $total = max(0, $subtotal - $discount + $shipping);

            $order = Order::create([
                'order_number' => $orderNumber,
                'source' => $request->source ?? 'direct_call',
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'district' => $request->district,
                'subtotal' => $subtotal,
                'shipping_charge' => $shipping,
                'discount' => $discount,
                'tax' => 0,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'status' => 'pending',
                'notes' => $request->notes ?? 'Created manually by Admin',
            ]);

            foreach ($request->items as $itemData) {
                $product = \App\Models\Product::find($itemData['product_id']);
                $variant = !empty($itemData['variant_id']) ? \App\Models\ProductVariant::find($itemData['variant_id']) : null;

                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'variant_name' => $variant?->display_name,
                    'price' => $itemData['unit_price'],
                    'quantity' => $itemData['quantity'],
                    'total' => $itemData['unit_price'] * $itemData['quantity'],
                ]);

                // Decrement stock
                if ($variant) {
                    $variant->decrement('quantity', $itemData['quantity']);
                } else {
                    $product->decrement('quantity', $itemData['quantity']);
                }
            }

            // Record accounting transaction if paid
            if ($request->payment_status === 'paid') {
                app(\App\Services\Accounting\AccountingService::class)->recordOrderPayment($order);
            }

            return redirect()->route('admin.orders.show', $order->id)->with('success', "Order #{$order->order_number} created successfully!");
        });
    }
}
