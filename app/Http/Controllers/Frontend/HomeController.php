<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\{Category, Product, Slider, Banner, Brand, Testimonial};
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $data = Cache::remember('homepage_data', 3600, function () {
            $flashSaleProducts = Product::active()->flashSale()->with(['primaryImage', 'category'])
                ->take(8)->get();
            $flashSaleIds = $flashSaleProducts->pluck('id')->toArray();

            $nailCategoryIds = Category::where('id', 24)->orWhere('parent_id', 24)->pluck('id')->toArray();
            $organicCategoryIds = [54];
            $anmolCategoryIds = [55];

            $otherProducts = Product::active()
                ->with(['primaryImage', 'category'])
                ->latest()
                ->get()
                ->sort(function ($a, $b) use ($nailCategoryIds, $organicCategoryIds, $anmolCategoryIds) {
                    $aScore = 0;
                    if (in_array($a->category_id, $nailCategoryIds)) {
                        $aScore = 3;
                    } elseif (in_array($a->category_id, $organicCategoryIds)) {
                        $aScore = 2;
                    } elseif (in_array($a->category_id, $anmolCategoryIds)) {
                        $aScore = 1;
                    }

                    $bScore = 0;
                    if (in_array($b->category_id, $nailCategoryIds)) {
                        $bScore = 3;
                    } elseif (in_array($b->category_id, $organicCategoryIds)) {
                        $bScore = 2;
                    } elseif (in_array($b->category_id, $anmolCategoryIds)) {
                        $bScore = 1;
                    }

                    if ($aScore !== $bScore) {
                        return $bScore <=> $aScore;
                    }
                    return $b->id <=> $a->id;
                })
                ->values();

            return [
                'sliders' => Slider::active()->get(),
                'categories' => Category::active()->parents()->with('children')
                    ->where('is_featured', true)->orderBy('order')->get(),
                'flashSaleProducts' => $flashSaleProducts,
                'otherProducts' => $otherProducts,
                'brands' => Brand::active()->featured()->orderBy('order')->get(),
                'banners' => Banner::active()->position('home')->get(),
                'testimonials' => Testimonial::active()->get(),
                'featuredReviews' => \App\Models\Review::approved()->featured()->with(['product', 'user'])->get(),
                'highestRatedReviews' => \App\Models\Review::approved()->where('rating', '>=', 4)->with(['product', 'user'])->latest()->take(6)->get(),
            ];
        });

        return view('frontend.home', $data);
    }
}
