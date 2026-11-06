<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $allSettings = Setting::all();
        $settings = [];
        foreach ($allSettings as $s) {
            $settings[$s->key] = $s->value;
        }

        return Inertia::render('Settings', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $items = $request->input('settings', []);

        foreach ($items as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Đã lưu cấu hình cài đặt website thành công!');
    }
}
