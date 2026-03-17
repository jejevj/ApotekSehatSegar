<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'tb_penjualan';
    public $timestamps = false;

    protected $fillable = [
        'kode_penjualan',
        'kode_barcode',
        'jumlah',
        'total',
        'tgl_penjualan',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barcode', 'kode_barcode');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
