<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'icon', 'url', 'route_name', 'permission_slug', 'target', 'order', 'parent_id'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_menus');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
}
