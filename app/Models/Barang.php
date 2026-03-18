<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'tb_barang';
    public $timestamps = false;
    protected $primaryKey = 'kode_barcode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_barcode',
        'nama_barang',
        'unit_id',
        'lokasi_id',
        'rak_id',
        'isi',
        'satuan',
        'harga_beli',
        'stok',
        'harga_jual',
        'profit',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id')->withDefault([
            'nama_lokasi' => 'Belum Terorganisir'
        ]);
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id')->withDefault([
            'nama_rak' => 'Belum Terorganisir'
        ]);
    }
}
