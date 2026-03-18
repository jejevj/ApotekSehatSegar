<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'tb_lokasi';
    protected $fillable = ['nama_lokasi'];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'lokasi_id');
    }
}
