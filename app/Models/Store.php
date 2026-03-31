<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Store extends Model
{
    protected $fillable = [
        'name', 'slug', 'business_type', 'is_active',
        'owner_name', 'phone', 'address',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function users(): HasMany { return $this->hasMany(User::class); }
    public function setting(): HasOne { return $this->hasOne(Setting::class); }
    public function billingSettings(): HasMany { return $this->hasMany(BillingSetting::class); }
    public function businessConfigs(): HasMany { return $this->hasMany(BusinessConfig::class); }
    public function roles(): HasMany { return $this->hasMany(Role::class); }
    public function activeBilling(): HasOne {
        return $this->hasOne(BillingSetting::class)->where('status', 'aktif')->latestOfMany();
    }
}
