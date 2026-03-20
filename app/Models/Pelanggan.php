<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'tb_pelanggan';
    public $timestamps = false;
    protected $primaryKey = 'kode_pelanggan';

    protected $fillable = [
        'nama',
        'tipe',
        'alamat',
        'telpon',
        'email',
    ];
}
