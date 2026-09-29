<?php

namespace App\Services\Accounting;

use App\Models\Account;
use App\Models\AccountingTransaction;
use App\Models\Expense;
use App\Models\Investment;
use App\Models\Order;
use App\Models\OwnerWithdrawal;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Salary;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Record a central accounting transaction and adjust account balance if applicable.
     */
    public function recordTransaction(array $data): AccountingTransaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = AccountingTransaction::create([
                'date' => $data['date'] ?? now()->toDateString(),
                'transaction_type' => $data['transaction_type'],
                'reference_number' => $data['reference_number'] ?? null,
                'description' => $data['description'],
                'money_in' => $data['money_in'] ?? 0,
                'money_out' => $data['money_out'] ?? 0,
                'account_id' => $data['account_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'product_id' => $data['product_id'] ?? null,
                'purchase_id' => $data['purchase_id'] ?? null,
                'expense_id' => $data['expense_id'] ?? null,
                'investment_id' => $data['investment_id'] ?? null,
                'salary_id' => $data['salary_id'] ?? null,
                'withdrawal_id' => $data['withdrawal_id'] ?? null,
                'admin_id' => $data['admin_id'] ?? auth()->id(),
            ]);

            if (!empty($data['account_id'])) {
                $account = Account::find($data['account_id']);
                if ($account) {
                    $netChange = ($data['money_in'] ?? 0) - ($data['money_out'] ?? 0);
                    $account->increment('current_balance', $netChange);
                }
            }

            return $transaction;
        });
    }

    /**
     * Reverse a transaction.
     */
    public function reverseTransaction(AccountingTransaction $originalTx, string $reason): AccountingTransaction
    {
        return DB::transaction(function () use ($originalTx, $reason) {
            if ($originalTx->is_reversed) {
                throw new \Exception("Transaction is already reversed.");
            }

            $reversal = AccountingTransaction::create([
                'date' => now()->toDateString(),
                'transaction_type' => 'reversal',
                'reference_number' => 'REV-' . ($originalTx->reference_number ?? $originalTx->id),
                'description' => "Reversal of TX #{$originalTx->id}: " . $reason,
                'money_in' => $originalTx->money_out, // swap in and out
                'money_out' => $originalTx->money_in,
                'account_id' => $originalTx->account_id,
                'order_id' => $originalTx->order_id,
                'product_id' => $originalTx->product_id,
                'purchase_id' => $originalTx->purchase_id,
                'expense_id' => $originalTx->expense_id,
                'investment_id' => $originalTx->investment_id,
                'salary_id' => $originalTx->salary_id,
                'withdrawal_id' => $originalTx->withdrawal_id,
                'admin_id' => auth()->id(),
            ]);

            $originalTx->update([
                'is_reversed' => true,
                'reversed_transaction_id' => $reversal->id,
            ]);

            if ($originalTx->account_id) {
                $account = Account::find($originalTx->account_id);
                if ($account) {
                    // Reverse balance effect
                    $netOriginal = $originalTx->money_in - $originalTx->money_out;
                    $account->decrement('current_balance', $netOriginal);
                }
            }

            return $reversal;
        });
    }

    /**
     * Record order payment.
     */
    public function recordOrderPayment(Order $order, float $amount, ?string $paymentMethod = null, ?int $accountId = null): ?AccountingTransaction
    {
        if ($amount <= 0) return null;

        // Default account based on payment method if not explicitly passed
        if (!$accountId) {
            $method = strtolower($paymentMethod ?? $order->payment_method);
            if (str_contains($method, 'bkash')) {
                $accountId = Account::where('type', 'bkash')->value('id');
            } elseif (str_contains($method, 'nagad')) {
                $accountId = Account::where('type', 'nagad')->value('id');
            } elseif (str_contains($method, 'bank')) {
                $accountId = Account::where('type', 'bank')->value('id');
            } else {
                $accountId = Account::where('type', 'cash')->value('id');
            }
        }

        return $this->recordTransaction([
            'date' => now()->toDateString(),
            'transaction_type' => 'order_payment',
            'reference_number' => $order->order_number,
            'description' => "Order Payment Received: #{$order->order_number} ({$order->name})",
            'money_in' => $amount,
            'money_out' => 0,
            'account_id' => $accountId,
            'order_id' => $order->id,
        ]);
    }

    /**
     * Record courier settlement.
     */
    public function recordCourierSettlement(Order $order, float $codAmount, float $courierCharge, ?int $accountId = null): AccountingTransaction
    {
        $netReceived = max(0, $codAmount - $courierCharge);

        if (!$accountId) {
            $accountId = Account::where('type', 'cash')->value('id') ?? Account::first()?->id;
        }

        return $this->recordTransaction([
            'date' => now()->toDateString(),
            'transaction_type' => 'courier_settlement',
            'reference_number' => "SETTLE-{$order->order_number}",
            'description' => "Courier Settlement: Order #{$order->order_number} (COD: ৳{$codAmount}, Charge: ৳{$courierCharge}, Net: ৳{$netReceived})",
            'money_in' => $netReceived,
            'money_out' => 0,
            'account_id' => $accountId,
            'order_id' => $order->id,
        ]);
    }

    /**
     * Record an Expense.
     */
    public function recordExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $expense = Expense::create([
                'category' => $data['category'],
                'amount' => $data['amount'],
                'date' => $data['date'] ?? now()->toDateString(),
                'description' => $data['description'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'account_id' => $data['account_id'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->recordTransaction([
                'date' => $expense->date->toDateString(),
                'transaction_type' => 'expense',
                'reference_number' => "EXP-{$expense->id}",
                'description' => "Expense: " . ucfirst(str_replace('_', ' ', $expense->category)) . ($expense->description ? " - {$expense->description}" : ''),
                'money_in' => 0,
                'money_out' => $expense->amount,
                'account_id' => $expense->account_id,
                'expense_id' => $expense->id,
            ]);

            return $expense;
        });
    }

    /**
     * Record a Supplier Purchase and increase inventory stock.
     */
    public function recordPurchase(array $data, array $items): Purchase
    {
        return DB::transaction(function () use ($data, $items) {
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += ($item['unit_cost'] * $item['quantity']);
            }

            $paidAmount = $data['paid_amount'] ?? 0;
            $dueAmount = max(0, $totalAmount - $paidAmount);
            $paymentStatus = $paidAmount >= $totalAmount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');

            $invoiceNumber = 'PUR-' . date('Ymd') . '-' . rand(1000, 9999);

            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'] ?? null,
                'invoice_number' => $invoiceNumber,
                'purchase_date' => $data['purchase_date'] ?? now()->toDateString(),
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'account_id' => $data['account_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'unit_cost' => $item['unit_cost'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['unit_cost'] * $item['quantity'],
                ]);

                // Increment stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    if (!empty($item['variant_id'])) {
                        $variant = ProductVariant::find($item['variant_id']);
                        if ($variant) {
                            $variant->increment('quantity', $item['quantity']);
                            $variant->update(['cost' => $item['unit_cost']]);
                        }
                    }
                    $product->increment('quantity', $item['quantity']);
                    $product->update(['cost' => $item['unit_cost']]);

                    // Stock History
                    StockHistory::create([
                        'product_id' => $product->id,
                        'variant_id' => $item['variant_id'] ?? null,
                        'type' => 'restock',
                        'quantity' => $item['quantity'],
                        'cost' => $item['unit_cost'],
                        'notes' => "Supplier Purchase Invoice #{$purchase->invoice_number}",
                    ]);
                }
            }

            // Record transaction for paid portion
            if ($paidAmount > 0) {
                $this->recordTransaction([
                    'date' => $purchase->purchase_date->toDateString(),
                    'transaction_type' => 'product_purchase',
                    'reference_number' => $purchase->invoice_number,
                    'description' => "Product Purchase: Invoice #{$purchase->invoice_number}",
                    'money_in' => 0,
                    'money_out' => $paidAmount,
                    'account_id' => $purchase->account_id,
                    'purchase_id' => $purchase->id,
                ]);
            }

            return $purchase;
        });
    }

    /**
     * Record Investment into business.
     */
    public function recordInvestment(array $data): Investment
    {
        return DB::transaction(function () use ($data) {
            $investment = Investment::create([
                'source' => $data['source'],
                'amount' => $data['amount'],
                'date' => $data['date'] ?? now()->toDateString(),
                'description' => $data['description'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'account_id' => $data['account_id'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->recordTransaction([
                'date' => $investment->date->toDateString(),
                'transaction_type' => 'investment',
                'reference_number' => "INV-{$investment->id}",
                'description' => "Investment Added: " . ucfirst(str_replace('_', ' ', $investment->source)),
                'money_in' => $investment->amount,
                'money_out' => 0,
                'account_id' => $investment->account_id,
                'investment_id' => $investment->id,
            ]);

            return $investment;
        });
    }

    /**
     * Record Salary Payment.
     */
    public function recordSalary(array $data): Salary
    {
        return DB::transaction(function () use ($data) {
            $paidAmount = $data['paid_amount'] ?? $data['salary_amount'];
            $dueAmount = max(0, $data['salary_amount'] - $paidAmount);
            $status = $paidAmount >= $data['salary_amount'] ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');

            $salary = Salary::create([
                'person_type' => $data['person_type'] ?? 'employee',
                'person_name' => $data['person_name'],
                'user_id' => $data['user_id'] ?? null,
                'salary_month' => $data['salary_month'],
                'salary_amount' => $data['salary_amount'],
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'status' => $status,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'account_id' => $data['account_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            if ($paidAmount > 0) {
                $category = $salary->person_type === 'owner' ? 'owner_salary' : 'employee_salary';
                
                // Record internal expense
                $expense = Expense::create([
                    'category' => $category,
                    'amount' => $paidAmount,
                    'date' => $salary->payment_date ? $salary->payment_date->toDateString() : now()->toDateString(),
                    'description' => "Salary Payment ({$salary->person_name} - {$salary->salary_month})",
                    'payment_method' => $salary->payment_method,
                    'account_id' => $salary->account_id,
                    'notes' => $salary->notes,
                    'created_by' => auth()->id(),
                ]);

                $this->recordTransaction([
                    'date' => $salary->payment_date ? $salary->payment_date->toDateString() : now()->toDateString(),
                    'transaction_type' => 'salary',
                    'reference_number' => "SAL-{$salary->id}",
                    'description' => "Salary Paid: {$salary->person_name} ({$salary->salary_month})",
                    'money_in' => 0,
                    'money_out' => $paidAmount,
                    'account_id' => $salary->account_id,
                    'salary_id' => $salary->id,
                    'expense_id' => $expense->id,
                ]);
            }

            return $salary;
        });
    }

    /**
     * Record Owner Withdrawal.
     */
    public function recordOwnerWithdrawal(array $data): OwnerWithdrawal
    {
        return DB::transaction(function () use ($data) {
            $withdrawal = OwnerWithdrawal::create([
                'amount' => $data['amount'],
                'date' => $data['date'] ?? now()->toDateString(),
                'reason' => $data['reason'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'account_id' => $data['account_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $this->recordTransaction([
                'date' => $withdrawal->date->toDateString(),
                'transaction_type' => 'owner_withdrawal',
                'reference_number' => "DRAW-{$withdrawal->id}",
                'description' => "Owner Withdrawal" . ($withdrawal->reason ? ": {$withdrawal->reason}" : ''),
                'money_in' => 0,
                'money_out' => $withdrawal->amount,
                'account_id' => $withdrawal->account_id,
                'withdrawal_id' => $withdrawal->id,
            ]);

            return $withdrawal;
        });
    }

    /**
     * Comprehensive Financial Position Summary.
     */
    public function getFinancialSummary(?string $startDate = null, ?string $endDate = null): array
    {
        // Sales Query
        $salesQuery = Order::whereNotIn('status', ['cancelled']);
        if ($startDate && $endDate) {
            $salesQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        $totalSales = (float) $salesQuery->sum('total');

        // Product Cost (COGS) for recognized / non-cancelled orders
        $orderIds = (clone $salesQuery)->pluck('id');
        
        $productCost = (float) DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->selectRaw('SUM(COALESCE(cost, 0) * quantity) as total_cost')
            ->value('total_cost');

        // Fallback for order items if cost wasn't snapshotted: calculate from products/variants
        if ($productCost == 0 && count($orderIds) > 0) {
            $items = DB::table('order_items')->whereIn('order_id', $orderIds)->get();
            foreach ($items as $item) {
                if ($item->variant_id) {
                    $vCost = DB::table('product_variants')->where('id', $item->variant_id)->value('cost');
                    $pCost = $vCost ?: DB::table('products')->where('id', $item->product_id)->value('cost');
                    $productCost += (($pCost ?? 0) * $item->quantity);
                } else {
                    $pCost = DB::table('products')->where('id', $item->product_id)->value('cost');
                    $productCost += (($pCost ?? 0) * $item->quantity);
                }
            }
        }

        // Money Received from transactions
        $moneyInQuery = AccountingTransaction::where('is_reversed', false);
        if ($startDate && $endDate) {
            $moneyInQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $totalMoneyReceived = (float) (clone $moneyInQuery)->sum('money_in');

        // Customer Due
        $customerDueQuery = Order::whereNotIn('status', ['cancelled'])
            ->whereIn('payment_status', ['pending', 'partially_paid']);
        if ($startDate && $endDate) {
            $customerDueQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        $customerDue = (float) $customerDueQuery->sum('total'); // Simplified calculation

        // Expenses
        $expenseQuery = Expense::query();
        if ($startDate && $endDate) {
            $expenseQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $otherExpenses = (float) $expenseQuery->sum('amount');

        // Gross Profit & Net Profit
        $grossProfit = $totalSales - $productCost;
        $netProfit = $grossProfit - $otherExpenses;
        $profitMargin = $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 2) : 0;

        // Stock Valuation
        $simpleStockValue = (float) Product::where('has_variants', false)
            ->selectRaw('SUM(COALESCE(cost, 0) * quantity) as val')
            ->value('val');

        $variantStockValue = (float) ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->selectRaw('SUM(COALESCE(product_variants.cost, products.cost, 0) * product_variants.quantity) as val')
            ->value('val');

        $totalStockValue = $simpleStockValue + $variantStockValue;

        // Cash in Hand across active accounts
        $cashInHand = (float) Account::where('is_active', true)->sum('current_balance');

        return [
            'total_sales' => $totalSales,
            'money_received' => $totalMoneyReceived,
            'customer_due' => $customerDue,
            'product_cost' => $productCost,
            'other_expenses' => $otherExpenses,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'profit_margin' => $profitMargin,
            'cash_in_hand' => $cashInHand,
            'stock_value' => $totalStockValue,
            'total_business_value' => $cashInHand + $totalStockValue,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}
