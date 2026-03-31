<?php

namespace App\Services;

use App\Models\BillingSetting;
use App\Models\BusinessConfig;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreProvisioningService
{
    public function provision(Store $store, array $adminData, string $businessType): User
    {
        return DB::transaction(function () use ($store, $adminData, $businessType) {
            $this->createDefaultSetting($store);
            $this->createDefaultBilling($store);
            $this->seedBusinessConfig($store, $businessType);
            $roles = $this->cloneDefaultRoles($store);
            return $this->createStoreAdmin($store, $adminData, $roles);
        });
    }

    private function createDefaultSetting(Store $store): void
    {
        Setting::withoutGlobalScopes()->updateOrCreate(
            ['store_id' => $store->id],
            [
                'nama_aplikasi' => $store->name,
                'nama_pemilik'  => $store->owner_name ?? '',
                'alamat'        => $store->address ?? '',
                'telepon'       => $store->phone ?? '',
            ]
        );
    }

    private function createDefaultBilling(Store $store): void
    {
        BillingSetting::withoutGlobalScopes()->firstOrCreate(
            ['store_id' => $store->id],
            [
                'expired_at'     => now()->addDays(30),
                'jumlah_tagihan' => 0,
                'is_active'      => true,
                'status'         => 'aktif',
            ]
        );
    }

    private function seedBusinessConfig(Store $store, string $businessType): void
    {
        $preset = config("business_presets.{$businessType}", config('business_presets.general', []));
        $preset['business_type'] = $businessType;
        $preset['is_setup_complete'] = false;

        foreach ($preset as $key => $value) {
            $stringValue = is_bool($value) ? ($value ? '1' : '0') : (string) $value;
            $type = is_bool($value) ? 'boolean' : 'string';

            BusinessConfig::withoutGlobalScopes()->updateOrCreate(
                ['store_id' => $store->id, 'key' => $key],
                ['value' => $stringValue, 'type' => $type]
            );
        }
    }

    private function cloneDefaultRoles(Store $store): array
    {
        // Ambil role template global (store_id = null, bukan super_admin)
        $templates = Role::withoutGlobalScopes()
            ->whereNull('store_id')
            ->where('slug', '!=', 'super_admin')
            ->with(['permissions', 'menus'])
            ->get();

        $clonedRoles = [];
        foreach ($templates as $template) {
            $newRole = Role::withoutGlobalScopes()->updateOrCreate(
                ['store_id' => $store->id, 'slug' => $template->slug],
                ['name' => $template->name]
            );
            $newRole->permissions()->sync($template->permissions->pluck('id'));
            // Sync menu dari template global agar sidebar langsung muncul
            $newRole->menus()->sync($template->menus->pluck('id'));
            $clonedRoles[$template->slug] = $newRole;
        }

        return $clonedRoles;
    }

    private function createStoreAdmin(Store $store, array $adminData, array $roles): User
    {
        $adminRole = $roles['admin'] ?? array_values($roles)[0] ?? null;

        return User::create([
            'store_id' => $store->id,
            'username' => $adminData['username'],
            'nama'     => $adminData['nama'],
            'password' => Hash::make($adminData['password']),
            'role_id'  => $adminRole?->id,
            'level'    => 'admin',
        ]);
    }
}
