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
        $adminRole = Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Administrator']);
        $kasirRole = Role::updateOrCreate(['slug' => 'kasir'], ['name' => 'Kasir']);

        // 2. Define Features and Actions
        $features = [
            'barang' => ['view', 'create', 'update', 'delete'],
            'pelanggan' => ['view', 'create', 'update', 'delete'],
            'penjualan' => ['view', 'create', 'update', 'delete'],
            'pengguna' => ['view', 'create', 'update', 'delete'],
            'role' => ['view', 'create', 'update', 'delete'],
            'menu' => ['view', 'create', 'update', 'delete'],
            'laporan' => ['view', 'print'],
        ];

        $allPermissionIds = [];

        foreach ($features as $feature => $actions) {
            foreach ($actions as $action) {
                $permission = Permission::updateOrCreate(
                    ['slug' => $feature . '.' . $action],
                    [
                        'name' => ucfirst($action) . ' ' . ucfirst($feature),
                        'feature' => $feature,
                        'action' => $action,
                    ]
                );
                $allPermissionIds[] = $permission->id;
            }
        }

        // 3. Assign All Permissions to Admin
        $adminRole->permissions()->sync($allPermissionIds);

        // 4. Assign Limited Permissions to Kasir (Contoh)
        $kasirPermissions = Permission::whereIn('feature', ['penjualan', 'pelanggan', 'barang'])
            ->whereIn('action', ['view', 'create'])
            ->pluck('id');
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

        $kasirUser = User::where('level', 'kasir')->first();
        if ($kasirUser) {
            $kasirUser->update(['role_id' => $kasirRole->id]);
        }
    }
}
