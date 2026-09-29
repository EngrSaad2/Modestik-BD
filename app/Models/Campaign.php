<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'image', 'description', 'discount_type',
        'discount_value', 'starts_at', 'ends_at', 'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function products() { return $this->belongsToMany(Product::class, 'campaign_products'); }

    public function scopeActive($query) {
        return $query->where('status', true)->where('starts_at', '<=', now())->where('ends_at', '>=', now());
    }

    public function getIsActiveAttribute() {
        return $this->status && $this->starts_at <= now() && $this->ends_at >= now();
    }

    public function getImageUrlAttribute() { return $this->image ? asset('storage/' . $this->image) : null; }
}
