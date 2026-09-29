<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index() { return view('admin.reviews.index', ['reviews' => Review::latest()->paginate(15)]); }
    public function show(Review $review) { return view('admin.reviews.show', compact('review')); }
    public function update(Request $request, Review $review) {
        $request->validate([
            'status' => 'nullable|in:pending,approved,rejected',
            'is_featured' => 'nullable|boolean',
        ]);

        $data = [];
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }
        if ($request->has('is_featured')) {
            $data['is_featured'] = $request->is_featured ? true : false;
        }

        $review->update($data);
        return back()->with('success', 'Review updated successfully.');
    }
    public function destroy(Review $review) { 
        $review->delete(); 
        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.'); 
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reviews,id',
        ]);

        $count = Review::whereIn('id', $request->ids)->delete();
        return back()->with('success', "{$count} review(s) deleted successfully.");
    }
}
