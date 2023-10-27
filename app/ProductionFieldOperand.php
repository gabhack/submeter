<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionFieldOperand extends Model
{
    protected $appends = array('value_field', 'value_table', 'value_const');
    
    public function getValueFieldAttribute()
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
    
    public function getValueTableAttribute()
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
    
    public function getValueConstAttribute()
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
