<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::latest()->paginate(15);
        return view('admin.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:pending,resolved'
        ]);

        $complaint->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Complaint status updated successfully.');
    }

    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return redirect()->route('admin.complaints.index')->with('success', 'Complaint deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:complaints,id'
        ]);

        $count = Complaint::whereIn('id', $request->ids)->delete();

        return redirect()->route('admin.complaints.index')->with('success', "{$count} complaint(s) deleted successfully.");
    }
}
