<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'rating.required' => 'অনুগ্রহ করে রেটিং সিলেক্ট করুন।',
            'comment.max' => 'মন্তব্যটি ১০০০ অক্ষরের মধ্যে হতে হবে।',
            'images.max' => 'আপনি সর্বোচ্চ ৫টি ছবি আপলোড করতে পারবেন।',
            'images.*.image' => 'আপলোডকৃত ফাইলটি অবশ্যই ছবি হতে হবে।',
            'images.*.max' => 'প্রতিটি ছবির সাইজ সর্বোচ্চ ২ মেগাবাইট হতে পারবে।',
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        // Check if user already reviewed
        $exists = Review::where('user_id', $userId)->where('product_id', $productId)->exists();
        if ($exists) {
            return back()->with('error', 'আপনি ইতিমধ্যে এই পণ্যটির একটি রিভিউ দিয়েছেন।');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        Review::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'images' => $imagePaths,
            'status' => 'pending', // Awaiting approval
        ]);

        return back()->with('success', 'আপনার মূল্যবান রিভিউটি জমা দেওয়ার জন্য ধন্যবাদ! অ্যাডমিন অনুমোদনের পর এটি প্রদর্শিত হবে।');
    }
}
