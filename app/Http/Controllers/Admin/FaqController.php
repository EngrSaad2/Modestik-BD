<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index() { return view('admin.faqs.index', ['faqs' => Faq::orderBy('order')->paginate(15)]); }
    public function create() { return view('admin.faqs.create'); }
    public function store(Request $request) { return redirect()->route('admin.faqs.index')->with('success', 'Faq created.'); }
    public function edit(Faq $faq) { return view('admin.faqs.edit', compact('faq')); }
    public function update(Request $request, Faq $faq) { return redirect()->route('admin.faqs.index')->with('success', 'Faq updated.'); }
    public function destroy(Faq $faq) { $faq->delete(); return redirect()->route('admin.faqs.index')->with('success', 'Faq deleted.'); }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:faqs,id']);
        $count = Faq::whereIn('id', $request->ids)->delete();
        return redirect()->route('admin.faqs.index')->with('success', "{$count} FAQ(s) deleted.");
    }
}
