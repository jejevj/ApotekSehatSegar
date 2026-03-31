<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasTenantScope;

    protected $table = 'stock_opname_items';

    protected $fillable = [
        'store_id',
        'stock_opname_id',
        'kode_barcode',
        'nama_barang',
        'harga_beli',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'nilai_selisih',
    ];

    protected $casts = [
        'harga_beli' => 'integer',
        'stok_sistem' => 'integer',
        'stok_fisik' => 'integer',
        'selisih' => 'integer',
        'nilai_selisih' => 'integer',
    ];

    public function opname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barcode', 'kode_barcode');
    }
}
