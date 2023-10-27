<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionGroupOperand extends Model
{
    protected $appends = array('valuegroup_field', 'valuegroup_table', 'valuegroup_const');
    
    public function getValuegroupFieldAttribute()
    {
        if($this->attributes['field_type_id'] == 1)
        {
            return $this->attributes['field_content'];
        }
        else
        {
            return "";
        }
    }
    
    public function getValuegroupTableAttribute()
    {
        if($this->attributes['field_type_id'] == 2)
        {
            return $this->attributes['field_content'];
        }
        else
        {
            return "";
        }
    }
    
    public function getValuegroupConstAttribute()
    {
        if($this->attributes['field_type_id'] == 3)
        {
            return $this->attributes['field_content'];
        }
        else
        {
            return "";
        }
    }
}
