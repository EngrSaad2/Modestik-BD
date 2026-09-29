<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'slug', 'content', 'meta_title', 'meta_description', 'featured_image', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function scopeActive($query) { return $query->where('status', true); }
}
