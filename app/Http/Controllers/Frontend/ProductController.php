<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category, Brand};
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['primaryImage', 'category', 'brand']);

        // Filters
        if ($request->category) $query->where('category_id', $request->category);
        if ($request->brand) $query->where('brand_id', $request->brand);
        if ($request->min_price) $query->where('price', '>=', $request->min_price);
        if ($request->max_price) $query->where('price', '<=', $request->max_price);
        if ($request->featured) $query->featured();
        if ($request->trending) $query->trending();
        if ($request->q) $query->search($request->q);

        // Sorting
        $query = match ($request->sort) {
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'popular' => $query->orderByDesc('views'),
            'newest' => $query->latest(),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        [$categories, $brands] = $this->getSidebarData();

        return view('frontend.products.index', compact('products', 'categories', 'brands'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->active()
            ->with(['images', 'category', 'brand', 'tags', 'variants.values.attributeValue.attribute', 'approvedReviews.user'])
            ->firstOrFail();

        $product->increment('views');

        $relatedProducts = Product::active()->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)->with('primaryImage', 'category')
            ->inRandomOrder()->take(4)->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();
        $categoryIds = collect([$category->id])->merge($category->children->pluck('id'));

        $products = Product::active()->whereIn('category_id', $categoryIds)
            ->with(['primaryImage', 'category'])->latest()->paginate(12);

        [$categories, $brands] = $this->getSidebarData();

        return view('frontend.products.index', compact('products', 'categories', 'brands', 'category'));
    }

    public function brand($slug)
    {
        $brand = Brand::where('slug', $slug)->active()->firstOrFail();
        $products = Product::active()->where('brand_id', $brand->id)
            ->with(['primaryImage', 'category'])->latest()->paginate(12);

        [$categories, $brands] = $this->getSidebarData();

        return view('frontend.products.index', compact('products', 'categories', 'brands', 'brand'));
    }

    public function flashSale()
    {
        $products = Product::active()->flashSale()->with(['primaryImage', 'category'])->paginate(12);
        [$categories, $brands] = $this->getSidebarData();

        return view('frontend.products.index', compact('products', 'categories', 'brands'));
    }

    private function getSidebarData()
    {
        $categories = \Illuminate\Support\Facades\Cache::remember('sidebar_categories', 3600, function () {
            $parents = Category::active()->parents()->with('children')->get();
            foreach ($parents as $parent) {
                $categoryIds = array_merge([$parent->id], $parent->children->pluck('id')->toArray());
                $parent->products_count = Product::active()->whereIn('category_id', $categoryIds)->count();
            }
            return $parents;
        });
        $brands = \Illuminate\Support\Facades\Cache::remember('sidebar_brands', 3600, function () {
            return Brand::active()->withCount('products')->get();
        });
        return [$categories, $brands];
    }
}
