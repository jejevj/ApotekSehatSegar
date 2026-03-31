<?php

use App\Services\BusinessConfigService;

if (!function_exists('label')) {
    function label(string $entity, bool $plural = false): string
    {
        return app(BusinessConfigService::class)->getLabel($entity);
    }
}

if (!function_exists('business_config')) {
    function business_config(string $key, mixed $default = null): mixed
    {
        return app(BusinessConfigService::class)->get($key, $default);
    }
}
