<?php

namespace App\Services;

use App\Models\BusinessConfig;
use App\Services\StoreContext;
use Illuminate\Support\Facades\Cache;

class BusinessConfigService
{
    private const CACHE_TTL = 3600;

    private function getCacheKey(): string
    {
        $storeId = StoreContext::getStoreId();
        return $storeId !== null ? "business_config_{$storeId}" : 'business_config_global';
    }

    /**
     * Default hardcoded fallbacks when key is not in DB or preset.
     */
    private array $defaults = [
        'business_type'        => 'general',
        'is_setup_complete'    => false,
        'trx_prefix'           => 'TRX',
        'show_product_location'=> true,
        'show_product_content' => false,
        'label_product'        => 'Produk',
        'label_location'       => 'Lokasi',
        'label_supplier'       => 'Supplier',
        'label_customer'       => 'Pelanggan',
        'label_purchase'       => 'Pembelian',
        'label_category'       => 'Kategori',
        'label_unit'           => 'Satuan',
    ];

    /**
     * Entity to config key mapping for getLabel().
     */
    private array $labelKeys = [
        'product'  => 'label_product',
        'location' => 'label_location',
        'supplier' => 'label_supplier',
        'customer' => 'label_customer',
        'purchase' => 'label_purchase',
        'category' => 'label_category',
        'unit'     => 'label_unit',
    ];

    /**
     * Get a config value. Checks cache/DB first, then 'general' preset, then hardcoded defaults.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->getAllCached();

        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        // Fallback to 'general' preset from config file
        $generalPreset = config('business_presets.general', []);
        if (array_key_exists($key, $generalPreset)) {
            return $generalPreset[$key];
        }

        // Fallback to hardcoded defaults
        if (array_key_exists($key, $this->defaults)) {
            return $this->defaults[$key];
        }

        return $default;
    }

    /**
     * Set a config value in DB and invalidate cache.
     */
    public function set(string $key, mixed $value): void
    {
        BusinessConfig::setValue($key, $value);
        Cache::forget($this->getCacheKey());
    }

    /**
     * Get the label for a given entity (product, location, supplier, etc.).
     */
    public function getLabel(string $entity): string
    {
        $configKey = $this->labelKeys[$entity] ?? null;

        if ($configKey !== null) {
            $value = $this->get($configKey);
            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        // Generic fallback for unknown entities
        return ucfirst($entity);
    }

    /**
     * Return all config as an associative array.
     */
    public function getAll(): array
    {
        return $this->getAllCached();
    }

    /**
     * Return the business_type value.
     */
    public function getBusinessType(): string
    {
        return (string) $this->get('business_type', 'general');
    }

    /**
     * Return whether setup is complete.
     */
    public function isSetupComplete(): bool
    {
        $value = $this->get('is_setup_complete', false);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Return the transaction prefix.
     */
    public function getTransactionPrefix(): string
    {
        return (string) $this->get('trx_prefix', 'TRX');
    }

    /**
     * Return whether to show product location field.
     */
    public function showProductLocation(): bool
    {
        $value = $this->get('show_product_location', true);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Return whether to show product content/unit field.
     */
    public function showProductContent(): bool
    {
        $value = $this->get('show_product_content', false);
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Load all configs from cache or DB.
     */
    private function getAllCached(): array
    {
        return Cache::remember($this->getCacheKey(), self::CACHE_TTL, function () {
            $storeId = StoreContext::getStoreId();

            $query = BusinessConfig::query();
            if ($storeId !== null) {
                $query->where('store_id', $storeId);
            }

            $rows = $query->get(['key', 'value', 'type']);
            $result = [];

            foreach ($rows as $row) {
                $result[$row->key] = match ($row->type) {
                    'boolean' => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $row->value,
                    'json'    => json_decode($row->value, true),
                    default   => $row->value,
                };
            }

            return $result;
        });
    }
}
