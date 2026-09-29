<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialTransaction::latest();

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->paginate(15);

        // Core Financial Metrics Calculations
        $totalInvestment = FinancialTransaction::investment()->sum('amount');
        $totalExpenses = FinancialTransaction::expense()->sum('amount');
        
        // Sales from orders (not cancelled)
        $totalSales = Order::whereNotIn('status', ['cancelled', 'returned'])->sum('total');

        // Inventory Value: sum of cost * qty
        $simpleInventoryValue = Product::where('has_variants', false)
            ->selectRaw('SUM(cost * quantity) as total')
            ->value('total') ?? 0;

        $variantInventoryValue = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->selectRaw('SUM(COALESCE(products.cost, 0) * product_variants.quantity) as total')
            ->value('total') ?? 0;

        $inventoryValue = $simpleInventoryValue + $variantInventoryValue;

        $cashInHand = $totalInvestment + $totalSales - $totalExpenses;
        $netProfitLoss = $totalSales - $totalExpenses;
        $workingCapital = $cashInHand;
        $businessWorth = $cashInHand + $inventoryValue;

        return view('admin.financial.index', compact(
            'transactions',
            'totalInvestment',
            'totalExpenses',
            'totalSales',
            'inventoryValue',
            'cashInHand',
            'netProfitLoss',
            'workingCapital',
            'businessWorth'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:investment,expense,revenue',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpeg,png,webp,zip|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        FinancialTransaction::create([
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'date' => $request->date,
            'notes' => $request->notes,
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('admin.financial.index')->with('success', 'Financial transaction recorded successfully.');
    }
}
