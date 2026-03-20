<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CategoryFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permissions for Category
        $actions = ['view', 'create', 'update', 'delete'];
        $feature = 'category';
        $categoryPermissionIds = [];

        foreach ($actions as $action) {
            $permission = Permission::updateOrCreate(
                ['slug' => $feature . '.' . $action],
                [
                    'name' => ucfirst($action) . ' Kategori',
                    'feature' => $feature,
                    'action' => $action,
                ]
            );
            $categoryPermissionIds[] = $permission->id;
        }

        // 2. Assign Permissions to Roles
        $superAdmin = Role::where('slug', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->syncWithoutDetaching($categoryPermissionIds);
        }

        $admin = Role::where('slug', 'admin')->first();
        if ($admin) {
            $admin->permissions()->syncWithoutDetaching($categoryPermissionIds);
        }

        // 3. Create Menu for Category
        $menuExists = Menu::where('route_name', 'category.index')->exists();

        if (!$menuExists) {
            // Get the order for the new menu. Let's put it after "Satuan Barang" (order 4).
            // First, shift the order of menus starting from order 5.
            Menu::where('order', '>=', 5)->increment('order');
        }

        $categoryMenu = Menu::updateOrCreate(
            ['route_name' => 'category.index'],
            [
                'name' => 'Kategori Barang',
                'icon' => 'category',
                'permission_slug' => 'category.view',
                'order' => 5,
            ]
        );

        // 4. Assign Menu to Roles
        if ($superAdmin) {
            $superAdmin->menus()->syncWithoutDetaching([$categoryMenu->id]);
        }

        if ($admin) {
            $admin->menus()->syncWithoutDetaching([$categoryMenu->id]);
        }
    }
}
