<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    protected $fillable = ['type', 'category', 'amount', 'date', 'notes', 'attachment'];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function scopeInvestment($query)
    {
        return $query->where('type', 'investment');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeRevenue($query)
    {
        return $query->where('type', 'revenue');
    }
}
