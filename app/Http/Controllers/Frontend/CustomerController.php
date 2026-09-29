<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{Order, Address, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $ordersCount = Order::where('user_id', $user->id)->count();
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();
        return view('frontend.customer.dashboard', compact('user', 'ordersCount', 'recentOrders'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())->latest()->paginate(10);
        return view('frontend.customer.orders', compact('orders'));
    }

    public function orderDetail($orderNumber)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('order_number', $orderNumber)
            ->with('items.product')
            ->firstOrFail();
        return view('frontend.customer.order-detail', compact('order'));
    }

    public function downloadInvoice($orderNumber)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('order_number', $orderNumber)
            ->with('items.product')
            ->firstOrFail();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [80, 220],
            'margin_left' => 4,
            'margin_right' => 4,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $html = view('frontend.invoice-pdf', compact('order'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output("invoice-{$order->order_number}.pdf", 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    public function profile()
    {
        return view('frontend.customer.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function addresses()
    {
        $addresses = Address::where('user_id', auth()->id())->get();
        return view('frontend.customer.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'division' => 'required|string',
            'district' => 'required|string',
        ]);

        $isDefault = $request->has('is_default');
        if ($isDefault) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        Address::create(array_merge($request->all(), [
            'user_id' => auth()->id(),
            'is_default' => $isDefault
        ]));

        return back()->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, $id)
    {
        $address = Address::where('user_id', auth()->id())->findOrFail($id);
        $request->validate([
            'label' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'division' => 'required|string',
            'district' => 'required|string',
        ]);

        $isDefault = $request->has('is_default');
        if ($isDefault) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        $address->update(array_merge($request->all(), [
            'is_default' => $isDefault
        ]));

        return back()->with('success', 'Address updated successfully.');
    }

    public function deleteAddress($id)
    {
        Address::where('user_id', auth()->id())->findOrFail($id)->delete();
        return back()->with('success', 'Address deleted successfully.');
    }

    public function trackOrderForm()
    {
        return view('frontend.track-order');
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'phone' => 'required|string',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->where('phone', $request->phone)
            ->first();

        if (!$order) {
            return back()->withErrors(['order_number' => 'Order not found matching details.']);
        }

        return view('frontend.track-order-result', compact('order'));
    }
}
