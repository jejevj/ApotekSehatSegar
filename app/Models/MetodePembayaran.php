<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'tipe',
        'nama_bank',
        'no_rekening',
        'is_aktif',
    ];
}

