<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;

class NewsletterController extends Controller
{
    public function index() { return view('admin.newsletters.index', ['newsletters' => Newsletter::latest()->paginate(15)]); }
    public function destroy(Newsletter $newsletter) { $newsletter->delete(); return redirect()->route('admin.newsletters.index')->with('success', 'Subscription removed.'); }
    public function export() { return response()->json(['message' => 'Exported subscriptions.']); }
}
