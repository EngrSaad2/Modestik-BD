<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = StockHistory::with('product', 'variant')->latest();

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $logs = $query->paginate(15);
        $products = Product::with('variants')->orderBy('name')->get();

        return view('admin.inventory.index', compact('logs', 'products'));
    }

    public function restock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $quantity = $request->quantity;
        $cost = $request->cost;

        $product = Product::findOrFail($productId);

        DB::beginTransaction();
        try {
            if ($variantId) {
                $variant = ProductVariant::findOrFail($variantId);
                $variant->increment('quantity', $quantity);
                $product->increment('quantity', $quantity); // Keep main product count in sync
            } else {
                $product->increment('quantity', $quantity);
            }

            // Update main product cost if cost is provided (average costing fallback)
            if ($cost > 0) {
                $product->update(['cost' => $cost]);
            }

            // Create Stock History Log
            StockHistory::create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'type' => 'restock',
                'quantity' => $quantity,
                'cost' => $cost,
                'notes' => $request->notes ?? 'ম্যানুয়াল রিস্টক',
            ]);

            // Track restock purchase as financial expense
            if ($cost > 0) {
                \App\Models\FinancialTransaction::create([
                    'type' => 'expense',
                    'category' => 'product_purchase_cost',
                    'amount' => $cost * $quantity,
                    'date' => now()->toDateString(),
                    'notes' => 'পণ্য রিস্টক: ' . $product->name . ($variantId ? ' (' . $variant->display_name . ')' : '') . ' (' . $quantity . ' টি)',
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Restock failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.inventory.index')->with('success', 'Product restocked and logged successfully.');
    }

    public function valuation()
    {
        // Calculate current stock levels and value
        $simpleProducts = Product::where('has_variants', false)->get();
        $variantProducts = ProductVariant::with('product')->get();

        $totalQty = $simpleProducts->sum('quantity') + $variantProducts->sum('quantity');
        
        $totalValue = $simpleProducts->sum(fn($p) => ($p->cost ?? 0) * $p->quantity) +
                     $variantProducts->sum(fn($v) => ($v->product->cost ?? 0) * $v->quantity);

        // Get unsold items listing
        $unsoldSimple = Product::where('has_variants', false)->where('quantity', '>', 0)->get();
        $unsoldVariants = ProductVariant::with('product')->where('quantity', '>', 0)->get();

        return view('admin.inventory.valuation', compact('totalQty', 'totalValue', 'unsoldSimple', 'unsoldVariants'));
    }
}
