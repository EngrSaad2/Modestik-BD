<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Attribute, AttributeValue};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->orderBy('order')->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:color,size,text',
        ]);

        Attribute::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute created successfully.');
    }

    public function edit(Attribute $attribute)
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:color,size,text',
        ]);

        $attribute->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted successfully.');
    }

    // Nested attribute values management hooks
    public function storeValue(Request $request, Attribute $attribute)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:20',
        ]);

        AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => $request->value,
            'color_code' => $request->color_code,
        ]);

        return back()->with('success', 'Attribute value added successfully.');
    }

    public function destroyValue($id)
    {
        AttributeValue::findOrFail($id)->delete();
        return back()->with('success', 'Attribute value deleted successfully.');
    }
}
