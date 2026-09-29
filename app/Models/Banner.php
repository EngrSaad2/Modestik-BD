<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title', 'image', 'url', 'position', 'order', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function scopeActive($query) { return $query->where('status', true)->orderBy('order'); }
    public function scopePosition($query, $position) { return $query->where('position', $position); }
    public function getImageUrlAttribute() { return asset('storage/' . $this->image); }
}
