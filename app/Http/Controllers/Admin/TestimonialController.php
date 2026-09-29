<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('order')->paginate(15);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('testimonials', 'public');
        }

        Testimonial::create([
            'name' => $request->name,
            'designation' => $request->designation,
            'content' => $request->content,
            'rating' => $request->rating,
            'image' => $imagePath,
            'order' => $request->order ?? 0,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial created successfully.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = [
            'name' => $request->name,
            'designation' => $request->designation,
            'content' => $request->content,
            'rating' => $request->rating,
            'order' => $request->order ?? 0,
            'status' => $request->has('status'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('testimonials', 'public');
        }

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated successfully.');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($testimonial->image);
        }
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:testimonials,id'
        ]);

        $testimonials = Testimonial::whereIn('id', $request->ids)->get();
        foreach ($testimonials as $testimonial) {
            if ($testimonial->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($testimonial->image);
            }
            $testimonial->delete();
        }

        return redirect()->route('admin.testimonials.index')->with('success', count($testimonials) . ' testimonial(s) deleted successfully.');
    }
}
