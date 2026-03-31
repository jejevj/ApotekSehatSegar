<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\Store;
use App\Models\User;
use App\Models\Scopes\TenantScope;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStores = Store::count();
        $activeStores = Store::where('is_active', true)->count();
        $totalUsers = User::whereNotNull('store_id')->count();
        $todayTransactions = Penjualan::withoutGlobalScope(TenantScope::class)
            ->whereDate('tgl_penjualan', today())
            ->count();

        return view('admin.dashboard.index', compact(
            'totalStores', 'activeStores', 'totalUsers', 'todayTransactions'
        ));
    }
}
