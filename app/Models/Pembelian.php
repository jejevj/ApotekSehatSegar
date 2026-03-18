<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_faktur',
        'tanggal',
        'distributor_id',
        'total',
        'dibayar',
        'sisa',
        'status',
        'bukti_nota',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'integer',
        'dibayar' => 'integer',
        'sisa' => 'integer',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(PembayaranPembelian::class);
    }
}
