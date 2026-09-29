<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['question', 'answer', 'category', 'order', 'status'];
    protected $casts = ['status' => 'boolean'];
    public function scopeActive($query) { return $query->where('status', true)->orderBy('order'); }
}
