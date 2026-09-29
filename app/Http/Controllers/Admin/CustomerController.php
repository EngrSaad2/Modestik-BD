<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereHas('roles', function($q) {
            $q->where('name', 'customer');
        })->orWhereDoesntHave('roles')->withCount('orders');
        
        if ($request->has('vip') && $request->vip !== '') {
            $query->where('is_vip', $request->vip == '1');
        }

        $customers = $query->latest()->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load(['orders', 'addresses']);
        return view('admin.customers.show', compact('customer'));
    }

    public function destroy(User $customer)
    {
        if ($customer->hasRole('admin') || $customer->hasRole('super-admin') || $customer->id === 1) {
            return back()->with('error', 'Administrator accounts cannot be deleted.');
        }

        \App\Models\Address::where('user_id', $customer->id)->delete();
        \App\Models\Cart::where('user_id', $customer->id)->delete();
        \App\Models\Wishlist::where('user_id', $customer->id)->delete();
        $name = $customer->name;
        $customer->delete();

        return back()->with('success', "Customer '{$name}' deleted successfully.");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $deletedCount = 0;
        $customers = User::whereIn('id', $request->ids)->get();

        foreach ($customers as $customer) {
            if ($customer->hasRole('admin') || $customer->hasRole('super-admin') || $customer->id === 1) {
                continue;
            }

            \App\Models\Address::where('user_id', $customer->id)->delete();
            \App\Models\Cart::where('user_id', $customer->id)->delete();
            \App\Models\Wishlist::where('user_id', $customer->id)->delete();
            $customer->delete();
            $deletedCount++;
        }

        return back()->with('success', "{$deletedCount} selected customer(s) deleted successfully.");
    }
}
