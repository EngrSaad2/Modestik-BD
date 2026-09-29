<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'location', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function items() { return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('order')->with('children'); }
    public function allItems() { return $this->hasMany(MenuItem::class)->orderBy('order'); }
}
