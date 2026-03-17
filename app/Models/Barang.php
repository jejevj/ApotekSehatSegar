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
        'satuan',
        'harga_beli',
        'stok',
        'harga_jual',
        'profit',
    ];
}
