<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'person_type',
        'person_name',
        'user_id',
        'salary_month',
        'salary_amount',
        'paid_amount',
        'due_amount',
        'payment_date',
        'status',
        'payment_method',
        'account_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'salary_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
