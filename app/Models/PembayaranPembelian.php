<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPembelian extends Model
{
    use HasFactory, HasTenantScope;

    protected $fillable = [
        'store_id',
        'pembelian_id',
        'tanggal_bayar',
        'nominal',
        'metode_pembayaran',
        'bukti_bayar',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal' => 'integer',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
