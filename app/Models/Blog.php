<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'image', 'excerpt', 'content', 'category_id',
        'author_id', 'meta_title', 'meta_description', 'views', 'status', 'published_at',
    ];

    protected $casts = ['status' => 'boolean', 'published_at' => 'datetime'];

    public function category() { return $this->belongsTo(BlogCategory::class, 'category_id'); }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }

    public function scopePublished($query) {
        return $query->where('status', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function getImageUrlAttribute() { return $this->image ? asset('storage/' . $this->image) : null; }
}
