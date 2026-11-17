<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_one_time_va' => 'required|numeric|min:0|max:100',
            'default_one_time_sales' => 'required|numeric|min:0|max:100',
            'default_recurring_va' => 'required|numeric|min:0|max:100',
            'default_recurring_sales' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Commission settings updated successfully.');
    }
}