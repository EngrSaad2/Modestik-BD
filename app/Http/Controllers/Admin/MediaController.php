<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query();

        if ($request->filled('search')) {
            $query->where('filename', 'like', "%{$request->search}%")
                  ->orWhere('path', 'like', "%{$request->search}%");
        }

        $media = $query->latest()->paginate(24)->withQueryString();
        return view('admin.media.index', compact('media'));
    }

    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file|max:10240']);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        $media = Media::create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Uploaded successfully.', 'media' => $media, 'url' => asset('storage/' . $path)]);
        }
        return back()->with('success', 'Media file uploaded successfully.');
    }

    public function destroy(Media $media)
    {
        if ($media->path && Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }
        $media->delete();
        return back()->with('success', 'Media file deleted successfully.');
    }

    public function cleanUnused()
    {
        $usedImagePaths = ProductImage::pluck('image')->toArray();
        $allFiles = Storage::disk('public')->files('products');
        $deletedCount = 0;

        foreach ($allFiles as $file) {
            if (!in_array($file, $usedImagePaths)) {
                Storage::disk('public')->delete($file);
                $deletedCount++;
            }
        }

        return back()->with('success', "Media cleanup finished: {$deletedCount} unused images removed.");
    }
}
