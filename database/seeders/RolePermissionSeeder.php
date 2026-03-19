<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Roles
        $superAdminRole = Role::updateOrCreate(['name' => 'Super Admin'], ['slug' => 'super_admin']);
        $adminRole = Role::updateOrCreate(['name' => 'Administrator'], ['slug' => 'admin']);
        $kasirRole = Role::updateOrCreate(['name' => 'Kasir'], ['slug' => 'kasir']);
        $billingRole = Role::updateOrCreate(['name' => 'Billing'], ['slug' => 'billing']);

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
            'pengguna' => ['view', 'create', 'update', 'delete'],
            'role' => ['view', 'create', 'update', 'delete'],
            'menu' => ['view', 'create', 'update', 'delete'],
            'setting' => ['view', 'update'],
            'laporan' => ['view', 'print'],
            'billing' => ['manage'],
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
        $adminUser = User::where('level', 'admin')->first();
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

        $kasirUser = User::where('level', 'kasir')->first();
        if ($kasirUser) {
            $kasirUser->update(['role_id' => $kasirRole->id]);
        }
    }
}
