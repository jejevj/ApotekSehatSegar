<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use App\Services\StoreContext;

trait HasTenantScope
{
    public static function bootHasTenantScope(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (empty($model->store_id)) {
                $model->store_id = StoreContext::getStoreId();
            }
        });
    }
}
