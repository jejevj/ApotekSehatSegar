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
                'name' => 'Penjualan',
                'icon' => 'add_shopping_cart',
                'route_name' => 'penjualan.index',
                'permission_slug' => 'penjualan.view',
                'order' => 3,
            ],
            [
                'name' => 'Pengguna',
                'icon' => 'person',
                'route_name' => 'pengguna.index',
                'permission_slug' => 'pengguna.view',
                'order' => 4,
            ],
            [
                'name' => 'Role Management',
                'icon' => 'security',
                'route_name' => 'role.index',
                'permission_slug' => 'role.view',
                'order' => 5,
            ],
            [
                'name' => 'Menu Management',
                'icon' => 'menu',
                'route_name' => 'menu.index',
                'permission_slug' => 'menu.view',
                'order' => 6,
            ],
            [
                'name' => 'Setting Aplikasi',
                'icon' => 'settings',
                'route_name' => 'setting.index',
                'permission_slug' => 'setting.view',
                'order' => 7,
            ],
            [
                'name' => 'Laporan Penjualan',
                'icon' => 'book',
                'target' => '#smallModal',
                'permission_slug' => 'laporan.view',
                'order' => 8,
            ],
        ];

        foreach ($menus as $menuData) {
            Menu::updateOrCreate(['name' => $menuData['name']], $menuData);
        }

        // Assign menus to roles
        $adminRole = Role::where('slug', 'admin')->first();
        $kasirRole = Role::where('slug', 'kasir')->first();

        if ($adminRole) {
            $adminRole->menus()->sync(Menu::pluck('id'));
        }

        if ($kasirRole) {
            $kasirMenus = Menu::whereIn('name', ['Beranda', 'Barang', 'Penjualan'])->pluck('id');
            $kasirRole->menus()->sync($kasirMenus);
        }
    }
}
