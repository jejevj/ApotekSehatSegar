<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasTenantScope;

    protected $table = 'settings';
    protected $fillable = [
        'store_id',
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
