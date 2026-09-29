<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerWithdrawal extends Model
{
    protected $fillable = [
        'amount',
        'date',
        'reason',
        'payment_method',
        'account_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
