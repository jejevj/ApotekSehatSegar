<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $table = 'stock_opnames';

    protected $fillable = [
        'kode_opname',
        'tanggal',
        'status',
        'catatan',
        'user_id',
        'approved_by',
        'approved_at',
        'finished_at',
        'total_nilai_rugi',
        'total_nilai_lebih',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'approved_at' => 'datetime',
        'finished_at' => 'datetime',
        'total_nilai_rugi' => 'integer',
        'total_nilai_lebih' => 'integer',
    ];

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class, 'stock_opname_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
