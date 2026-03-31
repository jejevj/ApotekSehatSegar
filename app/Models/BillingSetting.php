<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class BillingSetting extends Model
{
    use HasTenantScope;

    protected $table = 'billing_settings';

    protected $fillable = [
        'store_id',
        'expired_at',
        'jumlah_tagihan',
        'nama_bank',
        'no_rek',
        'is_active',
        'status',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'jumlah_tagihan' => 'integer',
        'is_active' => 'boolean',
    ];
}
