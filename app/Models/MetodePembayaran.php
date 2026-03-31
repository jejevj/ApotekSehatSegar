<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    use HasTenantScope;

    protected $fillable = [
        'store_id',
        'nama',
        'kode',
        'tipe',
        'nama_bank',
        'no_rekening',
        'is_aktif',
    ];
}

