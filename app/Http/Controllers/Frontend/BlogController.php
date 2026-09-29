<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::published()->with('category');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $blogs = $query->latest('published_at')->paginate(6);
        $categories = BlogCategory::active()->withCount('blogs')->get();
        $recentBlogs = Blog::published()->latest('published_at')->take(5)->get();

        return view('frontend.blog.index', compact('blogs', 'categories', 'recentBlogs'));
    }

    public function show($slug)
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();
        $blog->increment('views');

        $categories = BlogCategory::active()->withCount('blogs')->get();
        $recentBlogs = Blog::published()->where('id', '!=', $blog->id)->latest('published_at')->take(5)->get();

        return view('frontend.blog.show', compact('blog', 'categories', 'recentBlogs'));
    }
}
