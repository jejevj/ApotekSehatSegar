<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    use HasTenantScope;

    protected $fillable = ['store_id', 'name', 'slug'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menus')->orderBy('order');
    }
}
