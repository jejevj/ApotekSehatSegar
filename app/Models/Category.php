<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'nama_kategori',
        'slug',
        'keterangan',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'category_id', 'id');
    }
}
