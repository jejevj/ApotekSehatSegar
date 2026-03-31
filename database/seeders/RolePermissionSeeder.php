<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Roles as global templates (store_id = null)
        // Gunakan withoutGlobalScope agar TenantScope tidak memfilter query updateOrCreate
        $superAdminRole = Role::withoutGlobalScope(TenantScope::class)
            ->updateOrCreate(
                ['slug' => 'super_admin', 'store_id' => null],
                ['name' => 'Super Admin', 'store_id' => null]
            );
        $adminRole = Role::withoutGlobalScope(TenantScope::class)
            ->updateOrCreate(
                ['slug' => 'admin', 'store_id' => null],
                ['name' => 'Administrator', 'store_id' => null]
            );
        $kasirRole = Role::withoutGlobalScope(TenantScope::class)
            ->updateOrCreate(
                ['slug' => 'kasir', 'store_id' => null],
                ['name' => 'Kasir', 'store_id' => null]
            );
        $billingRole = Role::withoutGlobalScope(TenantScope::class)
            ->updateOrCreate(
                ['slug' => 'billing', 'store_id' => null],
                ['name' => 'Billing', 'store_id' => null]
            );

        // 2. Define Features and Actions
        $features = [
            'barang' => ['view', 'create', 'update', 'delete'],
            'unit' => ['view', 'create', 'update', 'delete'],
            'rak' => ['view', 'create', 'update', 'delete'],
            'opname' => ['view', 'create', 'update', 'delete', 'approve'],
            'pelanggan' => ['view', 'create', 'update', 'delete'],
            'penjualan' => ['view', 'create', 'update', 'delete', 'print_struk'],
            'distributor' => ['view', 'create', 'update', 'delete'],
            'pembelian' => ['view', 'create', 'update', 'delete'],
            'metode_pembayaran' => ['view', 'create', 'update', 'delete'],
            'pengguna' => ['view', 'create', 'update', 'delete'],
            'role' => ['view', 'create', 'update', 'delete'],
            'menu' => ['view', 'create', 'update', 'delete'],
            'setting' => ['view', 'update'],
            'laporan' => ['view', 'print'],
            'billing' => ['manage'],
            // FnB Module
            'ingredients' => ['view', 'create', 'update', 'delete'],
            'recipes' => ['view', 'create', 'update', 'delete'],
            'hpp' => ['view'],
        ];

        $allPermissionIds = [];

        foreach ($features as $feature => $actions) {
            foreach ($actions as $action) {
                // Custom Name for Specific Actions
                $customNames = [
                    'print_struk' => 'Cetak Struk',
                    'approve' => 'Setujui',
                    'print' => 'Cetak Laporan',
                    'manage' => 'Kelola',
                ];

                $actionName = isset($customNames[$action]) ? $customNames[$action] : ucfirst($action);

                $permission = Permission::updateOrCreate(
                    ['slug' => $feature . '.' . $action],
                    [
                        'name' => $actionName . ' ' . ucfirst($feature),
                        'feature' => $feature,
                        'action' => $action,
                    ]
                );
                $allPermissionIds[] = $permission->id;
            }
        }

        // 3. Assign Permissions
        // Super Admin: semua permissions
        $superAdminRole->permissions()->sync($allPermissionIds);
        // Admin: semua kecuali billing.manage
        $adminPermissionIds = Permission::where('slug', '!=', 'billing.manage')->pluck('id');
        $adminRole->permissions()->sync($adminPermissionIds);
        // Billing: hanya billing.manage
        $billingPermissionIds = Permission::where('slug', 'billing.manage')->pluck('id');
        $billingRole->permissions()->sync($billingPermissionIds);

        // 4. Assign Limited Permissions to Kasir (Contoh)
        $kasirPermissions = Permission::whereIn('feature', ['penjualan', 'pelanggan', 'barang'])
            ->whereIn('action', ['view', 'create', 'print_struk'])
            ->pluck('id')
            ->toArray();

        $kasirRole->permissions()->sync($kasirPermissions);

        // 5. Update Existing Users or Create Admin
        // Gunakan withoutGlobalScope agar query tidak terfilter TenantScope
        $adminUser = User::withoutGlobalScopes()->where('level', 'admin')->first();
        if ($adminUser) {
            $adminUser->update(['role_id' => $adminRole->id]);
        } else {
            User::create([
                'username' => 'admin',
                'nama' => 'Administrator',
                'password' => Hash::make('admin123'),
                'level' => 'admin',
                'role_id' => $adminRole->id,
            ]);
        }

        // Catatan: Penugasan user ke Super Admin dapat dilakukan via UI Pengguna.

        $kasirUser = User::withoutGlobalScopes()->where('level', 'kasir')->first();
        if ($kasirUser) {
            $kasirUser->update(['role_id' => $kasirRole->id]);
        }
    }
}
