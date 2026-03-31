<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FnbRecipeItem extends Model
{
    public $timestamps = false;

    protected $table = 'fnb_recipe_items';

    protected $fillable = [
        'recipe_id', 'ingredient_id', 'qty', 'unit_id', 'keterangan',
    ];

    public function recipe()
    {
        return $this->belongsTo(FnbRecipe::class, 'recipe_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(FnbIngredient::class, 'ingredient_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
