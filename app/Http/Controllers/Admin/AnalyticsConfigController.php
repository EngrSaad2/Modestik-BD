<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalyticsConfigController extends Controller
{
    public function index()
    {
        return view('admin.analytics-config.index');
    }

    public function update(Request $request)
    {
        $keys = [
            'fb_pixel_enabled', 'fb_pixel_id', 'fb_pixel_token', 'fb_pixel_test_code',
            'google_analytics_enabled', 'google_analytics_id', 'google_analytics_secret',
            'tiktok_pixel_enabled', 'tiktok_pixel_id', 'tiktok_pixel_token',
            'pinterest_pixel_enabled', 'pinterest_pixel_id', 'pinterest_pixel_token',
            'snapchat_pixel_enabled', 'snapchat_pixel_id', 'snapchat_pixel_token',
            'clarity_enabled', 'clarity_project_id',
            'search_console_enabled', 'search_console_key'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                $value = $request->get($key);
                if ($value === 'on' || $value === '1') {
                    $value = '1';
                }
                Setting::set($key, $value, 'analytics');
            } else {
                if (str_ends_with($key, '_enabled')) {
                    Setting::set($key, '0', 'analytics');
                }
            }
        }

        Cache::forget('homepage_data');

        return back()->with('success', 'Configuration updated successfully.');
    }
}
