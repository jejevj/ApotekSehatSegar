<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Menu;
use App\Models\Role;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Beranda',
                'icon' => 'home',
                'url' => '/',
                'order' => 1,
            ],
            [
                'name' => 'Barang',
                'icon' => 'view_module',
                'route_name' => 'barang.index',
                'permission_slug' => 'barang.view',
                'order' => 2,
            ],
            [
                'name' => 'Satuan Barang',
                'icon' => 'straighten',
                'route_name' => 'unit.index',
                'permission_slug' => 'unit.view',
                'order' => 3,
            ],
            [
                'name' => 'Penjualan',
                'icon' => 'add_shopping_cart',
                'route_name' => 'penjualan.index',
                'permission_slug' => 'penjualan.view',
                'order' => 4,
            ],
            [
                'name' => 'Pengguna',
                'icon' => 'person',
                'route_name' => 'pengguna.index',
                'permission_slug' => 'pengguna.view',
                'order' => 5,
            ],
            [
                'name' => 'Role Management',
                'icon' => 'security',
                'route_name' => 'role.index',
                'permission_slug' => 'role.view',
                'order' => 6,
            ],
            [
                'name' => 'Menu Management',
                'icon' => 'menu',
                'route_name' => 'menu.index',
                'permission_slug' => 'menu.view',
                'order' => 7,
            ],
            [
                'name' => 'Billing Settings',
                'icon' => 'credit_card',
                'route_name' => 'billing.index',
                'permission_slug' => 'billing.manage',
                'order' => 8,
            ],
            [
                'name' => 'Setting Aplikasi',
                'icon' => 'settings',
                'route_name' => 'setting.index',
                'permission_slug' => 'setting.view',
                'order' => 9,
            ],
            [
                'name' => 'Laporan Penjualan',
                'icon' => 'book',
                'target' => '#smallModal',
                'permission_slug' => 'laporan.view',
                'order' => 10,
            ],
        ];

        foreach ($menus as $menuData) {
            Menu::updateOrCreate(['name' => $menuData['name']], $menuData);
        }

        // Assign menus to roles
        $superAdminRole = Role::where('slug', 'super_admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $kasirRole = Role::where('slug', 'kasir')->first();

        if ($superAdminRole) {
            $superAdminRole->menus()->sync(Menu::pluck('id'));
        }

        if ($adminRole) {
            $adminRole->menus()->sync(Menu::pluck('id'));
        }

        if ($kasirRole) {
            $kasirMenus = Menu::whereIn('name', ['Beranda', 'Barang', 'Penjualan'])->pluck('id');
            $kasirRole->menus()->sync($kasirMenus);
        }

        // Auto sync menus for all other roles based on their permissions (prevents empty sidebar)
        $allMenus = Menu::all();
        foreach (Role::with('permissions')->get() as $role) {
            if (in_array($role->slug, ['super_admin', 'admin', 'kasir'], true)) {
                continue;
            }
            $permissionSlugs = $role->permissions->pluck('slug')->unique()->values();
            $menuIds = $allMenus
                ->filter(function ($menu) use ($permissionSlugs) {
                    return empty($menu->permission_slug) || $permissionSlugs->contains($menu->permission_slug);
                })
                ->pluck('id')
                ->values()
                ->all();
            $role->menus()->sync($menuIds);
        }
    }
}
