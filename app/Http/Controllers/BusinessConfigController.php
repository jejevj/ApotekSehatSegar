<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BusinessConfigService;

class BusinessConfigController extends Controller
{
    protected BusinessConfigService $service;

    public function __construct(BusinessConfigService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $config = $this->service->getAll();
        return view('business_config.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_type'         => 'required|in:apotek,retail,fnb,general',
            'label_product'         => 'required|string|max:100',
            'label_location'        => 'required|string|max:100',
            'label_supplier'        => 'required|string|max:100',
            'label_customer'        => 'required|string|max:100',
            'label_purchase'        => 'required|string|max:100',
            'label_category'        => 'required|string|max:100',
            'label_unit'            => 'required|string|max:100',
            'trx_prefix'            => 'required|string|max:20',
            'receipt_footer'        => 'nullable|string|max:255',
            'low_stock_threshold'   => 'nullable|integer|min:0',
        ]);

        $fields = [
            'business_type', 'label_product', 'label_location', 'label_supplier',
            'label_customer', 'label_purchase', 'label_category', 'label_unit',
            'trx_prefix', 'receipt_footer', 'low_stock_threshold',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $this->service->set($field, $request->input($field));
            }
        }

        // Handle boolean toggles (unchecked = not present in request)
        $this->service->set('show_product_location', $request->boolean('show_product_location'));
        $this->service->set('show_product_content', $request->boolean('show_product_content'));

        return redirect()->route('business-config.index')->with('success', 'Konfigurasi bisnis berhasil disimpan.');
    }

    public function getPreset(string $businessType)
    {
        return response()->json(config('business_presets.' . $businessType, []));
    }
}
