<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    protected $appends = ['menus'];
    
    public function getMenusAttribute()
    {
        $groups = unserialize($this->opciones);
        return $groups;
    }
}
