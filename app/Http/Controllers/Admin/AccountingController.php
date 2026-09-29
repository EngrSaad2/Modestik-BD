<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountingTransaction;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Investment;
use App\Models\Order;
use App\Models\OwnerWithdrawal;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\Salary;
use App\Models\Supplier;
use App\Services\Accounting\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    protected AccountingService $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
        $this->ensureDefaultAccountsExist();
    }

    private function ensureDefaultAccountsExist(): void
    {
        if (Account::count() === 0) {
            Account::create(['name' => 'Cash in Hand', 'type' => 'cash', 'opening_balance' => 0, 'current_balance' => 0]);
            Account::create(['name' => 'bKash Merchant / Personal', 'type' => 'bkash', 'opening_balance' => 0, 'current_balance' => 0]);
            Account::create(['name' => 'Nagad Account', 'type' => 'nagad', 'opening_balance' => 0, 'current_balance' => 0]);
            Account::create(['name' => 'Bank Account', 'type' => 'bank', 'opening_balance' => 0, 'current_balance' => 0]);
        }
    }

    /**
     * Accounting Dashboard (হিসাব-নিকাশ ড্যাশবোর্ড)
     */
    public function dashboard(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $summary = $this->accountingService->getFinancialSummary($startDate, $endDate);

        // Daily Sales & Expense Chart Data
        $chartDates = [];
        $salesData = [];
        $expenseData = [];

        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $dt) {
            $dateStr = $dt->format('Y-m-d');
            $chartDates[] = $dt->format('d M');

            $daySales = Order::whereNotIn('status', ['cancelled'])
                ->whereDate('created_at', $dateStr)
                ->sum('total');

            $dayExpenses = Expense::whereDate('date', $dateStr)->sum('amount');

            $salesData[] = (float) $daySales;
            $expenseData[] = (float) $dayExpenses;
        }

        // Recent Central Ledger Transactions
        $recentTransactions = AccountingTransaction::with(['account', 'order', 'purchase', 'expense'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.accounting.dashboard', compact(
            'summary',
            'startDate',
            'endDate',
            'chartDates',
            'salesData',
            'expenseData',
            'recentTransactions'
        ));
    }

    /**
     * Money In (টাকা এসেছে)
     */
    public function moneyIn(Request $request)
    {
        $query = AccountingTransaction::with(['account', 'order'])
            ->where('money_in', '>', 0)
            ->where('is_reversed', false)
            ->latest();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('payment_method')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('payment_method', $request->payment_method);
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        $totalIncome = (float) (clone $query)->sum('money_in');

        return view('admin.accounting.money-in', compact('transactions', 'totalIncome'));
    }

    /**
     * Money Out (টাকা খরচ)
     */
    public function moneyOut(Request $request)
    {
        $query = Expense::with(['account', 'creator'])->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->paginate(20)->withQueryString();
        $totalExpenses = (float) (clone $query)->sum('amount');
        $accounts = Account::where('is_active', true)->get();

        return view('admin.accounting.money-out', compact('expenses', 'totalExpenses', 'accounts'));
    }

    /**
     * Store Expense manually.
     */
    public function storeExpense(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'notes' => 'nullable|string',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('expenses', 'public');
        }

        $data = $request->all();
        $data['attachment'] = $attachmentPath;

        $this->accountingService->recordExpense($data);

        return back()->with('success', 'New expense added successfully.');
    }

    /**
     * Sales & Profit (বিক্রি ও লাভ)
     */
    public function salesProfit(Request $request)
    {
        $range = $request->get('range', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($range === 'daily') {
            $startDate = now()->toDateString();
            $endDate = now()->toDateString();
        } elseif ($range === 'weekly') {
            $startDate = now()->startOfWeek()->toDateString();
            $endDate = now()->endOfWeek()->toDateString();
        } elseif ($range === 'monthly') {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        } elseif ($range === 'yearly') {
            $startDate = now()->startOfYear()->toDateString();
            $endDate = now()->endOfYear()->toDateString();
        } elseif (!$startDate || !$endDate) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        $summary = $this->accountingService->getFinancialSummary($startDate, $endDate);

        // Daily chart data for selected range
        $labels = [];
        $salesArr = [];
        $cogsArr = [];
        $grossProfitArr = [];
        $expenseArr = [];
        $netProfitArr = [];

        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1D'),
            (new \DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $dt) {
            $dStr = $dt->format('Y-m-d');
            $labels[] = $dt->format('d M');

            $dOrders = Order::whereNotIn('status', ['cancelled'])->whereDate('created_at', $dStr)->get();
            $dSales = (float) $dOrders->sum('total');

            $dCogs = 0;
            $dOrderIds = $dOrders->pluck('id');
            if (count($dOrderIds) > 0) {
                $dCogs = (float) DB::table('order_items')
                    ->whereIn('order_id', $dOrderIds)
                    ->selectRaw('SUM(COALESCE(cost, 0) * quantity) as total')
                    ->value('total');
            }

            $dExp = (float) Expense::whereDate('date', $dStr)->sum('amount');
            $dGross = $dSales - $dCogs;
            $dNet = $dGross - $dExp;

            $salesArr[] = $dSales;
            $cogsArr[] = $dCogs;
            $grossProfitArr[] = $dGross;
            $expenseArr[] = $dExp;
            $netProfitArr[] = $dNet;
        }

        return view('admin.accounting.sales-profit', compact(
            'summary',
            'range',
            'startDate',
            'endDate',
            'labels',
            'salesArr',
            'cogsArr',
            'grossProfitArr',
            'expenseArr',
            'netProfitArr'
        ));
    }

    /**
     * Supplier Purchases (পণ্য কেনা)
     */
    public function purchases(Request $request)
    {
        $query = Purchase::with(['supplier', 'account', 'items.product'])->latest();

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $purchases = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();
        $accounts = Account::where('is_active', true)->get();
        $products = Product::with('variants')->active()->orderBy('name')->get();

        return view('admin.accounting.purchases', compact('purchases', 'suppliers', 'accounts', 'products'));
    }

    /**
     * Store Purchase & Auto Restock.
     */
    public function storePurchase(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $this->accountingService->recordPurchase($request->all(), $request->items);

        return back()->with('success', 'Purchase entry saved successfully and stock updated.');
    }

    /**
     * Customer Due (কাস্টমার পাওনা)
     */
    public function customerDue(Request $request)
    {
        $query = Order::with('user')
            ->whereNotIn('status', ['cancelled'])
            ->whereIn('payment_status', ['pending', 'partially_paid'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $totalDue = (float) Order::whereNotIn('status', ['cancelled'])
            ->whereIn('payment_status', ['pending', 'partially_paid'])
            ->sum('total');

        $accounts = Account::where('is_active', true)->get();

        return view('admin.accounting.customer-due', compact('orders', 'totalDue', 'accounts'));
    }

    /**
     * Collect Due Payment for Order.
     */
    public function collectOrderDue(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        $this->accountingService->recordOrderPayment(
            $order,
            $request->amount,
            $request->payment_method,
            $request->account_id
        );

        $order->update(['payment_status' => 'paid']);

        return back()->with('success', 'Due payment collection recorded successfully.');
    }

    /**
     * Supplier Due (সাপ্লায়ার পাওনা)
     */
    public function supplierDue(Request $request)
    {
        $query = Purchase::with(['supplier', 'account'])
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->latest();

        $purchases = $query->paginate(15)->withQueryString();
        $totalSupplierDue = (float) Purchase::whereIn('payment_status', ['unpaid', 'partial'])->sum('due_amount');
        $accounts = Account::where('is_active', true)->get();

        return view('admin.accounting.supplier-due', compact('purchases', 'totalSupplierDue', 'accounts'));
    }

    /**
     * Settle Supplier Due.
     */
    public function paySupplierDue(Request $request, Purchase $purchase)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $purchase->due_amount,
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        $newPaid = $purchase->paid_amount + $request->amount;
        $newDue = max(0, $purchase->total_amount - $newPaid);
        $newStatus = $newDue == 0 ? 'paid' : 'partial';

        $purchase->update([
            'paid_amount' => $newPaid,
            'due_amount' => $newDue,
            'payment_status' => $newStatus,
        ]);

        $this->accountingService->recordTransaction([
            'date' => now()->toDateString(),
            'transaction_type' => 'product_purchase',
            'reference_number' => $purchase->invoice_number,
            'description' => "Supplier due payment: Invoice #{$purchase->invoice_number}",
            'money_in' => 0,
            'money_out' => $request->amount,
            'account_id' => $request->account_id,
            'purchase_id' => $purchase->id,
        ]);

        return back()->with('success', 'Supplier due payment recorded successfully.');
    }

    /**
     * Money Accounts (ক্যাশ ও অ্যাকাউন্টস)
     */
    public function accounts(Request $request)
    {
        $accounts = Account::withCount('transactions')->get();

        $selectedAccountId = $request->get('account_id');
        $txQuery = AccountingTransaction::with(['order', 'purchase', 'expense', 'investment', 'salary', 'withdrawal'])->latest();

        if ($selectedAccountId) {
            $txQuery->where('account_id', $selectedAccountId);
        }

        $transactions = $txQuery->paginate(15)->withQueryString();

        return view('admin.accounting.accounts', compact('accounts', 'transactions', 'selectedAccountId'));
    }

    /**
     * Store new Money Account.
     */
    public function storeAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bkash,nagad,bank,other',
            'account_number' => 'nullable|string|max:100',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        Account::create([
            'name' => $request->name,
            'type' => $request->type,
            'account_number' => $request->account_number,
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
            'is_active' => true,
        ]);

        return back()->with('success', 'New account created successfully.');
    }

    /**
     * Investments (ব্যবসায় টাকা যোগ)
     */
    public function investments(Request $request)
    {
        $investments = Investment::with(['account', 'creator'])->latest()->paginate(15);
        $totalInvested = (float) Investment::sum('amount');
        $accounts = Account::where('is_active', true)->get();

        return view('admin.accounting.investments', compact('investments', 'totalInvested', 'accounts'));
    }

    /**
     * Store Investment.
     */
    public function storeInvestment(Request $request)
    {
        $request->validate([
            'source' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        $this->accountingService->recordInvestment($request->all());

        return back()->with('success', 'Investment recorded successfully.');
    }

    /**
     * Salaries (বেতন ও পারিশ্রমিক)
     */
    public function salaries(Request $request)
    {
        $salaries = Salary::with(['account', 'creator'])->latest()->paginate(15);
        $totalPaidSalaries = (float) Salary::sum('paid_amount');
        $totalDueSalaries = (float) Salary::sum('due_amount');
        $accounts = Account::where('is_active', true)->get();

        return view('admin.accounting.salaries', compact('salaries', 'totalPaidSalaries', 'totalDueSalaries', 'accounts'));
    }

    /**
     * Store Salary.
     */
    public function storeSalary(Request $request)
    {
        $request->validate([
            'person_type' => 'required|in:employee,owner',
            'person_name' => 'required|string|max:255',
            'salary_month' => 'required|string',
            'salary_amount' => 'required|numeric|min:0.01',
            'paid_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id',
            'notes' => 'nullable|string',
        ]);

        $this->accountingService->recordSalary($request->all());

        return back()->with('success', 'Salary entry saved successfully.');
    }

    /**
     * Owner Withdrawals (ব্যবসা থেকে টাকা নেওয়া)
     */
    public function ownerWithdrawals(Request $request)
    {
        $withdrawals = OwnerWithdrawal::with(['account', 'creator'])->latest()->paginate(15);
        $totalWithdrawals = (float) OwnerWithdrawal::sum('amount');
        $accounts = Account::where('is_active', true)->get();

        return view('admin.accounting.owner-withdrawals', compact('withdrawals', 'totalWithdrawals', 'accounts'));
    }

    /**
     * Store Owner Withdrawal.
     */
    public function storeWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'reason' => 'nullable|string',
            'payment_method' => 'required|string',
            'account_id' => 'nullable|exists:accounts,id',
            'notes' => 'nullable|string',
        ]);

        $this->accountingService->recordOwnerWithdrawal($request->all());

        return back()->with('success', 'Owner withdrawal recorded successfully.');
    }

    /**
     * Courier Settlement List
     */
    public function courierSettlements(Request $request)
    {
        $query = Order::with(['courierSettledBy', 'items'])
            ->whereNotNull('courier_settlement_status')
            ->latest();

        $orders = $query->paginate(15)->withQueryString();
        $accounts = Account::where('is_active', true)->get();

        return view('admin.accounting.courier-settlements', compact('orders', 'accounts'));
    }

    /**
     * Profit and Loss Page (লাভ ক্ষতি)
     */
    public function profitLoss(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $summary = $this->accountingService->getFinancialSummary($startDate, $endDate);

        // Expense category breakdown
        $expenseCategories = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('category')
            ->get();

        return view('admin.accounting.profit-loss', compact('summary', 'startDate', 'endDate', 'expenseCategories'));
    }

    /**
     * Inventory Value (স্টকের মূল্য)
     */
    public function inventoryValue(Request $request)
    {
        $simpleProducts = Product::where('has_variants', false)->get();
        $variantProducts = ProductVariant::with('product')->get();

        $totalQty = $simpleProducts->sum('quantity') + $variantProducts->sum('quantity');

        $totalStockCost = (float) ($simpleProducts->sum(fn($p) => ($p->cost ?? 0) * $p->quantity) +
            $variantProducts->sum(fn($v) => ($v->cost ?? $v->product->cost ?? 0) * $v->quantity));

        $lowStockProducts = Product::where('quantity', '<=', 5)->get();
        $outOfStockCount = Product::where('quantity', 0)->count();

        $products = Product::with(['variants', 'category'])->orderBy('name')->paginate(20);

        return view('admin.accounting.inventory-value', compact(
            'totalQty',
            'totalStockCost',
            'lowStockProducts',
            'outOfStockCount',
            'products'
        ));
    }

    /**
     * Monthly Closing (মাসিক হিসাব সমাপনী)
     */
    public function monthlyClosing(Request $request)
    {
        $monthStr = $request->get('month', date('Y-m')); // e.g. 2026-08
        $startDate = $monthStr . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $summary = $this->accountingService->getFinancialSummary($startDate, $endDate);

        $totalPurchases = (float) Purchase::whereBetween('purchase_date', [$startDate, $endDate])->sum('total_amount');
        $totalSalaries = (float) Salary::whereBetween('payment_date', [$startDate, $endDate])->sum('paid_amount');
        $totalInvestments = (float) Investment::whereBetween('date', [$startDate, $endDate])->sum('amount');
        $totalWithdrawals = (float) OwnerWithdrawal::whereBetween('date', [$startDate, $endDate])->sum('amount');
        $totalRefunds = (float) Order::where('payment_status', 'refunded')->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->sum('total');

        return view('admin.accounting.monthly-closing', compact(
            'monthStr',
            'startDate',
            'endDate',
            'summary',
            'totalPurchases',
            'totalSalaries',
            'totalInvestments',
            'totalWithdrawals',
            'totalRefunds'
        ));
    }

    /**
     * Central Transaction Ledger (ট্রানজেকশন লেজার)
     */
    public function transactions(Request $request)
    {
        $query = AccountingTransaction::with(['account', 'order', 'purchase', 'expense', 'investment', 'salary', 'withdrawal', 'admin'])->latest();

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('admin.accounting.transactions', compact('transactions'));
    }

    /**
     * Reverse a transaction.
     */
    public function reverseTransaction(Request $request, AccountingTransaction $transaction)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $this->accountingService->reverseTransaction($transaction, $request->reason);
            return back()->with('success', 'Transaction reversed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Financial Reports Suite (আর্থিক রিপোর্টস)
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('type', 'sales');
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $data = [];
        if ($reportType === 'sales') {
            $data = Order::with('user')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->latest()->get();
        } elseif ($reportType === 'expenses') {
            $data = Expense::with('account')->whereBetween('date', [$startDate, $endDate])->latest()->get();
        } elseif ($reportType === 'purchases') {
            $data = Purchase::with('supplier')->whereBetween('purchase_date', [$startDate, $endDate])->latest()->get();
        } elseif ($reportType === 'salaries') {
            $data = Salary::whereBetween('payment_date', [$startDate, $endDate])->latest()->get();
        } elseif ($reportType === 'investments') {
            $data = Investment::whereBetween('date', [$startDate, $endDate])->latest()->get();
        } elseif ($reportType === 'withdrawals') {
            $data = OwnerWithdrawal::whereBetween('date', [$startDate, $endDate])->latest()->get();
        } else {
            $data = AccountingTransaction::with('account')->whereBetween('date', [$startDate, $endDate])->latest()->get();
        }

        $categories = Category::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();

        return view('admin.accounting.reports', compact('reportType', 'startDate', 'endDate', 'data', 'categories', 'products'));
    }

    /**
     * Export Report to CSV.
     */
    public function exportReport(Request $request, string $type)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $filename = "accounting-report-{$type}-{$startDate}-to-{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            if ($type === 'sales') {
                fputcsv($file, ['Order #', 'Customer', 'Date', 'Total Amount', 'Payment Method', 'Payment Status', 'Status']);
                $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->get();
                foreach ($orders as $o) {
                    fputcsv($file, [$o->order_number, $o->name, $o->created_at->format('Y-m-d'), $o->total, $o->payment_method, $o->payment_status, $o->status]);
                }
            } elseif ($type === 'expenses') {
                fputcsv($file, ['Date', 'Category', 'Description', 'Amount', 'Payment Method']);
                $expenses = Expense::whereBetween('date', [$startDate, $endDate])->get();
                foreach ($expenses as $e) {
                    fputcsv($file, [$e->date->format('Y-m-d'), $e->category, $e->description, $e->amount, $e->payment_method]);
                }
            } else {
                fputcsv($file, ['Date', 'Type', 'Reference', 'Description', 'Money In', 'Money Out']);
                $txs = AccountingTransaction::whereBetween('date', [$startDate, $endDate])->get();
                foreach ($txs as $t) {
                    fputcsv($file, [$t->date->format('Y-m-d'), $t->transaction_type, $t->reference_number, $t->description, $t->money_in, $t->money_out]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
