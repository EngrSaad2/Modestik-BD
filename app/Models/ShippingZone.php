<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = ['name', 'charge', 'min_days', 'max_days', 'free_shipping_min', 'status'];

    protected $casts = [
        'charge' => 'decimal:2',
        'free_shipping_min' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
