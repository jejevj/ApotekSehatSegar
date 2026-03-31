<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasTenantScope;

    protected $table = 'tb_barang';
    public $timestamps = false;
    protected $primaryKey = 'kode_barcode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'store_id',
        'kode_barcode',
        'nama_barang',
        'category_id',
        'unit_id',
        'rak_id',
        'isi',
        'satuan',
        'harga_beli',
        'stok',
        'harga_jual',
        'profit',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault([
            'nama_kategori' => 'Belum Memiliki Kategori'
        ]);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id')->withDefault([
            'nama_lokasi' => 'Belum',
            'nama_rak' => 'Terorganisir'
        ]);
    }
}
