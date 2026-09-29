<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCartLog extends Model
{
    protected $fillable = [
        'cart_id',
        'user_id',
        'action',
        'description',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
