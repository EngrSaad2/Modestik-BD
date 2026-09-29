<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $products = Product::active()
            ->search($q)
            ->with(['primaryImage', 'category'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('frontend.search', compact('products', 'q'));
    }

    public function suggestions(Request $request)
    {
        $q = $request->input('q');
        if (strlen($q) < 2) {
            return '';
        }

        $products = Product::active()
            ->search($q)
            ->take(5)
            ->get();

        return view('components.search-suggestions', compact('products'));
    }
}
