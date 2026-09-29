<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'source', 'order_number', 'name', 'phone', 'email', 'address',
        'division', 'district', 'area', 'zip', 'subtotal', 'shipping_charge',
        'discount', 'tax', 'total', 'coupon_id', 'coupon_code', 'payment_method',
        'bkash_number', 'bkash_trx_id',
        'payment_status', 'status', 'notes', 'tracking_number', 'shipping_zone_id',
        'courier_settlement_status', 'courier_settled_at', 'courier_settled_by_id',
        'courier_name', 'consignment_id', 'tracking_code', 'courier_status', 'last_courier_sync_at',
    ];

    public function getSourceLabelAttribute()
    {
        return match ($this->source) {
            'messenger' => 'Messenger',
            'instagram' => 'Instagram',
            'whatsapp' => 'WhatsApp',
            'direct_call', 'call' => 'Direct Call',
            'other' => 'Other',
            default => 'Website',
        };
    }

    public function getSourceIconAttribute()
    {
        return match ($this->source) {
            'messenger' => 'fab fa-facebook-messenger text-primary',
            'instagram' => 'fab fa-instagram text-danger',
            'whatsapp' => 'fab fa-whatsapp text-success',
            'direct_call', 'call' => 'fas fa-phone-alt text-info',
            'other' => 'fas fa-store text-dark',
            default => 'fas fa-globe text-secondary',
        };
    }

    public function getSourceBadgeAttribute()
    {
        $icon = $this->source_icon;
        $label = $this->source_label;
        return "<span class=\"badge bg-light text-dark border font-monospace text-xs\"><i class=\"{$icon} me-1\"></i>{$label}</span>";
    }

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'courier_settled_at' => 'datetime',
        'last_courier_sync_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function (Order $order) {
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;
                $order->adjustStockForStatusChange($oldStatus, $newStatus);
            }
        });
    }

    /**
     * Adjust product/variant stock when order status changes between active and restocked (cancelled/returned).
     */
    public function adjustStockForStatusChange(?string $oldStatus, ?string $newStatus): void
    {
        if (!$oldStatus || !$newStatus) {
            return;
        }

        $restockedStatuses = ['cancelled', 'returned'];
        $wasRestocked = in_array($oldStatus, $restockedStatuses);
        $isRestocked = in_array($newStatus, $restockedStatuses);

        if ($wasRestocked === $isRestocked) {
            return;
        }

        $this->loadMissing(['items.product', 'items.variant']);

        if ($isRestocked && !$wasRestocked) {
            // Order was cancelled or returned -> RESTOCK inventory (increment stock)
            foreach ($this->items as $item) {
                if ($item->variant_id && $item->variant) {
                    $item->variant->increment('quantity', $item->quantity);
                }
                if ($item->product_id && $item->product) {
                    $item->product->increment('quantity', $item->quantity);
                }
                StockHistory::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type' => $newStatus === 'cancelled' ? 'cancel' : 'return',
                    'quantity' => $item->quantity,
                    'notes' => 'অর্ডার #' . $this->order_number . ' (' . ucfirst($newStatus) . ') স্টক পুনর্বহাল',
                ]);
            }
        } elseif (!$isRestocked && $wasRestocked) {
            // Order was reactivated from cancelled/returned -> DEDUCT inventory (decrement stock)
            foreach ($this->items as $item) {
                if ($item->variant_id && $item->variant) {
                    $item->variant->decrement('quantity', $item->quantity);
                }
                if ($item->product_id && $item->product) {
                    $item->product->decrement('quantity', $item->quantity);
                }
                StockHistory::create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'type' => 'sale',
                    'quantity' => -$item->quantity,
                    'notes' => 'অর্ডার #' . $this->order_number . ' পুনরায় সক্রিয় করার জন্য স্টক হ্রাস',
                ]);
            }
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courierSettledBy()
    {
        return $this->belongsTo(User::class, 'courier_settled_by_id');
    }

    public function courierLogs()
    {
        return $this->hasMany(CourierLog::class)->latest();
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Helpers
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return $prefix . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function validateForCourier(): array
    {
        $errors = [];
        if (empty(trim($this->name))) $errors[] = "Customer Name is missing";
        if (empty(trim($this->phone)) || !preg_match('/^01[3-9]\d{8}$/', trim($this->phone))) $errors[] = "Customer Phone must be a valid 11-digit number";
        if (empty(trim($this->address))) $errors[] = "Full Address is missing";
        if (empty(trim($this->district))) $errors[] = "District is missing";
        if (empty(trim($this->division))) $errors[] = "Thana/Area is missing";
        if ($this->items->isEmpty()) $errors[] = "Order has no items";
        if ($this->total <= 0) $errors[] = "Total Amount must be greater than 0";

        return $errors;
    }

    public function getCodAmountAttribute()
    {
        if ($this->payment_status === 'paid') {
            return 0;
        }
        return (float) $this->total;
    }

    public function getTrackingUrlAttribute()
    {
        if ($this->tracking_code || $this->tracking_number) {
            $code = $this->tracking_code ?? $this->tracking_number;
            return "https://steadfast.com.bd/t/" . $code;
        }
        return null;
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'confirmed' => '<span class="badge bg-info">Confirmed</span>',
            'processing' => '<span class="badge bg-primary">Processing</span>',
            'shipped' => '<span class="badge bg-secondary">Shipped</span>',
            'delivered' => '<span class="badge bg-success">Delivered</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'returned' => '<span class="badge bg-dark">Returned</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getPaymentStatusBadgeAttribute()
    {
        return match ($this->payment_status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'paid' => '<span class="badge bg-success">Paid</span>',
            'partially_paid' => '<span class="badge bg-info">Partially Paid</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
            'refunded' => '<span class="badge bg-dark">Refunded</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->payment_status) . '</span>',
        };
    }

    public function getCourierSettlementBadgeAttribute()
    {
        return match ($this->courier_settlement_status) {
            'settled' => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Settled</span>',
            default => '<span class="badge bg-secondary">Unsettled</span>',
        };
    }

    public function getCourierStatusBadgeAttribute()
    {
        $status = strtolower($this->courier_status ?? 'unassigned');
        return match ($status) {
            'pending', 'pending_pickup' => '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending Pickup</span>',
            'picked_up', 'in_transit' => '<span class="badge bg-info"><i class="fas fa-shipping-fast me-1"></i>In Transit</span>',
            'hub_received' => '<span class="badge bg-primary"><i class="fas fa-warehouse me-1"></i>Hub Received</span>',
            'out_for_delivery' => '<span class="badge bg-primary"><i class="fas fa-motorcycle me-1"></i>Out for Delivery</span>',
            'delivered' => '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Delivered</span>',
            'partial_delivery' => '<span class="badge bg-warning"><i class="fas fa-adjust me-1"></i>Partial Delivery</span>',
            'cancelled' => '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Cancelled</span>',
            'returned' => '<span class="badge bg-dark"><i class="fas fa-undo me-1"></i>Returned</span>',
            'courier_assigned', 'sent_to_courier' => '<span class="badge bg-info"><i class="fas fa-paper-plane me-1"></i>Assigned</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($status) . '</span>',
        };
    }
}
