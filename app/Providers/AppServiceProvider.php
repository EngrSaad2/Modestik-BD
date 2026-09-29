<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $clearAllCaches = function () {
            \Illuminate\Support\Facades\Cache::forget('homepage_data');
            \Illuminate\Support\Facades\Cache::forget('nav_categories');
            \Illuminate\Support\Facades\Cache::forget('sidebar_categories');
            \Illuminate\Support\Facades\Cache::forget('sidebar_brands');
        };

        \App\Models\Slider::saved($clearAllCaches);
        \App\Models\Slider::deleted($clearAllCaches);
        \App\Models\Category::saved($clearAllCaches);
        \App\Models\Category::deleted($clearAllCaches);
        \App\Models\Product::saved($clearAllCaches);
        \App\Models\Product::deleted($clearAllCaches);
        \App\Models\Brand::saved($clearAllCaches);
        \App\Models\Brand::deleted($clearAllCaches);
        \App\Models\Banner::saved($clearAllCaches);
        \App\Models\Banner::deleted($clearAllCaches);
        \App\Models\Testimonial::saved($clearAllCaches);
        \App\Models\Testimonial::deleted($clearAllCaches);
    }
}
