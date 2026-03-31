<?php

namespace App\Models\Scopes;

use App\Services\StoreContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $storeId = StoreContext::getStoreId();
        if ($storeId === null) {
            return; // super_admin bypass
        }
        $builder->where($model->getTable() . '.store_id', $storeId);
    }
}
