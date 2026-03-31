<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasTenantScope;

    protected $table = 'units';

    protected $fillable = [
        'store_id',
        'nama',
    ];
}
