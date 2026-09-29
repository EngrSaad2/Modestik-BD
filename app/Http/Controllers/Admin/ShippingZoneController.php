<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $shippingZones = ShippingZone::latest()->paginate(15);
        return view('admin.shipping-zones.index', compact('shippingZones'));
    }

    public function create()
    {
        return view('admin.shipping-zones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shipping_zones,name',
            'charge' => 'required|numeric|min:0',
            'min_days' => 'required|integer|min:1',
            'max_days' => 'required|integer|min:1|gte:min_days',
            'free_shipping_min' => 'nullable|numeric|min:0',
        ]);

        ShippingZone::create([
            'name' => $request->name,
            'charge' => $request->charge,
            'min_days' => $request->min_days,
            'max_days' => $request->max_days,
            'free_shipping_min' => $request->free_shipping_min,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.shipping-zones.index')->with('success', 'Shipping Zone created successfully.');
    }

    public function edit(ShippingZone $shippingZone)
    {
        return view('admin.shipping-zones.edit', compact('shippingZone'));
    }

    public function update(Request $request, ShippingZone $shippingZone)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shipping_zones,name,' . $shippingZone->id,
            'charge' => 'required|numeric|min:0',
            'min_days' => 'required|integer|min:1',
            'max_days' => 'required|integer|min:1|gte:min_days',
            'free_shipping_min' => 'nullable|numeric|min:0',
        ]);

        $shippingZone->update([
            'name' => $request->name,
            'charge' => $request->charge,
            'min_days' => $request->min_days,
            'max_days' => $request->max_days,
            'free_shipping_min' => $request->free_shipping_min,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.shipping-zones.index')->with('success', 'Shipping Zone updated successfully.');
    }

    public function destroy(ShippingZone $shippingZone)
    {
        $shippingZone->delete();
        return redirect()->route('admin.shipping-zones.index')->with('success', 'Shipping Zone deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:shipping_zones,id'
        ]);

        $count = ShippingZone::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.shipping-zones.index')->with('success', "{$count} shipping zone(s) deleted successfully.");
    }
}
