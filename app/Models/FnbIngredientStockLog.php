<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class FnbIngredientStockLog extends Model
{
    use HasTenantScope;

    protected $table = 'fnb_ingredient_stock_logs';

    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'ingredient_id', 'store_id', 'qty_change',
        'type', 'reference_id', 'reference_type', 'keterangan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function ingredient()
    {
        return $this->belongsTo(FnbIngredient::class, 'ingredient_id');
    }
}
