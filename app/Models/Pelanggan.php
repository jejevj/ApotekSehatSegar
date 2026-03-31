<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasTenantScope;

    protected $table = 'tb_pelanggan';
    public $timestamps = false;
    protected $primaryKey = 'kode_pelanggan';

    protected $fillable = [
        'store_id',
        'nama',
        'tipe',
        'alamat',
        'telpon',
        'email',
    ];
}
