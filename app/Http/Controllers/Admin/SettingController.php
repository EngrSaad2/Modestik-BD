<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index($group = 'general')
    {
        if (!in_array($group, ['general', 'social'])) {
            return redirect()->route('admin.settings.index', 'general');
        }
        $settings = Setting::where('group', $group)->get();
        return view('admin.settings.index', compact('settings', 'group'));
    }

    public function update(Request $request, $group)
    {
        if (!in_array($group, ['general', 'social'])) {
            abort(404);
        }
        foreach ($request->except('_token') as $key => $value) {
            Setting::set($key, $value, $group);
        }

        \Illuminate\Support\Facades\Cache::forget('homepage_data');

        return back()->with('success', 'Settings updated successfully.');
    }
}
