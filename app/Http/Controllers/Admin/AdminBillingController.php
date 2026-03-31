<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingSetting;
use App\Models\Store;
use Illuminate\Http\Request;

class AdminBillingController extends Controller
{
    public function index(Store $store)
    {
        $billings = BillingSetting::withoutGlobalScopes()
            ->where('store_id', $store->id)
            ->latest()
            ->get();

        return view('admin.billing.index', compact('store', 'billings'));
    }

    public function create(Store $store)
    {
        return view('admin.billing.create', compact('store'));
    }

    public function store(Store $store, Request $request)
    {
        $validated = $request->validate([
            'expired_at'     => 'required|date',
            'jumlah_tagihan' => 'required|integer|min:0',
            'nama_bank'      => 'nullable|string|max:255',
            'no_rek'         => 'nullable|string|max:255',
            'status'         => 'required|string|in:aktif,nonaktif,expired',
        ]);

        BillingSetting::withoutGlobalScopes()->create(array_merge(
            $validated,
            ['store_id' => $store->id, 'is_active' => $validated['status'] === 'aktif']
        ));

        return redirect()->route('admin.stores.billing', $store)
            ->with('success', 'Billing berhasil ditambahkan.');
    }

    public function activate(Store $store, BillingSetting $billing)
    {
        // Nonaktifkan semua billing toko ini terlebih dahulu
        BillingSetting::withoutGlobalScopes()
            ->where('store_id', $store->id)
            ->update(['is_active' => false, 'status' => 'nonaktif']);

        // Aktifkan billing yang dipilih
        $billing->update(['is_active' => true, 'status' => 'aktif']);

        return back()->with('success', 'Billing berhasil diaktifkan.');
    }

    public function setStatus(Store $store, BillingSetting $billing, string $status)
    {
        $allowedStatuses = ['aktif', 'nonaktif', 'expired'];

        if (!in_array($status, $allowedStatuses)) {
            return back()->with('error', 'Status tidak valid.');
        }

        $billing->update([
            'status'    => $status,
            'is_active' => $status === 'aktif',
        ]);

        return back()->with('success', "Status billing diubah menjadi \"{$status}\".");
    }
}
