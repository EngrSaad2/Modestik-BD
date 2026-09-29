<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'product_id', 'order_id', 'rating', 'comment', 'status', 'is_featured', 'images'];

    protected $casts = [
        'images' => 'array',
        'is_featured' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function order() { return $this->belongsTo(Order::class); }

    public function scopeApproved($query) { return $query->where('status', 'approved'); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
}
