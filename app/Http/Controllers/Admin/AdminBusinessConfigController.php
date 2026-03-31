<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessConfig;
use App\Models\Store;
use App\Services\StoreContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminBusinessConfigController extends Controller
{
    public function index(Store $store)
    {
        $configs = BusinessConfig::withoutGlobalScopes()
            ->where('store_id', $store->id)
            ->get()
            ->keyBy('key');

        return view('admin.business_config.index', compact('store', 'configs'));
    }

    public function update(Store $store, Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $isBool = in_array($value, ['1', '0', 'true', 'false'], true) ||
                      is_bool($value);
            $type = $isBool ? 'boolean' : 'string';
            $stringValue = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

            BusinessConfig::withoutGlobalScopes()->updateOrCreate(
                ['store_id' => $store->id, 'key' => $key],
                ['value' => $stringValue, 'type' => $type]
            );
        }

        // Invalidate cache untuk toko ini
        Cache::forget("business_config_{$store->id}");

        return back()->with('success', 'Konfigurasi bisnis berhasil diperbarui.');
    }
}
