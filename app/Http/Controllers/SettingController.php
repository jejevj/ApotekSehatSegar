<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:setting.view')->only('index');
        $this->middleware('permission:setting.update')->only('update');
    }

    public function index()
    {
        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create([
                'nama_aplikasi' => 'Apotek App',
                'alamat' => 'Alamat Apotek',
                'telepon' => '08123456789'
            ]);
        }
        return view('setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();
        
        $request->validate([
            'nama_aplikasi' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['logo', 'favicon']);

        if ($request->hasFile('logo')) {
            if ($setting->logo && file_exists(public_path('images/' . $setting->logo))) {
                unlink(public_path('images/' . $setting->logo));
            }
            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('images'), $logoName);
            $data['logo'] = $logoName;
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon && file_exists(public_path('images/' . $setting->favicon))) {
                unlink(public_path('images/' . $setting->favicon));
            }
            $favicon = $request->file('favicon');
            $faviconName = 'favicon_' . time() . '.' . $favicon->getClientOriginalExtension();
            $favicon->move(public_path('images'), $faviconName);
            $data['favicon'] = $faviconName;
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui');
    }
}
