<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembelian_id',
        'kode_barcode',
        'nama_barang',
        'harga_beli',
        'jumlah',
        'subtotal',
    ];

    protected $casts = [
        'harga_beli' => 'integer',
        'jumlah' => 'integer',
        'subtotal' => 'integer',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barcode', 'kode_barcode');
    }
}
