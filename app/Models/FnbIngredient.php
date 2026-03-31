<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class FnbIngredient extends Model
{
    use HasTenantScope;

    protected $table = 'fnb_ingredients';

    protected $fillable = [
        'store_id', 'nama', 'unit_id', 'harga_beli',
        'stok', 'stok_minimum', 'keterangan',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function recipeItems()
    {
        return $this->hasMany(FnbRecipeItem::class, 'ingredient_id');
    }

    public function stockLogs()
    {
        return $this->hasMany(FnbIngredientStockLog::class, 'ingredient_id');
    }

    public function isLowStock(): bool
    {
        return $this->stok < $this->stok_minimum;
    }
}
