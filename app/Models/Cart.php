<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_id',
        'customer_name',
        'phone',
        'email',
        'address',
        'division',
        'district',
        'thana',
        'stage',
        'crm_status',
        'assigned_staff_id',
        'recovered_order_id',
        'recovery_token',
        'ip_address',
        'user_agent',
        'traffic_source',
        'last_activity_at',
        'abandoned_at',
        'recovered_at',
        'recovered_by_id',
        'notes',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'recovered_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cart) {
            if (empty($cart->recovery_token)) {
                $cart->recovery_token = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function recoveredOrder()
    {
        return $this->belongsTo(Order::class, 'recovered_order_id');
    }

    public function recoveredBy()
    {
        return $this->belongsTo(User::class, 'recovered_by_id');
    }

    public function logs()
    {
        return $this->hasMany(AbandonedCartLog::class, 'cart_id')->latest();
    }

    public function getSubtotalAttribute()
    {
        return $this->items->sum(fn($item) => $item->price * $item->quantity);
    }

    public function getDiscountAttribute()
    {
        if ($this->coupon) {
            return $this->coupon->calculateDiscount($this->subtotal);
        }
        return 0;
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getTotalValueAttribute()
    {
        return max(0, $this->subtotal - $this->discount);
    }

    public function getCustomerDisplayNameAttribute()
    {
        if (!empty($this->customer_name)) {
            return $this->customer_name;
        }
        if ($this->user) {
            return $this->user->name;
        }
        return 'Guest Visitor';
    }

    public function getCustomerDisplayPhoneAttribute()
    {
        if (!empty($this->phone)) {
            return $this->phone;
        }
        if ($this->user && !empty($this->user->phone)) {
            return $this->user->phone;
        }
        return 'N/A';
    }

    public function getStageLabelAttribute()
    {
        return match ($this->stage) {
            'cart_created', 'cart_viewed', 'cart_view' => 'Cart Page',
            'checkout_started' => 'Checkout Started',
            'shipping_info' => 'Shipping Info',
            'payment_started' => 'Payment Method',
            'abandoned' => $this->inferDropOffStageLabel(),
            'recovered' => 'Recovered',
            'lost' => 'Lost Lead',
            default => ucfirst(str_replace('_', ' ', $this->stage ?? 'Cart Page')),
        };
    }

    public function inferDropOffStageLabel()
    {
        if (!empty($this->address) || (!empty($this->phone) && !empty($this->customer_name))) {
            return 'Shipping Info';
        }
        if (!empty($this->phone) || !empty($this->email)) {
            return 'Checkout Started';
        }
        return 'Cart Page';
    }

    public function getStageBadgeClassAttribute()
    {
        $effectiveStage = $this->stage === 'abandoned' 
            ? strtolower(str_replace(' ', '_', $this->inferDropOffStageLabel())) 
            : $this->stage;

        return match ($effectiveStage) {
            'cart_created', 'cart_viewed', 'cart_view', 'cart_page' => 'bg-secondary bg-opacity-20 text-dark border border-secondary',
            'checkout_started' => 'bg-info bg-opacity-20 text-info border border-info',
            'shipping_info' => 'bg-warning bg-opacity-20 text-dark border border-warning',
            'payment_started' => 'bg-primary bg-opacity-20 text-primary border border-primary',
            'abandoned' => 'bg-danger bg-opacity-20 text-danger border border-danger',
            'recovered' => 'bg-success bg-opacity-20 text-success border border-success',
            'lost' => 'bg-secondary bg-opacity-20 text-secondary border border-secondary',
            default => 'bg-light text-dark border border-secondary',
        };
    }

    public function getCrmBadgeClassAttribute()
    {
        return match ($this->crm_status) {
            'new' => 'bg-primary text-white',
            'contacted' => 'bg-info text-white',
            'in_progress' => 'bg-warning text-dark',
            'recovered' => 'bg-success text-white',
            'lost' => 'bg-secondary text-white',
            default => 'bg-light text-dark',
        };
    }

    public function scopeIncomplete($query)
    {
        return $query->where('stage', '!=', 'order_completed')
            ->whereHas('items');
    }

    public function scopeAbandoned($query)
    {
        return $query->whereIn('stage', ['abandoned', 'shipping_info', 'checkout_started', 'payment_started', 'cart_viewed'])
            ->whereHas('items');
    }

    public function scopeRecovered($query)
    {
        return $query->where('stage', 'recovered')
            ->orWhere('crm_status', 'recovered');
    }
}
