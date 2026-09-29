<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index() { return view('admin.contacts.index', ['contacts' => Contact::latest()->paginate(15)]); }
    public function show(Contact $contact) { return view('admin.contacts.show', compact('contact')); }
    public function destroy(Contact $contact) { $contact->delete(); return redirect()->route('admin.contacts.index')->with('success', 'Contact deleted.'); }
    public function updateStatus(Contact $contact) {
        $contact->update(['status' => 'read']);
        return back()->with('success', 'Contact marked as read.');
    }

    public function bulkDelete(\Illuminate\Http\Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:contacts,id']);
        $count = Contact::whereIn('id', $request->ids)->delete();
        return back()->with('success', "{$count} contact message(s) deleted.");
    }
}
