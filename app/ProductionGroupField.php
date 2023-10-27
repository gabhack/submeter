<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionGroupField extends Model
{
    public function operation()
    {
        return $this->hasOne("App\GroupOperation", "id", "operation_id");
    }
    
    public function production_type()
    {
        return $this->hasOne("App\ProductionType", "id", "production_type_id");
    }
    
    public function operands()
    {
        return $this->hasMany("App\ProductionGroupOperand", "production_group_field_id", "id");
    }
}
