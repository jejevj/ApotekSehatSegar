<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasTenantScope;

    protected $fillable = [
        'store_id',
        'nama_kategori',
        'slug',
        'keterangan',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'category_id', 'id');
    }
}
