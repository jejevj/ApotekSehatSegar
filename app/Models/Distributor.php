<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'store_id',
        'nama',
        'alamat',
        'telepon',
        'keterangan',
    ];

    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
}
