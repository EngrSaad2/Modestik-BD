<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->whereNotNull('payment_method');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('bkash_trx_id', 'like', "%{$search}%")
                  ->orWhere('bkash_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->input('status'));
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->input('method'));
        }

        $payments = $query->latest()->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }
}
