<?php

namespace App\Models;

use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class FnbRecipe extends Model
{
    use HasTenantScope;

    protected $table = 'fnb_recipes';

    protected $fillable = [
        'store_id', 'menu_id', 'nama_resep', 'porsi',
        'keterangan', 'hpp_per_porsi', 'hpp_outdated',
    ];

    protected $casts = [
        'hpp_outdated' => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(Barang::class, 'menu_id', 'kode_barcode');
    }

    public function items()
    {
        return $this->hasMany(FnbRecipeItem::class, 'recipe_id');
    }
}
