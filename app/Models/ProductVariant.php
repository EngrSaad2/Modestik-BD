<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'sku', 'image', 'price', 'sale_price', 'cost', 'quantity', 'status'];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(ProductVariantValue::class, 'variant_id');
    }

    public function getDisplayNameAttribute()
    {
        return $this->values->map(fn($v) => $v->attributeValue->value)->join(' / ');
    }

    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price ?? $this->product->current_price;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(storage_path('app/public/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}
