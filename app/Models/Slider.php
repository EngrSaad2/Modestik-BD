<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = ['title', 'subtitle', 'description', 'image', 'button_text', 'button_url', 'order', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function scopeActive($query) { return $query->where('status', true)->orderBy('order'); }
    public function getImageUrlAttribute() { return asset('storage/' . $this->image); }
}
