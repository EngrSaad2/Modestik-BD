<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierLog extends Model
{
    protected $fillable = [
        'order_id',
        'courier_name',
        'action',
        'request_payload',
        'response_payload',
        'status_code',
        'error_message',
        'created_by',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
