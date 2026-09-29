<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name', 'slug', 'type', 'status', 'order'];

    protected $casts = ['status' => 'boolean'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
