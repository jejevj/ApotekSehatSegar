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
        'harga_jual_kustom',
        'user_id_pengubah',
        'jumlah',
        'total',
        'tgl_penjualan',
        'id_pelanggan',
        'id_user',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barcode', 'kode_barcode');
    }

    public function pengubah()
    {
        return $this->belongsTo(User::class, 'user_id_pengubah');
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
