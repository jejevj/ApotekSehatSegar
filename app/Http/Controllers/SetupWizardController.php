<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BusinessConfigService;

class SetupWizardController extends Controller
{
    public function __construct(private BusinessConfigService $config) {}

    public function index()
    {
        if ($this->config->isSetupComplete()) {
            return redirect('/');
        }

        return view('setup.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|in:apotek,retail,fnb,general',
            'address'       => 'nullable|string',
            'phone'         => 'nullable|string|max:20',
        ]);

        // Simpan semua preset label sesuai business_type
        $presets = config('business_presets.' . $request->business_type, []);
        foreach ($presets as $key => $value) {
            $this->config->set($key, $value);
        }

        // Simpan data bisnis
        $this->config->set('business_name', $request->business_name);
        $this->config->set('business_type', $request->business_type);
        $this->config->set('address', $request->address);
        $this->config->set('phone', $request->phone);
        $this->config->set('is_setup_complete', true);

        return redirect('/')->with('success', 'Setup bisnis berhasil disimpan. Selamat datang!');
    }
}
