<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

use App\Models\Product;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(15);
        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $products = Product::active()->get();
        return view('admin.campaigns.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:campaigns,name',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('campaigns', 'public');
        }

        $campaign = Campaign::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'image' => $imagePath,
            'status' => $request->has('status'),
        ]);

        $campaign->products()->sync($request->products);

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function edit(Campaign $campaign)
    {
        $products = Product::active()->get();
        $campaign->load('products');
        return view('admin.campaigns.edit', compact('campaign', 'products'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:campaigns,name,' . $campaign->id,
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'status' => $request->has('status'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign->update($data);
        $campaign->products()->sync($request->products);

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->products()->detach();
        $campaign->delete();
        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign deleted successfully.');
    }
}
