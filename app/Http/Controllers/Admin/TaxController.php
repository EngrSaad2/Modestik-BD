<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::latest()->paginate(15);
        return view('admin.taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('admin.taxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:taxes,name',
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
        ]);

        Tax::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'type' => $request->type,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.taxes.index')->with('success', 'Tax created successfully.');
    }

    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:taxes,name,' . $tax->id,
            'rate' => 'required|numeric|min:0',
            'type' => 'required|in:percentage,fixed',
        ]);

        $tax->update([
            'name' => $request->name,
            'rate' => $request->rate,
            'type' => $request->type,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.taxes.index')->with('success', 'Tax updated successfully.');
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();
        return redirect()->route('admin.taxes.index')->with('success', 'Tax deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:taxes,id'
        ]);

        $count = Tax::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.taxes.index')->with('success', "{$count} tax rule(s) deleted successfully.");
    }
}
