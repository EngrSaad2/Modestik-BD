<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingTransaction extends Model
{
    protected $fillable = [
        'date',
        'transaction_type',
        'reference_number',
        'description',
        'money_in',
        'money_out',
        'account_id',
        'order_id',
        'product_id',
        'purchase_id',
        'expense_id',
        'investment_id',
        'salary_id',
        'withdrawal_id',
        'admin_id',
        'is_reversed',
        'reversed_transaction_id',
    ];

    protected $casts = [
        'date' => 'date',
        'money_in' => 'decimal:2',
        'money_out' => 'decimal:2',
        'is_reversed' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function withdrawal()
    {
        return $this->belongsTo(OwnerWithdrawal::class, 'withdrawal_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
