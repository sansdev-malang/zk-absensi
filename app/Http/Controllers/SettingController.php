<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'payroll_start_date' => 'required|integer|min:1|max:31',
            'payroll_end_date' => 'required|integer|min:1|max:31',
        ]);

        Setting::updateOrCreate(['key' => 'payroll_start_date'], ['value' => $request->payroll_start_date]);
        Setting::updateOrCreate(['key' => 'payroll_end_date'], ['value' => $request->payroll_end_date]);

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
