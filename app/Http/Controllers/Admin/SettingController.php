<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable'],
        ]);

        foreach ($data['settings'] as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            if ($setting) {
                // Handle checkboxes (absent = false)
                if ($setting->type === 'boolean') {
                    $value = $value ? '1' : '0';
                }
                SystemSetting::set($key, $value, $setting->type, $setting->group);
            }
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
