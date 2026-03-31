<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessConfig;

class BusinessConfigSeeder extends Seeder
{
    /**
     * Seed default business config (general preset, setup not complete).
     * Reads APP_BUSINESS_TYPE env to set initial business_type.
     */
    public function run(): void
    {
        $businessType = env('APP_BUSINESS_TYPE', 'general');
        $validTypes = ['apotek', 'retail', 'fnb', 'general'];
        if (!in_array($businessType, $validTypes)) {
            $businessType = 'general';
        }

        $preset = config("business_presets.{$businessType}", config('business_presets.general'));

        $defaults = array_merge($preset, [
            'business_type'     => $businessType,
            'is_setup_complete' => 'false',
            'low_stock_threshold' => '10',
            'receipt_footer'    => 'Terima kasih atas kunjungan Anda',
        ]);

        foreach ($defaults as $key => $value) {
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
