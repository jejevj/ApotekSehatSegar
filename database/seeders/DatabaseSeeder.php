<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions global
        $this->call(RolePermissionSeeder::class);

        // 2. Menus global
        $this->call(MenuSeeder::class);

        // 3. Buat toko default "Toko Utama"
        $store = Store::updateOrCreate(
            ['slug' => 'toko-utama'],
            [
                'name'      => 'Toko Utama',
                'address'   => 'Jl. Raya Utama No. 1',
                'phone'     => '021-0000000',
                'is_active' => true,
            ]
        );

        // 4. Super Admin (tidak terikat toko)
        $superAdminRole = Role::withoutGlobalScope(TenantScope::class)
            ->where('slug', 'super_admin')
            ->whereNull('store_id')
            ->first();

        User::withoutGlobalScopes()->updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nama'     => 'Super Administrator',
                'password' => Hash::make('superadmin123'),
                'level'    => 'admin',
                'store_id' => null,
                'role_id'  => $superAdminRole?->id,
                'foto'     => null,
            ]
        );

        // 5. Admin toko (terikat ke Toko Utama)
        $adminRole = Role::withoutGlobalScope(TenantScope::class)
            ->where('slug', 'admin')
            ->whereNull('store_id')
            ->first();

        User::withoutGlobalScopes()->updateOrCreate(
            ['username' => 'admin'],
            [
                'nama'     => 'Administrator',
                'password' => Hash::make('admin123'),
                'level'    => 'admin',
                'store_id' => $store->id,
                'role_id'  => $adminRole?->id,
                'foto'     => null,
            ]
        );

        // 6. Kasir toko (terikat ke Toko Utama)
        $kasirRole = Role::withoutGlobalScope(TenantScope::class)
            ->where('slug', 'kasir')
            ->whereNull('store_id')
            ->first();

        User::withoutGlobalScopes()->updateOrCreate(
            ['username' => 'kasir'],
            [
                'nama'     => 'Kasir',
                'password' => Hash::make('kasir123'),
                'level'    => 'kasir',
                'store_id' => $store->id,
                'role_id'  => $kasirRole?->id,
                'foto'     => null,
            ]
        );

        // 7. Business config & setting default untuk Toko Utama
        $this->call(BusinessConfigSeeder::class);
        $this->call(SettingSeeder::class);
    }
}
