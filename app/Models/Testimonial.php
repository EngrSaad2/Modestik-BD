<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'designation', 'image', 'content', 'rating', 'status', 'order'];
    protected $casts = ['status' => 'boolean'];

    public function scopeActive($query) { return $query->where('status', true)->orderBy('order'); }
    public function getImageUrlAttribute() { return $this->image ? asset('storage/' . $this->image) : null; }
}
