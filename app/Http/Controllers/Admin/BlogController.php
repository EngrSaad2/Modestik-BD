<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

use App\Models\BlogCategory;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('category', 'author')->latest()->paginate(15);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        $categories = BlogCategory::where('status', true)->get();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blogs', 'public');
        }

        Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt ?? Str::words(strip_tags($request->content), 20),
            'content' => $request->content,
            'category_id' => $request->category_id,
            'author_id' => auth()->id() ?? 1, // Fallback to 1 if no auth session
            'meta_title' => $request->meta_title ?? $request->title,
            'meta_description' => $request->meta_description,
            'image' => $imagePath,
            'status' => $request->has('status'),
            'published_at' => $request->has('status') ? now() : null,
        ]);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully.');
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::where('status', true)->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt ?? Str::words(strip_tags($request->content), 20),
            'content' => $request->content,
            'category_id' => $request->category_id,
            'meta_title' => $request->meta_title ?? $request->title,
            'meta_description' => $request->meta_description,
            'status' => $request->has('status'),
            'published_at' => $request->has('status') ? ($blog->published_at ?? now()) : null,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blogs,id',
        ]);

        $count = Blog::whereIn('id', $request->ids)->delete();
        return back()->with('success', "{$count} blog post(s) deleted successfully.");
    }
}
