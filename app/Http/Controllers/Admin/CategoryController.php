<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent', 'children');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
            $categories = $query->latest()->paginate(20)->withQueryString();
        } else {
            $categories = Category::parents()->with('children')->orderBy('order')->paginate(20);
        }

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::parents()->active()->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        Category::create(array_merge($request->all(), [
            'slug' => $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name),
            'is_featured' => $request->has('is_featured'),
            'status' => $request->has('status'),
            'meta_keywords' => $request->meta_keywords ?? null,
        ]));

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $parents = Category::parents()->active()->where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $category->update(array_merge($request->all(), [
            'slug' => $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name),
            'is_featured' => $request->has('is_featured'),
            'status' => $request->has('status'),
            'meta_keywords' => $request->meta_keywords ?? null,
        ]));

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        $count = Category::whereIn('id', $request->ids)->delete();
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return back()->with('success', "{$count} category(ies) deleted successfully.");
    }
}
