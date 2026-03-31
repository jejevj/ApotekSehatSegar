<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            // Idempoten: buat atau ambil store default
            $storeId = DB::table('stores')->where('slug', 'toko-utama')->value('id');

            if (!$storeId) {
                $storeId = DB::table('stores')->insertGetId([
                    'name'          => 'Toko Utama',
                    'slug'          => 'toko-utama',
                    'business_type' => 'general',
                    'is_active'     => 1,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // Tabel operasional — update record yang store_id masih null
            $operationalTables = [
                'settings',
                'billing_settings',
                'business_configs',
                'tb_barang',
                'tb_penjualan',
                'tb_penjualan_detail',
                'pembelians',
                'pembelian_details',
                'pembayaran_pembelians',
                'tb_pelanggan',
                'distributors',
                'categories',
                'units',
                'tb_rak',
                'metode_pembayarans',
                'stock_opnames',
                'stock_opname_items',
            ];

            foreach ($operationalTables as $table) {
                // Cek apakah tabel dan kolom store_id ada
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'store_id')) {
                    DB::table($table)
                        ->whereNull('store_id')
                        ->update(['store_id' => $storeId]);
                }
            }

            // Update users: non-super_admin yang store_id masih null
            // Ambil role_id super_admin
            $superAdminRoleId = DB::table('roles')
                ->where('slug', 'super_admin')
                ->whereNull('store_id')
                ->value('id');

            DB::table('users')
                ->whereNull('store_id')
                ->when($superAdminRoleId, function ($query) use ($superAdminRoleId) {
                    $query->where('role_id', '!=', $superAdminRoleId);
                })
                ->update(['store_id' => $storeId]);

            // Update roles: bukan super_admin dan store_id masih null
            DB::table('roles')
                ->whereNull('store_id')
                ->where('slug', '!=', 'super_admin')
                ->update(['store_id' => $storeId]);
        });
    }

    public function down(): void
    {
        // Tidak ada rollback — data migration tidak di-reverse
    }
};
