<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->paginate(15);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'url' => 'nullable|url',
            'position' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'image' => $imagePath,
            'url' => $request->url,
            'position' => $request->position,
            'order' => $request->order ?? 0,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'url' => 'nullable|url',
            'position' => 'required|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $data = [
            'title' => $request->title,
            'url' => $request->url,
            'position' => $request->position,
            'order' => $request->order ?? 0,
            'status' => $request->has('status'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:banners,id'
        ]);

        $banners = Banner::whereIn('id', $request->ids)->get();
        foreach ($banners as $banner) {
            if ($banner->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($banner->image);
            }
            $banner->delete();
        }

        return redirect()->route('admin.banners.index')->with('success', count($banners) . ' banner(s) deleted successfully.');
    }
}
