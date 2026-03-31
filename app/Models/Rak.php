<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasTenantScope;

    protected $table = 'tb_rak';
    protected $fillable = ['store_id', 'nama_lokasi', 'nama_rak'];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'rak_id');
    }

    public function getFullLocationAttribute()
    {
        return "{$this->nama_lokasi} - {$this->nama_rak}";
    }
}
