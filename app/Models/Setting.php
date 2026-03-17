<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = [
        'nama_aplikasi',
        'nama_pemilik',
        'alamat',
        'telepon',
        'email',
        'logo',
        'favicon',
        'footer_text'
    ];
}
