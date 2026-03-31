<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessConfig;

class FnbPresetSeeder extends Seeder
{
    /**
     * Seed F&B-specific business config.
     * Run with: php artisan db:seed --class=FnbPresetSeeder
     */
    public function run(): void
    {
        $preset = config('business_presets.fnb');

        $data = array_merge($preset, [
            'business_type'       => 'fnb',
            'is_setup_complete'   => 'true',
            'low_stock_threshold' => '3',
            'receipt_footer'      => 'Selamat menikmati, sampai jumpa lagi!',
        ]);

        foreach ($data as $key => $value) {
            BusinessConfig::updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value,
                    'type'  => $this->resolveType($value),
                ]
            );
        }
    }

    private function resolveType(mixed $value): string
    {
        if (is_bool($value) || in_array($value, ['true', 'false'], true)) {
            return 'boolean';
        }
        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            return 'integer';
        }
        return 'string';
    }
}
