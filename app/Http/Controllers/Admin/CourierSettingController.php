<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Courier\CourierManager;
use Illuminate\Http\Request;

class CourierSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'steadfast_api_key' => Setting::get('steadfast_api_key', env('STEADFAST_API_KEY', '')),
            'steadfast_secret_key' => Setting::get('steadfast_secret_key', env('STEADFAST_SECRET_KEY', '')),
            'steadfast_base_url' => Setting::get('steadfast_base_url', env('STEADFAST_BASE_URL', 'https://portal.steadfast.com.bd/api/v1')),
            'steadfast_mode' => Setting::get('steadfast_mode', 'live'),
            'default_courier' => Setting::get('default_courier', 'steadfast'),
        ];

        return view('admin.settings.courier', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'steadfast_api_key' => 'required|string',
            'steadfast_secret_key' => 'required|string',
            'steadfast_base_url' => 'required|url',
            'steadfast_mode' => 'required|in:test,live',
            'default_courier' => 'required|string',
        ]);

        Setting::set('steadfast_api_key', trim($request->steadfast_api_key));
        Setting::set('steadfast_secret_key', trim($request->steadfast_secret_key));
        Setting::set('steadfast_base_url', trim($request->steadfast_base_url));
        Setting::set('steadfast_mode', $request->steadfast_mode);
        Setting::set('default_courier', $request->default_courier);

        return back()->with('success', 'Courier settings updated successfully.');
    }

    public function testConnection(CourierManager $courierManager)
    {
        $res = $courierManager->driver('steadfast')->checkBalance();
        if ($res['success']) {
            return back()->with('success', $res['message']);
        }
        return back()->with('error', $res['message']);
    }
}
