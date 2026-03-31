<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\Scopes\TenantScope;
use App\Models\Store;
use App\Services\StoreProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function __construct(private StoreProvisioningService $provisioning) {}

    public function index()
    {
        $stores = Store::with('activeBilling')->latest()->paginate(20);
        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'business_type' => 'required|string|in:apotek,retail,fnb,general',
            'owner_name'    => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'admin_username'=> 'required|string|max:255|unique:users,username',
            'admin_nama'    => 'required|string|max:255',
            'admin_password'=> 'required|string|min:6',
        ]);

        $store = Store::create([
            'name'          => $validated['name'],
            'slug'          => Str::slug($validated['name']) . '-' . Str::random(6),
            'business_type' => $validated['business_type'],
            'is_active'     => true,
            'owner_name'    => $validated['owner_name'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'address'       => $validated['address'] ?? null,
        ]);

        $adminData = [
            'username' => $validated['admin_username'],
            'nama'     => $validated['admin_nama'],
            'password' => $validated['admin_password'],
        ];

        $this->provisioning->provision($store, $adminData, $validated['business_type']);

        return redirect()->route('admin.stores.show', $store)
            ->with('success', "Toko \"{$store->name}\" berhasil dibuat.");
    }

    public function show(Store $store)
    {
        $store->load([
            'users',
            'activeBilling',
            'businessConfigs',
        ]);

        return view('admin.stores.show', compact('store'));
    }

    public function edit(Store $store)
    {
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'business_type' => 'required|string|in:apotek,retail,fnb,general',
            'owner_name'    => 'nullable|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $store->update($validated);

        return redirect()->route('admin.stores.show', $store)
            ->with('success', 'Data toko berhasil diperbarui.');
    }

    public function destroy(Store $store)
    {
        $hasTransactions = Penjualan::withoutGlobalScope(TenantScope::class)
            ->where('store_id', $store->id)
            ->exists();

        if ($hasTransactions) {
            return back()->with('error', 'Toko tidak dapat dihapus karena memiliki data transaksi.');
        }

        $store->delete();

        return redirect()->route('admin.stores.index')
            ->with('success', 'Toko berhasil dihapus.');
    }
}
