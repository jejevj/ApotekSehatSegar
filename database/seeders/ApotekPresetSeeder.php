<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessConfig;

class ApotekPresetSeeder extends Seeder
{
    /**
     * Seed apotek-specific business config.
     * Run with: php artisan db:seed --class=ApotekPresetSeeder
     */
    public function run(): void
    {
        $preset = config('business_presets.apotek');

        $data = array_merge($preset, [
            'business_type'       => 'apotek',
            'is_setup_complete'   => 'true',
            'low_stock_threshold' => '10',
            'receipt_footer'      => 'Barang yang sudah dibeli tidak dapat dikembalikan',
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
