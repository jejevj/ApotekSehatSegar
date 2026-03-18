<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    protected $table = 'tb_rak';
    protected $fillable = ['nama_rak'];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'rak_id');
    }
}
